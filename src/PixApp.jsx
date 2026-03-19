import { useMemo, useState } from "react";

function crc16ccitt(str) {
    let crc = 0xffff;
    for (let i = 0; i < str.length; i += 1) {
        crc ^= str.charCodeAt(i) << 8;
        for (let j = 0; j < 8; j += 1) {
            crc = (crc & 0x8000) ? (((crc << 1) ^ 0x1021) & 0xffff) : ((crc << 1) & 0xffff);
        }
    }
    return crc.toString(16).toUpperCase().padStart(4, "0");
}

function tlv(id, value) {
    return `${id}${String(value.length).padStart(2, "0")}${value}`;
}

function sanitize(str, maxLength) {
    return str
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^A-Za-z0-9 ]/g, " ")
        .replace(/\s+/g, " ")
        .trim()
        .slice(0, maxLength);
}

function formatPixKey(type, raw) {
    const value = raw.trim();
    if (type === "phone") {
        const digits = value.replace(/\D/g, "");
        if (digits.startsWith("55") && digits.length >= 12) {
            return `+${digits}`;
        }
        return `+55${digits}`;
    }
    if (type === "cpf" || type === "cnpj") {
        return value.replace(/\D/g, "");
    }
    if (type === "email") {
        return value.toLowerCase();
    }
    return value;
}

function buildPixString({ keyType, rawKey, name, city, amount, txid }) {
    const key = formatPixKey(keyType, rawKey);
    if (!key) {
        throw new Error("Informe a chave Pix.");
    }

    const nameClean = sanitize(name, 25);
    if (!nameClean) {
        throw new Error("Informe o nome do beneficiario.");
    }

    const cityClean = sanitize(city, 15);
    if (!cityClean) {
        throw new Error("Informe a cidade.");
    }

    const merchantInfo = tlv("26", tlv("00", "BR.GOV.BCB.PIX") + tlv("01", key));

    const amountNum = parseFloat(String(amount || "0").replace(",", ".")) || 0;
    const amountField = amountNum > 0 ? tlv("54", amountNum.toFixed(2)) : "";

    const txidClean = (txid || "").replace(/[^A-Za-z0-9]/g, "").slice(0, 25) || "***";
    const additionalData = tlv("62", tlv("05", txidClean));

    const payload =
        tlv("00", "01") +
        merchantInfo +
        tlv("52", "0000") +
        tlv("53", "986") +
        amountField +
        tlv("58", "BR") +
        tlv("59", nameClean) +
        tlv("60", cityClean) +
        additionalData +
        "6304";

    return payload + crc16ccitt(payload);
}

const KEY_PLACEHOLDERS = {
    phone: "(00) 00000-0000",
    cpf: "000.000.000-00",
    cnpj: "00.000.000/0000-00",
    email: "seu@email.com",
    random: "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
};

function PixApp() {
    const [keyType, setKeyType] = useState("phone");
    const [pixKey, setPixKey] = useState("");
    const [name, setName] = useState("");
    const [city, setCity] = useState("");
    const [amount, setAmount] = useState("");
    const [txid, setTxid] = useState("");
    const [feedback, setFeedback] = useState({ message: "", isError: false });
    const [copyFeedback, setCopyFeedback] = useState("");
    const [qrCodeDataUrl, setQrCodeDataUrl] = useState("");
    const [pixString, setPixString] = useState("");

    const keyPlaceholder = useMemo(() => KEY_PLACEHOLDERS[keyType] || "", [keyType]);

    function setMainFeedback(message, isError = false) {
        setFeedback({ message, isError });
    }

    async function handleSubmit(event) {
        event.preventDefault();
        setMainFeedback("Gerando QR Code...");

        let payload;
        try {
            payload = buildPixString({
                keyType,
                rawKey: pixKey,
                name,
                city,
                amount,
                txid
            });
        } catch (error) {
            setMainFeedback(error.message || "Dados invalidos.", true);
            return;
        }

        try {
            const response = await fetch("/api/qr", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ data: payload })
            });
            const data = await response.json();

            if (!response.ok) {
                setMainFeedback(data.error || "Erro ao gerar QR Code.", true);
                return;
            }

            setQrCodeDataUrl(data.qrCodeDataUrl);
            setPixString(payload);
            setMainFeedback("QR Code gerado com sucesso!");
        } catch {
            setMainFeedback("Erro de rede. Tente novamente.", true);
        }
    }

    async function handleCopy() {
        if (!pixString) {
            return;
        }

        try {
            await navigator.clipboard.writeText(pixString);
        } catch {
            // Fallback for browsers that block clipboard API
            const textarea = document.createElement("textarea");
            textarea.value = pixString;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
        }

        setCopyFeedback("Copiado!");
        window.setTimeout(() => setCopyFeedback(""), 2000);
    }

    const hasResult = Boolean(qrCodeDataUrl);

    return (
        <>
            <header className="topnav">
                <a href="/" className="brand">
                    QRCompact
                </a>
                <nav className="nav-links">
                    <a href="/">Links Curtos</a>
                    <a href="/pix.html" className="nav-active">
                        Gerar Pix
                    </a>
                </nav>
            </header>

            <main className="layout">
                <section className="hero card">
                    <h1>Gerar QR Code Pix</h1>
                    <p>Preencha os dados e gere seu QR Code e codigo Pix Copia e Cola gratuitamente.</p>
                </section>

                <div className="pix-grid">
                    <section className="card">
                        <h2 className="section-title">Dados Pix</h2>
                        <form id="pix-form" onSubmit={handleSubmit}>
                            <div className="field-group">
                                <label htmlFor="pix-key">Chave PIX</label>
                                <div className="key-input-row">
                                    <select
                                        id="key-type"
                                        aria-label="Tipo de chave Pix"
                                        value={keyType}
                                        onChange={(event) => {
                                            setKeyType(event.target.value);
                                            setPixKey("");
                                        }}
                                    >
                                        <option value="phone">Telefone</option>
                                        <option value="cpf">CPF</option>
                                        <option value="cnpj">CNPJ</option>
                                        <option value="email">Email</option>
                                        <option value="random">Aleatoria</option>
                                    </select>
                                    <input
                                        id="pix-key"
                                        type="text"
                                        placeholder={keyPlaceholder}
                                        autoComplete="off"
                                        required
                                        value={pixKey}
                                        onChange={(event) => setPixKey(event.target.value)}
                                    />
                                </div>
                            </div>

                            <div className="field-group">
                                <label htmlFor="pix-name">Nome do beneficiario <small>(ate 25 letras)</small></label>
                                <input
                                    id="pix-name"
                                    type="text"
                                    placeholder="Seu Nome"
                                    maxLength={30}
                                    required
                                    value={name}
                                    onChange={(event) => setName(event.target.value)}
                                />
                            </div>

                            <div className="field-group">
                                <label htmlFor="pix-city">Cidade do beneficiario <small>(ate 15 letras)</small></label>
                                <input
                                    id="pix-city"
                                    type="text"
                                    placeholder="Sua Cidade"
                                    maxLength={20}
                                    required
                                    value={city}
                                    onChange={(event) => setCity(event.target.value)}
                                />
                            </div>

                            <div className="field-group">
                                <label htmlFor="pix-amount">Valor para transferencia <small>(opcional)</small></label>
                                <input
                                    id="pix-amount"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="0,00"
                                    value={amount}
                                    onChange={(event) => setAmount(event.target.value)}
                                />
                            </div>

                            <div className="field-group">
                                <label htmlFor="pix-txid">Codigo da transferencia <small>(sem espaco, opcional, ate 25 caracteres)</small></label>
                                <input
                                    id="pix-txid"
                                    type="text"
                                    maxLength={25}
                                    placeholder="PEDIDO001"
                                    autoComplete="off"
                                    value={txid}
                                    onChange={(event) => setTxid(event.target.value)}
                                />
                            </div>

                            <p id="pix-feedback" className={`feedback ${feedback.isError ? "error" : ""}`} aria-live="polite">
                                {feedback.message}
                            </p>

                            <button type="submit" className="btn-full">Gerar QR Code Pix</button>
                        </form>
                    </section>

                    <div className="pix-right-col">
                        {!hasResult && (
                            <section id="pix-placeholder" className="card pix-placeholder">
                                <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <rect x="3" y="3" width="7" height="7" rx="1" />
                                    <rect x="3" y="14" width="7" height="7" rx="1" />
                                    <rect x="14" y="3" width="7" height="7" rx="1" />
                                    <rect x="5" y="5" width="3" height="3" fill="currentColor" stroke="none" />
                                    <rect x="5" y="16" width="3" height="3" fill="currentColor" stroke="none" />
                                    <rect x="16" y="5" width="3" height="3" fill="currentColor" stroke="none" />
                                    <line x1="14" y1="14" x2="14" y2="14.01" />
                                    <line x1="18" y1="14" x2="18" y2="14.01" />
                                    <line x1="21" y1="14" x2="21" y2="14.01" />
                                    <line x1="14" y1="18" x2="14" y2="21" />
                                    <line x1="18" y1="17" x2="18" y2="21" />
                                    <line x1="21" y1="17" x2="21" y2="21" />
                                </svg>
                                <p>Preencha os dados e clique em<br /><strong>Gerar QR Code Pix</strong></p>
                            </section>
                        )}

                        {hasResult && (
                            <section id="pix-output" className="card">
                                <h2 className="section-title">QR Code Pix</h2>

                                <div className="qr-center">
                                    <img id="pix-qr-image" alt="QR Code Pix" src={qrCodeDataUrl} />
                                </div>

                                <a id="download-qr" className="btn-download" download="qrcode-pix.png" href={qrCodeDataUrl}>
                                    Baixar QR Code (PNG)
                                </a>

                                <div className="pix-details">
                                    <p>Chave PIX: {pixKey}</p>
                                    <p>Nome: {name}</p>
                                    <p>Tipo de Chave: {keyType === "email" ? "Email" : keyType}</p>
                                    <p>Codigo da transferencia: {txid || "***"}</p>
                                </div>

                                <div className="copy-section">
                                    <label>Pix Copia e Cola</label>
                                    <div className="copy-row">
                                        <textarea id="pix-string" readOnly rows={4} aria-label="Codigo Pix copia e cola" value={pixString} />
                                        <button id="copy-btn" type="button" onClick={handleCopy}>Copiar</button>
                                    </div>
                                    <p id="copy-feedback" className="feedback" aria-live="polite">{copyFeedback}</p>
                                </div>


                            </section>
                        )}
                    </div>
                </div>
            </main>
        </>
    );
}

export default PixApp;
