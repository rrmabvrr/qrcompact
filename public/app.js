const form = document.getElementById("shorten-form");
const urlInput = document.getElementById("url-input");
const feedback = document.getElementById("feedback");
const result = document.getElementById("result");
const shortUrlElement = document.getElementById("short-url");
const qrImage = document.getElementById("qr-image");
const linksList = document.getElementById("links-list");
const detailsModal = document.getElementById("details-modal");
const modalShortUrl = document.getElementById("modal-short-url");
const modalOriginalUrl = document.getElementById("modal-original-url");
const modalQrImage = document.getElementById("modal-qr-image");
const closeModalButton = document.getElementById("close-modal-button");
const editModal = document.getElementById("edit-modal");
const editSlug = document.getElementById("edit-slug");
const editForm = document.getElementById("edit-form");
const editUrlInput = document.getElementById("edit-url-input");
const editFeedback = document.getElementById("edit-feedback");
const cancelEditButton = document.getElementById("cancel-edit-button");

let currentEditSlug = null;
let lastFocusedElement = null;

function getFocusableElements(container) {
    return Array.from(
        container.querySelectorAll(
            "a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex='-1'])"
        )
    ).filter((element) => {
        if (element.offsetParent !== null) {
            return true;
        }

        return element === document.activeElement;
    });
}

function trapFocusInsideModal(event, modal) {
    if (event.key !== "Tab" || modal.classList.contains("hidden")) {
        return;
    }

    const focusableElements = getFocusableElements(modal);
    if (!focusableElements.length) {
        return;
    }

    const first = focusableElements[0];
    const last = focusableElements[focusableElements.length - 1];
    const current = document.activeElement;

    if (event.shiftKey && current === first) {
        event.preventDefault();
        last.focus();
        return;
    }

    if (!event.shiftKey && current === last) {
        event.preventDefault();
        first.focus();
    }
}

function openDetailsModal(triggerElement) {
    lastFocusedElement = triggerElement || document.activeElement;
    detailsModal.classList.remove("hidden");
    closeModalButton.focus();
}

function closeDetailsModal() {
    detailsModal.classList.add("hidden");
    if (lastFocusedElement && typeof lastFocusedElement.focus === "function") {
        lastFocusedElement.focus();
    }
}

function setFeedback(message, isError = false) {
    feedback.textContent = message;
    feedback.classList.toggle("error", isError);
}

function compactUrlText(url, maxLength = 42) {
    if (!url) {
        return url;
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

function renderLinks(items) {
    linksList.innerHTML = "";

    if (!items.length) {
        const li = document.createElement("li");
        li.textContent = "Nenhum link criado ainda.";
        linksList.appendChild(li);
        return;
    }

    items.forEach((item) => {
        const li = document.createElement("li");
        const rowTop = document.createElement("div");
        rowTop.className = "link-row-top";

        const shortAnchor = document.createElement("a");
        shortAnchor.href = item.shortUrl;
        shortAnchor.target = "_blank";
        shortAnchor.rel = "noopener noreferrer";
        shortAnchor.className = "short-link";
        shortAnchor.textContent = item.shortUrl;

        const targetSpan = document.createElement("span");
        targetSpan.className = "target";
        targetSpan.title = item.original_url;
        targetSpan.textContent = `Destino: ${compactUrlText(item.original_url)}`;

        const actions = document.createElement("div");
        actions.className = "actions";

        const editButton = document.createElement("button");
        editButton.type = "button";
        editButton.textContent = "Editar";
        editButton.addEventListener("click", () => editLink(item, editButton));

        const viewButton = document.createElement("button");
        viewButton.type = "button";
        viewButton.textContent = "Visualizar";
        viewButton.addEventListener("click", () => viewDetails(item.slug, viewButton));

        actions.appendChild(editButton);
        actions.appendChild(viewButton);

        rowTop.appendChild(shortAnchor);
        rowTop.appendChild(actions);
        li.appendChild(rowTop);
        li.appendChild(targetSpan);
        linksList.appendChild(li);
    });
}

async function viewDetails(slug, triggerElement) {
    try {
        const response = await fetch(`/api/links/${slug}`);
        const data = await response.json();

        if (!response.ok) {
            setFeedback(data.error || "Nao foi possivel carregar os detalhes.", true);
            return;
        }

        modalShortUrl.href = data.shortUrl;
        modalShortUrl.textContent = data.shortUrl;
        modalOriginalUrl.textContent = data.originalUrl;
        modalQrImage.src = data.qrCodeDataUrl;
        openDetailsModal(triggerElement);
    } catch {
        setFeedback("Erro ao carregar os detalhes do link.", true);
    }
}

async function editLink(item, triggerElement) {
    lastFocusedElement = triggerElement || document.activeElement;
    currentEditSlug = item.slug;
    editSlug.textContent = item.slug;
    editUrlInput.value = item.original_url;
    editFeedback.textContent = "";
    editFeedback.classList.remove("error");
    editUrlInput.classList.remove("invalid");
    editModal.classList.remove("hidden");
    editUrlInput.focus();
}

function closeEditModal() {
    editModal.classList.add("hidden");
    currentEditSlug = null;
    if (lastFocusedElement && typeof lastFocusedElement.focus === "function") {
        lastFocusedElement.focus();
    }
}

function isValidHttpUrl(url) {
    try {
        const parsed = new URL(url);
        return parsed.protocol === "http:" || parsed.protocol === "https:";
    } catch {
        return false;
    }
}

editForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const newUrl = editUrlInput.value.trim();
    if (!newUrl || !isValidHttpUrl(newUrl)) {
        editFeedback.textContent = "Informe uma URL valida com http:// ou https://";
        editFeedback.classList.add("error");
        editUrlInput.classList.add("invalid");
        return;
    }

    editFeedback.textContent = "Salvando alteracoes...";
    editFeedback.classList.remove("error");
    editUrlInput.classList.remove("invalid");

    try {
        const response = await fetch(`/api/links/${currentEditSlug}`, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ url: newUrl.trim() })
        });
        const data = await response.json();

        if (!response.ok) {
            editFeedback.textContent = data.error || "Erro ao editar link.";
            editFeedback.classList.add("error");
            editUrlInput.classList.add("invalid");
            return;
        }

        closeEditModal();
        setFeedback("Link atualizado com sucesso.");
        await loadLinks();
    } catch {
        editFeedback.textContent = "Erro de rede ao editar link.";
        editFeedback.classList.add("error");
    }
});

async function loadLinks() {
    try {
        const response = await fetch("/api/links");
        const data = await response.json();
        renderLinks(data);
    } catch {
        setFeedback("Nao foi possivel carregar os links existentes.", true);
    }
}

form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const url = urlInput.value.trim();

    if (!url) {
        setFeedback("Informe uma URL valida.", true);
        return;
    }

    setFeedback("Gerando link curto...");

    try {
        const response = await fetch("/api/shorten", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ url })
        });

        const data = await response.json();

        if (!response.ok) {
            setFeedback(data.error || "Erro ao gerar link.", true);
            return;
        }

        shortUrlElement.href = data.shortUrl;
        shortUrlElement.textContent = data.shortUrl;
        qrImage.src = data.qrCodeDataUrl;
        result.classList.remove("hidden");
        setFeedback("Link e QR Code gerados com sucesso.");

        urlInput.value = "";
        await loadLinks();
    } catch {
        setFeedback("Erro de rede. Tente novamente.", true);
    }
});

closeModalButton.addEventListener("click", closeDetailsModal);

detailsModal.addEventListener("click", (event) => {
    if (event.target === detailsModal) {
        closeDetailsModal();
    }
});

cancelEditButton.addEventListener("click", closeEditModal);

editModal.addEventListener("click", (event) => {
    if (event.target === editModal) {
        closeEditModal();
    }
});

document.addEventListener("keydown", (event) => {
    if (!editModal.classList.contains("hidden")) {
        trapFocusInsideModal(event, editModal);
    } else if (!detailsModal.classList.contains("hidden")) {
        trapFocusInsideModal(event, detailsModal);
    }

    if (event.key !== "Escape") {
        return;
    }

    if (!editModal.classList.contains("hidden")) {
        closeEditModal();
        return;
    }

    if (!detailsModal.classList.contains("hidden")) {
        closeDetailsModal();
    }
});

loadLinks();
