const path = require("path");
const fs = require("fs");
const express = require("express");
const Database = require("better-sqlite3");
const QRCode = require("qrcode");

const app = express();
const PORT = process.env.PORT || 3000;
const BASE_URL = process.env.BASE_URL || `http://localhost:${PORT}`;
const distDir = path.join(__dirname, "dist");

const dataDir = path.join(__dirname, "data");
if (!fs.existsSync(dataDir)) {
    fs.mkdirSync(dataDir, { recursive: true });
}

const db = new Database(path.join(dataDir, "qrcompact.db"));
db.exec(`
  CREATE TABLE IF NOT EXISTS links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    original_url TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
  );
`);

app.use(express.json());
if (fs.existsSync(distDir)) {
    app.use(express.static(distDir));
}
app.use(express.static(path.join(__dirname, "public")));

const insertLink = db.prepare(
    "INSERT INTO links (slug, original_url) VALUES (?, ?)"
);
const getBySlug = db.prepare("SELECT slug, original_url FROM links WHERE slug = ?");
const updateBySlug = db.prepare("UPDATE links SET original_url = ? WHERE slug = ?");
const listLinks = db.prepare(
    "SELECT slug, original_url, created_at FROM links ORDER BY id DESC LIMIT 100"
);

function generateSlug(length = 6) {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    let slug = "";
    for (let i = 0; i < length; i += 1) {
        slug += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return slug;
}

function isValidHttpUrl(url) {
    try {
        const parsed = new URL(url);
        return parsed.protocol === "http:" || parsed.protocol === "https:";
    } catch {
        return false;
    }
}

app.post("/api/shorten", async (req, res) => {
    const { url } = req.body;

    if (!url || !isValidHttpUrl(url)) {
        return res.status(400).json({ error: "URL invalida. Use http:// ou https://" });
    }

    let slug;
    let tries = 0;
    do {
        slug = generateSlug(6);
        tries += 1;
    } while (getBySlug.get(slug) && tries < 10);

    if (getBySlug.get(slug)) {
        return res.status(500).json({ error: "Falha ao gerar slug unico" });
    }

    try {
        insertLink.run(slug, url);
        const shortUrl = `${BASE_URL}/${slug}`;
        const qrCodeDataUrl = await QRCode.toDataURL(shortUrl, {
            errorCorrectionLevel: "M",
            margin: 1,
            width: 260
        });

        return res.json({ slug, shortUrl, originalUrl: url, qrCodeDataUrl });
    } catch (error) {
        if (error && error.code === "SQLITE_CONSTRAINT_UNIQUE") {
            return res.status(409).json({ error: "Slug em conflito, tente novamente" });
        }
        return res.status(500).json({ error: "Erro ao salvar link" });
    }
});

app.get("/api/links", (req, res) => {
    const rows = listLinks.all().map((item) => ({
        ...item,
        shortUrl: `${BASE_URL}/${item.slug}`
    }));

    return res.json(rows);
});

app.get("/api/links/:slug", async (req, res) => {
    const { slug } = req.params;
    const row = getBySlug.get(slug);

    if (!row) {
        return res.status(404).json({ error: "Link nao encontrado" });
    }

    const shortUrl = `${BASE_URL}/${row.slug}`;
    const qrCodeDataUrl = await QRCode.toDataURL(shortUrl, {
        errorCorrectionLevel: "M",
        margin: 1,
        width: 260
    });

    return res.json({
        slug: row.slug,
        originalUrl: row.original_url,
        shortUrl,
        qrCodeDataUrl
    });
});

app.put("/api/links/:slug", (req, res) => {
    const { slug } = req.params;
    const { url } = req.body;

    if (!url || !isValidHttpUrl(url)) {
        return res.status(400).json({ error: "URL invalida. Use http:// ou https://" });
    }

    const existing = getBySlug.get(slug);
    if (!existing) {
        return res.status(404).json({ error: "Link nao encontrado" });
    }

    updateBySlug.run(url, slug);

    return res.json({
        slug,
        originalUrl: url,
        shortUrl: `${BASE_URL}/${slug}`
    });
});

app.post("/api/qr", async (req, res) => {
    const { data } = req.body;

    if (!data || typeof data !== "string" || !data.trim()) {
        return res.status(400).json({ error: "Dados invalidos" });
    }

    try {
        const qrCodeDataUrl = await QRCode.toDataURL(data.trim(), {
            errorCorrectionLevel: "M",
            margin: 1,
            width: 300
        });
        return res.json({ qrCodeDataUrl });
    } catch {
        return res.status(500).json({ error: "Erro ao gerar QR Code" });
    }
});

app.get("/:slug", (req, res, next) => {
    const { slug } = req.params;
    if (slug === "api" || slug.includes(".")) {
        return next();
    }

    const row = getBySlug.get(slug);
    if (!row) {
        return res.status(404).send("Link curto nao encontrado");
    }

    return res.redirect(302, row.original_url);
});

app.listen(PORT, () => {
    console.log(`qrcompact rodando em ${BASE_URL}`);
});
