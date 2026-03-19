import { useEffect, useMemo, useRef, useState } from "react";

function compactUrlText(url, maxLength = 42) {
    if (!url) {
        return "";
    }

    let readableUrl = url;
    try {
        const parsed = new URL(url);
        const pathname = parsed.pathname === "/" ? "" : parsed.pathname;
        readableUrl = `${parsed.host}${pathname}${parsed.search}${parsed.hash}`;
    } catch {
        readableUrl = url.replace(/^https?:\/\//i, "").replace(/^www\./i, "");
    }

    if (readableUrl.length <= maxLength) {
        return readableUrl;
    }

    return `${readableUrl.slice(0, maxLength - 3)}...`;
}

function App() {
    const [url, setUrl] = useState("");
    const [feedback, setFeedback] = useState({ message: "", isError: false });
    const [result, setResult] = useState(null);
    const [links, setLinks] = useState([]);
    const [loadingLinks, setLoadingLinks] = useState(false);
    const [detail, setDetail] = useState(null);
    const [editing, setEditing] = useState(null);
    const [editUrl, setEditUrl] = useState("");
    const [editFeedback, setEditFeedback] = useState({ message: "", isError: false });
    const editUrlInputRef = useRef(null);

    const hasLinks = useMemo(() => links.length > 0, [links]);

    useEffect(() => {
        function handleEscape(event) {
            if (event.key !== "Escape") {
                return;
            }

            if (editing) {
                setEditing(null);
                return;
            }

            if (detail) {
                setDetail(null);
            }
        }

        document.addEventListener("keydown", handleEscape);
        return () => document.removeEventListener("keydown", handleEscape);
    }, [editing, detail]);

    useEffect(() => {
        if (!editing || !editUrlInputRef.current) {
            return;
        }

        editUrlInputRef.current.focus();
        editUrlInputRef.current.select();
    }, [editing]);

    async function loadLinks() {
        setLoadingLinks(true);
        try {
            const response = await fetch("/api/links");
            const data = await response.json();
            if (!response.ok) {
                setFeedback({ message: data.error || "Erro ao carregar links.", isError: true });
                return;
            }
            setLinks(data);
        } catch {
            setFeedback({ message: "Nao foi possivel carregar os links existentes.", isError: true });
        } finally {
            setLoadingLinks(false);
        }
    }

    useEffect(() => {
        loadLinks();
    }, []);

    async function handleSubmit(event) {
        event.preventDefault();
        const value = url.trim();

        if (!value) {
            setFeedback({ message: "Informe uma URL valida.", isError: true });
            return;
        }

        setFeedback({ message: "Gerando link curto...", isError: false });

        try {
            const response = await fetch("/api/shorten", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ url: value })
            });

            const data = await response.json();

            if (!response.ok) {
                setFeedback({ message: data.error || "Erro ao gerar link.", isError: true });
                return;
            }

            setResult(data);
            setUrl("");
            setFeedback({ message: "Link e QR Code gerados com sucesso.", isError: false });
            await loadLinks();
        } catch {
            setFeedback({ message: "Erro de rede. Tente novamente.", isError: true });
        }
    }

    async function handleViewDetails(slug) {
        try {
            const response = await fetch(`/api/links/${slug}`);
            const data = await response.json();

            if (!response.ok) {
                setFeedback({ message: data.error || "Nao foi possivel carregar os detalhes.", isError: true });
                return;
            }

            setDetail(data);
        } catch {
            setFeedback({ message: "Erro ao carregar os detalhes do link.", isError: true });
        }
    }

    function handleOpenEdit(item) {
        setEditing(item);
        setEditUrl(item.original_url || "");
        setEditFeedback({ message: "", isError: false });
    }

    function isValidHttpUrl(value) {
        try {
            const parsed = new URL(value);
            return parsed.protocol === "http:" || parsed.protocol === "https:";
        } catch {
            return false;
        }
    }

    async function handleEditSubmit(event) {
        event.preventDefault();

        const value = editUrl.trim();
        if (!value || !isValidHttpUrl(value)) {
            setEditFeedback({ message: "Informe uma URL valida com http:// ou https://", isError: true });
            return;
        }

        setEditFeedback({ message: "Salvando alteracoes...", isError: false });

        try {
            const response = await fetch(`/api/links/${editing.slug}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ url: value })
            });
            const data = await response.json();

            if (!response.ok) {
                setEditFeedback({ message: data.error || "Erro ao editar link.", isError: true });
                return;
            }

            setEditing(null);
            setEditUrl("");
            setEditFeedback({ message: "", isError: false });
            setFeedback({ message: "Link atualizado com sucesso.", isError: false });
            await loadLinks();
            if (detail && detail.slug === data.slug) {
                await handleViewDetails(data.slug);
            }
        } catch {
            setEditFeedback({ message: "Erro de rede ao editar link.", isError: true });
        }
    }

    return (
        <>
            <header className="topnav">
                <a href="/" className="brand">
                    QRCompact
                </a>
                <nav className="nav-links">
                    <a href="/" className="nav-active">
                        Links Curtos
                    </a>
                    <a href="/pix.html">Gerar Pix</a>
                </nav>
            </header>

            <main className="layout">
                <section className="hero card">
                    <h1>QRCompact</h1>
                    <p>Crie links curtos com redirecionamento e QR Code em segundos.</p>
                </section>

                <section className="card form-card">
                    <form onSubmit={handleSubmit}>
                        <label htmlFor="url-input">URL de destino</label>
                        <div className="input-row">
                            <input
                                id="url-input"
                                name="url"
                                type="url"
                                placeholder="https://seusite.com/pagina"
                                required
                                value={url}
                                onChange={(event) => setUrl(event.target.value)}
                            />
                            <button type="submit">Gerar</button>
                        </div>
                    </form>

                    <p className={`feedback ${feedback.isError ? "error" : ""}`} aria-live="polite">
                        {feedback.message}
                    </p>

                    {result && (
                        <div className="result">
                            <a href={result.shortUrl} target="_blank" rel="noopener noreferrer">
                                {result.shortUrl}
                            </a>
                            <img src={result.qrCodeDataUrl} alt="QR Code do link curto" id="qr-image" />
                        </div>
                    )}
                </section>

                <section className="card">
                    <h2>Ultimos links</h2>
                    {loadingLinks && <p className="feedback">Carregando...</p>}
                    <ul className="links-list">
                        {!loadingLinks && !hasLinks && <li>Nenhum link criado ainda.</li>}

                        {links.map((item) => (
                            <li key={item.slug}>
                                <div className="link-row-top">
                                    <a
                                        className="short-link"
                                        href={item.shortUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {item.shortUrl}
                                    </a>
                                    <div className="actions">
                                        <button type="button" onClick={() => handleOpenEdit(item)}>
                                            Editar
                                        </button>
                                        <button type="button" onClick={() => handleViewDetails(item.slug)}>
                                            Visualizar
                                        </button>
                                    </div>
                                </div>
                                <span className="target" title={item.original_url}>
                                    Destino: {compactUrlText(item.original_url)}
                                </span>
                            </li>
                        ))}
                    </ul>
                </section>

            </main>

            {detail && (
                <div className="modal" role="dialog" aria-modal="true" aria-labelledby="details-title" onClick={() => setDetail(null)}>
                    <div className="modal-content" onClick={(event) => event.stopPropagation()}>
                        <h3 id="details-title">Detalhes do link</h3>
                        <p>
                            <strong>URL compacta:</strong>{" "}
                            <a href={detail.shortUrl} target="_blank" rel="noopener noreferrer">
                                {detail.shortUrl}
                            </a>
                        </p>
                        <img id="modal-qr-image" src={detail.qrCodeDataUrl} alt="QR Code do link" />
                        <p>
                            <strong>URL original:</strong> <span>{detail.originalUrl}</span>
                        </p>
                        <button type="button" onClick={() => setDetail(null)}>
                            Fechar
                        </button>
                    </div>
                </div>
            )}

            {editing && (
                <div className="modal" role="dialog" aria-modal="true" aria-labelledby="edit-title" onClick={() => setEditing(null)}>
                    <div className="modal-content" onClick={(event) => event.stopPropagation()}>
                        <h3 id="edit-title">Editar link</h3>
                        <p>
                            <strong>Slug:</strong> {editing.slug}
                        </p>
                        <form className="edit-form" onSubmit={handleEditSubmit}>
                            <label htmlFor="edit-url-input">Nova URL de destino</label>
                            <input
                                id="edit-url-input"
                                name="url"
                                type="url"
                                required
                                ref={editUrlInputRef}
                                value={editUrl}
                                onChange={(event) => setEditUrl(event.target.value)}
                            />
                            <p className={`feedback ${editFeedback.isError ? "error" : ""}`} aria-live="polite">
                                {editFeedback.message}
                            </p>
                            <div className="modal-actions">
                                <button type="button" className="btn-ghost" onClick={() => setEditing(null)}>
                                    Cancelar
                                </button>
                                <button type="submit">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </>
    );
}

export default App;
