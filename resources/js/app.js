import * as bootstrap from "bootstrap";
import {
    downloadBlob,
    getJpgBlob,
    getPngBlob,
    svgDataUrlToBlob,
} from "./qr-downloads";

const body = document.body;
const page = body.dataset.page;

const KEY_PLACEHOLDERS = {
    phone: "(95) 99999-9999",
    cpf: "000.000.000-00",
    cnpj: "00.000.000/0000-00",
    email: "voce@exemplo.com",
    random: "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
};

function getMessage(payload, fallback) {
    if (!payload) {
        return fallback;
    }

    if (typeof payload.message === "string" && payload.message.trim() !== "") {
        return payload.message;
    }

    if (payload.errors && typeof payload.errors === "object") {
        const firstError = Object.values(payload.errors).flat()[0];
        if (firstError) {
            return firstError;
        }
    }

    return fallback;
}

async function requestJson(
    url,
    options = {},
    fallbackMessage = "Erro ao processar a solicitacao.",
) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    const response = await fetch(url, {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}),
            ...(options.headers || {}),
        },
        ...options,
    });

    let payload = null;

    try {
        payload = await response.json();
    } catch {
        payload = null;
    }

    if (!response.ok) {
        throw new Error(getMessage(payload, fallbackMessage));
    }

    return payload;
}

function compactUrlText(url, maxLength = 56) {
    if (!url) {
        return "";
    }

    try {
        const parsed = new URL(url);
        const compact = `${parsed.host}${parsed.pathname}${parsed.search}${parsed.hash}`;
        return compact.length <= maxLength
            ? compact
            : `${compact.slice(0, maxLength - 3)}...`;
    } catch {
        return url.length <= maxLength
            ? url
            : `${url.slice(0, maxLength - 3)}...`;
    }
}

function formatDate(isoString) {
    if (!isoString) {
        return "-";
    }

    return new Intl.DateTimeFormat("pt-BR", {
        dateStyle: "short",
        timeStyle: "short",
    }).format(new Date(isoString));
}

function setFeedback(element, message, isError = false) {
    if (!element) {
        return;
    }

    element.textContent = message;
    element.classList.remove("is-success");
    element.classList.toggle("is-error", isError);
    element.classList.toggle("text-danger", isError);
}

function setSuccessFeedback(element, message) {
    if (!element) {
        return;
    }

    setFeedback(element, message, false);
    element.classList.add("is-success");
}

function setupLinksPage() {
    const createForm = document.querySelector("[data-links-form]");
    if (!createForm) {
        return;
    }

    const nameInput = document.querySelector("[data-links-name]");
    const urlInput = document.querySelector("[data-links-url]");
    const feedback = document.querySelector("[data-links-feedback]");
    const result = document.querySelector("[data-links-result]");
    const resultName = document.querySelector("[data-result-name]");
    const resultLink = document.querySelector("[data-result-link]");
    const resultQr = document.querySelector("[data-result-qr]");
    const list = document.querySelector("[data-links-list]");
    const emptyState = document.querySelector("[data-links-empty]");
    const detailModal = document.querySelector("[data-detail-modal]");
    const qrPlaceholder = document.querySelector("[data-qr-placeholder]");
    const detailTitle = document.querySelector("[data-detail-name-title]");
    const detailName = document.querySelector("[data-detail-name]");
    const detailUrl = document.querySelector("[data-detail-url]");
    const detailDestination = document.querySelector(
        "[data-detail-destination]",
    );
    const detailCreatedAt = document.querySelector("[data-detail-created]");
    const detailUpdatedAt = document.querySelector("[data-detail-updated]");
    const detailQr = document.querySelector("[data-detail-qr]");
    const editModal = document.querySelector("[data-edit-modal]");
    const editForm = document.querySelector("[data-edit-form]");
    const editNameTitle = document.querySelector("[data-edit-nome]");
    const editName = document.querySelector("[data-edit-name]");
    const editUrl = document.querySelector("[data-edit-url]");
    const editFeedback = document.querySelector("[data-edit-feedback]");
    const modeInputs = document.querySelectorAll("[data-link-mode]");
    const whatsappFields = document.querySelector("[data-whatsapp-fields]");
    const waPhoneInput = document.querySelector("[data-wa-phone]");
    const waMessageInput = document.querySelector("[data-wa-message]");

    let editingSlug = null;
    let editingName = "";

    function getSelectedMode() {
        if (modeInputs.length === 0) {
            return body.dataset.page === "whatsapp" ? "whatsapp" : "url";
        }

        const selected = Array.from(modeInputs).find((input) => input.checked);
        return selected ? selected.value : "url";
    }

    function syncLinkModeUI() {
        const isWhatsAppMode = getSelectedMode() === "whatsapp";
        if (whatsappFields) {
            whatsappFields.hidden = !isWhatsAppMode;
        }

        const urlFieldWrapper = urlInput ? urlInput.closest(".mb-2") : null;
        if (urlFieldWrapper) {
            urlFieldWrapper.hidden = isWhatsAppMode;
        }

        if (urlInput) {
            urlInput.required = !isWhatsAppMode;
        }
        if (waPhoneInput) waPhoneInput.required = isWhatsAppMode;
        if (waMessageInput) waMessageInput.required = false;
    }

    function buildWhatsAppUrl() {
        const rawPhone = waPhoneInput.value.trim();
        const message = waMessageInput.value.trim();
        const digits = rawPhone.replace(/\D/g, "");

        if (!digits || digits.length < 12) {
            throw new Error(
                "Informe um numero WhatsApp valido com codigo do pais + DDD + numero.",
            );
        }

        const query = message ? `?text=${encodeURIComponent(message)}` : "";

        return `https://wa.me/${digits}${query}`;
    }

    function ensureWhatsAppPrefix() {
        if (waPhoneInput && waPhoneInput.value.trim() === "") {
            waPhoneInput.value = "55";
        }
    }

    function renderLinks(links) {
        list.innerHTML = "";
        emptyState.hidden = links.length > 0;

        const maxClicks = links.reduce((highest, current) => {
            const clicks = Number(current.clickCount ?? 0);
            return clicks > highest ? clicks : highest;
        }, 0);

        links.forEach((item) => {
            const clicks = Number(item.clickCount ?? 0);
            const isMostAccessed = maxClicks > 0 && clicks === maxClicks;
            const displayName = item.name || item.slug;
            const badge = isMostAccessed
                ? '<span class="link-badge-top">Mais acessado</span>'
                : "";

            const article = document.createElement("article");
            article.className = "link-item";

            const info = document.createElement("div");
            info.className = "link-item-info";

            const name = document.createElement("span");
            name.className = "link-target";
            name.textContent = `Nome: ${displayName}`;

            const head = document.createElement("div");
            head.className = "link-head";

            const shortLink = document.createElement("a");
            shortLink.href = item.shortUrl;
            shortLink.target = "_blank";
            shortLink.rel = "noreferrer";
            shortLink.className = "link-short";
            shortLink.textContent = item.shortUrl;
            head.appendChild(shortLink);

            if (badge) {
                head.insertAdjacentHTML("beforeend", badge);
            }

            const target = document.createElement("span");
            target.className = "link-target";
            target.title = item.originalUrl;
            target.textContent = compactUrlText(item.originalUrl, 60);

            const clickCounter = document.createElement("span");
            clickCounter.className = "link-target";
            clickCounter.textContent = `Cliques: ${clicks}`;

            info.append(name, head, target, clickCounter);

            const actions = document.createElement("div");
            actions.className = "link-actions";

            const detailButton = document.createElement("button");
            detailButton.type = "button";
            detailButton.className = "btn-action";
            detailButton.dataset.action = "detail";
            detailButton.dataset.slug = item.slug;
            detailButton.textContent = "Ver QR";

            const editButton = document.createElement("button");
            editButton.type = "button";
            editButton.className = "btn-action";
            editButton.dataset.action = "edit";
            editButton.dataset.slug = item.slug;
            editButton.dataset.name = displayName;
            editButton.dataset.url = item.originalUrl;
            editButton.textContent = "Editar";

            actions.append(detailButton, editButton);
            article.append(info, actions);
            list.appendChild(article);
        });
    }

    async function loadLinks() {
        try {
            const links = await requestJson(
                "/api/links",
                {},
                "Nao foi possivel carregar os links.",
            );
            renderLinks(links);
        } catch (error) {
            setFeedback(feedback, error.message, true);
        }
    }

    function openModal(modal) {
        if (!modal) {
            return;
        }

        bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }

        bootstrap.Modal.getOrCreateInstance(modal).hide();
    }

    createForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        setFeedback(feedback, "Gerando link curto...");

        try {
            const mode = getSelectedMode();
            const typedName = nameInput ? nameInput.value.trim() : "";
            const urlToShorten =
                mode === "whatsapp"
                    ? buildWhatsAppUrl()
                    : urlInput
                      ? urlInput.value.trim()
                      : "";
            const fallbackWhatsappName = waPhoneInput
                ? `WhatsApp ${waPhoneInput.value.trim()}`
                : "WhatsApp";
            const name =
                typedName || (mode === "whatsapp" ? fallbackWhatsappName : "");

            const payload = await requestJson(
                "/api/shorten",
                {
                    method: "POST",
                    body: JSON.stringify({ name, url: urlToShorten }),
                },
                "Nao foi possivel gerar o link curto.",
            );

            result.hidden = false;
            if (qrPlaceholder) qrPlaceholder.hidden = true;
            if (resultName) resultName.textContent = payload.name || name;
            resultLink.href = payload.shortUrl;
            resultLink.textContent = payload.shortUrl;
            resultQr.src = payload.qrCodeDataUrl;
            resultQr.alt = `QR Code do link ${payload.slug}`;
            if (nameInput) nameInput.value = "";
            if (mode === "whatsapp") {
                if (waPhoneInput) waPhoneInput.value = "55";
                if (waMessageInput) waMessageInput.value = "";
            } else if (urlInput) {
                urlInput.value = "";
            }
            setSuccessFeedback(
                feedback,
                payload.message || "Link curto criado com sucesso.",
            );
            await loadLinks();
        } catch (error) {
            setFeedback(feedback, error.message, true);
        }
    });

    modeInputs.forEach((input) => {
        input.addEventListener("change", syncLinkModeUI);
    });

    ensureWhatsAppPrefix();
    syncLinkModeUI();

    list.addEventListener("click", async (event) => {
        const button = event.target.closest("button[data-action]");
        if (!button) {
            return;
        }

        const { action, slug } = button.dataset;
        if (action === "detail") {
            try {
                const payload = await requestJson(
                    `/api/links/${slug}`,
                    {},
                    "Nao foi possivel carregar os detalhes do link.",
                );
                if (detailTitle)
                    detailTitle.textContent = payload.name || payload.slug;
                if (detailName) detailName.textContent = payload.name || "-";
                if (detailUrl) {
                    detailUrl.href = payload.shortUrl;
                    detailUrl.textContent = payload.shortUrl;
                }
                if (detailDestination)
                    detailDestination.textContent = payload.originalUrl;
                if (detailCreatedAt)
                    detailCreatedAt.textContent = formatDate(payload.createdAt);
                if (detailUpdatedAt)
                    detailUpdatedAt.textContent = formatDate(payload.updatedAt);
                if (detailQr) {
                    detailQr.src = payload.qrCodeDataUrl;
                    detailQr.alt = `QR Code do link ${payload.slug}`;
                    detailQr.dataset.svgDataUrl =
                        payload.qrCodeSvgDataUrl || "";
                }
                openModal(detailModal);
            } catch (error) {
                setFeedback(feedback, error.message, true);
            }
        }

        if (action === "edit") {
            editingSlug = slug;
            editingName = button.dataset.name || "";
            if (editNameTitle)
                editNameTitle.textContent = button.dataset.name || slug;
            if (editName) editName.value = button.dataset.name || "";
            if (editUrl) editUrl.value = button.dataset.url || "";
            if (editFeedback) setFeedback(editFeedback, "");
            openModal(editModal);
            if (editName) {
                editName.focus();
                editName.select();
            } else if (editUrl) {
                editUrl.focus();
                editUrl.select();
            }
        }
    });

    if (editForm) {
        editForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            if (!editingSlug || !editUrl) {
                return;
            }

            if (editFeedback)
                setFeedback(editFeedback, "Salvando alteracoes...");

            try {
                const payload = await requestJson(
                    `/api/links/${editingSlug}`,
                    {
                        method: "PUT",
                        body: JSON.stringify({
                            name: editName
                                ? editName.value.trim()
                                : editingName,
                            url: editUrl.value.trim(),
                        }),
                    },
                    "Nao foi possivel atualizar o link.",
                );

                closeModal(editModal);
                setSuccessFeedback(
                    feedback,
                    payload.message || "Link atualizado com sucesso.",
                );
                await loadLinks();
            } catch (error) {
                if (editFeedback)
                    setFeedback(editFeedback, error.message, true);
            }
        });
    }

    if (editModal && editForm) {
        editModal.addEventListener("hidden.bs.modal", () => {
            editingSlug = null;
            editingName = "";
            editForm.reset();
            if (editFeedback) setFeedback(editFeedback, "");
        });
    }

    if (detailModal) {
        detailModal.addEventListener("click", (event) => {
            const button = event.target.closest(
                "[data-download-qr-svg], [data-download-qr-png], [data-download-qr-jpg]",
            );
            if (!button) return;

            const qrImg = detailQr;
            if (!qrImg || !qrImg.src) {
                console.warn("QR image not found or has no src");
                return;
            }

            const imgSrc = qrImg.src;

            // PNG download
            if (button.matches("[data-download-qr-png]")) {
                console.log("PNG download requested");
                getPngBlob(imgSrc)
                    .then((blob) => downloadBlob(blob, "qrcode.png"))
                    .catch((err) => {
                        console.error("Erro ao processar PNG:", err);
                        alert("Erro ao baixar PNG");
                    });
            }

            // JPG download
            if (button.matches("[data-download-qr-jpg]")) {
                console.log("JPG download requested");
                getJpgBlob(imgSrc)
                    .then((blob) => downloadBlob(blob, "qrcode.jpg"))
                    .catch((err) => {
                        console.error("Erro ao carregar imagem para JPG", err);
                        alert("Erro ao baixar JPG");
                    });
            }

            // SVG download
            if (button.matches("[data-download-qr-svg]")) {
                console.log("SVG download requested");
                const svgDataUrl = qrImg?.dataset?.svgDataUrl;
                if (svgDataUrl && svgDataUrl.startsWith("data:image/svg+xml")) {
                    try {
                        const blob = svgDataUrlToBlob(svgDataUrl);
                        downloadBlob(blob, "qrcode.svg");
                    } catch (err) {
                        console.error("Erro ao processar SVG:", err);
                        alert("Erro ao processar QR Code SVG");
                    }
                } else {
                    alert(
                        "QR Code em SVG não disponível para este link. Tente outro formato.",
                    );
                }
            }
        });
    }

    loadLinks();
}

function setupPixPage() {
    const form = document.querySelector("[data-pix-form]");
    if (!form) {
        return;
    }

    const feedback = document.querySelector("[data-pix-feedback]");
    const keyType = document.querySelector("[data-pix-key-type]");
    const key = document.querySelector("[data-pix-key]");
    const payloadOutput = document.querySelector("[data-pix-payload]");
    const qrImage = document.querySelector("[data-pix-qr]");
    const result = document.querySelector("[data-pix-result]");
    const copyButton = document.querySelector("[data-pix-copy]");
    const copyFeedback = document.querySelector("[data-pix-copy-feedback]");
    const qrPlaceholder = document.querySelector("[data-qr-placeholder]");
    const summaryKey = document.querySelector("[data-pix-summary-key]");
    const summaryName = document.querySelector("[data-pix-summary-name]");
    const summaryTxid = document.querySelector("[data-pix-summary-txid]");

    function syncPlaceholder() {
        key.placeholder =
            KEY_PLACEHOLDERS[keyType.value] || "Informe sua chave Pix";
    }

    keyType.addEventListener("change", syncPlaceholder);
    syncPlaceholder();

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        setFeedback(feedback, "Gerando payload Pix e QR Code...");

        const payloadBody = {
            mode: "pix",
            key_type: keyType.value,
            key: key.value.trim(),
            name: document.querySelector("[data-pix-name]").value.trim(),
            city: document.querySelector("[data-pix-city]").value.trim(),
            amount: document.querySelector("[data-pix-amount]").value.trim(),
            txid: document.querySelector("[data-pix-txid]").value.trim(),
        };

        try {
            const payload = await requestJson(
                "/api/qr",
                {
                    method: "POST",
                    body: JSON.stringify(payloadBody),
                },
                "Nao foi possivel gerar o payload Pix.",
            );

            payloadOutput.value = payload.payload;
            qrImage.src = payload.qrCodeDataUrl;
            if (summaryKey) summaryKey.textContent = payloadBody.key || "—";
            if (summaryName) summaryName.textContent = payloadBody.name || "—";
            if (summaryTxid)
                summaryTxid.textContent = payloadBody.txid || "***";
            result.hidden = false;
            if (qrPlaceholder) qrPlaceholder.hidden = true;
            setSuccessFeedback(
                feedback,
                payload.message || "Payload Pix gerado com sucesso.",
            );
        } catch (error) {
            setFeedback(feedback, error.message, true);
        }
    });

    copyButton.addEventListener("click", async () => {
        if (!payloadOutput.value) {
            return;
        }

        try {
            await navigator.clipboard.writeText(payloadOutput.value);
        } catch {
            payloadOutput.focus();
            payloadOutput.select();
            document.execCommand("copy");
        }

        copyFeedback.textContent = "Codigo Pix copiado.";
        window.setTimeout(() => {
            copyFeedback.textContent = "";
        }, 1800);
    });
}

if (page === "links") {
    setupLinksPage();
}

if (page === "whatsapp") {
    setupLinksPage();
}

if (page === "pix") {
    setupPixPage();
}
