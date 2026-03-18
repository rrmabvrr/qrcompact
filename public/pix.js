/* =============================================================
   CRC16-CCITT-FALSE
   Poly: 0x1021 | Init: 0xFFFF | RefIn: false | RefOut: false
   Usado pelo Banco Central do Brasil no padrão EMV QR Code Pix
   ============================================================= */
function crc16ccitt(str) {
    let crc = 0xFFFF;
    for (let i = 0; i < str.length; i++) {
        crc ^= str.charCodeAt(i) << 8;
        for (let j = 0; j < 8; j++) {
            crc = (crc & 0x8000)
                ? ((crc << 1) ^ 0x1021) & 0xFFFF
                : (crc << 1) & 0xFFFF;
        }
    }
    return crc.toString(16).toUpperCase().padStart(4, "0");
}

/* =============================================================
   TLV helper: "ID" + tamanho com 2 dígitos + valor
   ============================================================= */
function tlv(id, value) {
    return `${id}${String(value.length).padStart(2, "0")}${value}`;
}

/* =============================================================
   Remove acentos e caracteres não-ASCII para campos de nome/cidade
   ============================================================= */
function sanitize(str, maxLength) {
    return str
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^A-Za-z0-9 ]/g, " ")
        .replace(/\s+/g, " ")
        .trim()
        .slice(0, maxLength);
}

/* =============================================================
   Formata a chave Pix de acordo com o tipo selecionado
   ============================================================= */
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
    return value; // random key: manter como está
}

/* =============================================================
   Monta a string EMV QR Code Pix (padrão BCB)
   ============================================================= */
function buildPixString({ keyType, rawKey, name, city, amount, txid }) {
    const key = formatPixKey(keyType, rawKey);
    if (!key) throw new Error("Informe a chave Pix.");

    const nameClean = sanitize(name, 25);
    if (!nameClean) throw new Error("Informe o nome do beneficiário.");

    const cityClean = sanitize(city, 15);
    if (!cityClean) throw new Error("Informe a cidade.");

    // Tag 26 — Merchant account info
    const merchantInfo = tlv("26",
        tlv("00", "BR.GOV.BCB.PIX") +
        tlv("01", key)
    );

    // Tag 54 — Valor (opcional, só inclui se > 0)
    const amountNum = parseFloat(String(amount || "0").replace(",", ".")) || 0;
    const amountField = amountNum > 0 ? tlv("54", amountNum.toFixed(2)) : "";

    // Tag 62 / 05 — txid / referência
    const txidClean = (txid || "").replace(/[^A-Za-z0-9]/g, "").slice(0, 25) || "***";
    const additionalData = tlv("62", tlv("05", txidClean));

    // Monta payload sem o valor do CRC (os 4 últimos char serão o CRC)
    const payload =
        tlv("00", "01") +      // Payload format indicator
        merchantInfo +          // Merchant account info
        tlv("52", "0000") +    // Merchant category code
        tlv("53", "986") +     // Currency (BRL = 986)
        amountField +           // Amount (optional)
        tlv("58", "BR") +      // Country code
        tlv("59", nameClean) + // Merchant name
        tlv("60", cityClean) + // Merchant city
        additionalData +        // Additional data
        "6304";                 // Tag CRC sem valor

    return payload + crc16ccitt(payload);
}

/* =============================================================
   Elementos DOM
   ============================================================= */
const keyTypeSelect = document.getElementById("key-type");
const pixKeyInput = document.getElementById("pix-key");
const pixNameInput = document.getElementById("pix-name");
const pixCityInput = document.getElementById("pix-city");
const pixAmountInput = document.getElementById("pix-amount");
const pixTxidInput = document.getElementById("pix-txid");
const pixFeedback = document.getElementById("pix-feedback");
const pixPlaceholder = document.getElementById("pix-placeholder");
const pixOutput = document.getElementById("pix-output");
const pixQrImage = document.getElementById("pix-qr-image");
const pixStringField = document.getElementById("pix-string");
const copyBtn = document.getElementById("copy-btn");
const copyFeedback = document.getElementById("copy-feedback");
const downloadLink = document.getElementById("download-qr");
const form = document.getElementById("pix-form");

/* =============================================================
   Placeholder por tipo de chave
   ============================================================= */
const KEY_PLACEHOLDERS = {
    phone: "(00) 00000-0000",
    cpf: "000.000.000-00",
    cnpj: "00.000.000/0000-00",
    email: "seu@email.com",
    random: "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
};

keyTypeSelect.addEventListener("change", () => {
    pixKeyInput.placeholder = KEY_PLACEHOLDERS[keyTypeSelect.value] || "";
    pixKeyInput.value = "";
    pixKeyInput.focus();
});

/* =============================================================
   Helpers de feedback
   ============================================================= */
function setFeedback(el, message, isError = false) {
    el.textContent = message;
    el.classList.toggle("error", isError);
}

/* =============================================================
   Submit do formulário
   ============================================================= */
form.addEventListener("submit", async (event) => {
    event.preventDefault();
    setFeedback(pixFeedback, "Gerando QR Code...");

    let pixString;
    try {
        pixString = buildPixString({
            keyType: keyTypeSelect.value,
            rawKey: pixKeyInput.value,
            name: pixNameInput.value,
            city: pixCityInput.value,
            amount: pixAmountInput.value,
            txid: pixTxidInput.value,
        });
    } catch (err) {
        setFeedback(pixFeedback, err.message, true);
        return;
    }

    try {
        const response = await fetch("/api/qr", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ data: pixString }),
        });

        const result = await response.json();

        if (!response.ok) {
            setFeedback(pixFeedback, result.error || "Erro ao gerar QR Code.", true);
            return;
        }

        pixQrImage.src = result.qrCodeDataUrl;
        pixStringField.value = pixString;
        downloadLink.href = result.qrCodeDataUrl;

        pixPlaceholder.classList.add("hidden");
        pixOutput.classList.remove("hidden");

        setFeedback(pixFeedback, "QR Code gerado com sucesso!");
        pixOutput.scrollIntoView({ behavior: "smooth", block: "nearest" });

    } catch {
        setFeedback(pixFeedback, "Erro de rede. Tente novamente.", true);
    }
});

/* =============================================================
   Copiar para clipboard
   ============================================================= */
copyBtn.addEventListener("click", async () => {
    const text = pixStringField.value;
    try {
        await navigator.clipboard.writeText(text);
    } catch {
        pixStringField.select();
        document.execCommand("copy");
    }
    setFeedback(copyFeedback, "Copiado!");
    setTimeout(() => setFeedback(copyFeedback, ""), 2000);
});
