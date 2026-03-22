import * as bootstrap from 'bootstrap';

const body = document.body;
const page = body.dataset.page;

const KEY_PLACEHOLDERS = {
    phone: '(11) 99999-9999',
    cpf: '000.000.000-00',
    cnpj: '00.000.000/0000-00',
    email: 'voce@exemplo.com',
    random: 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
};

function getMessage(payload, fallback) {
    if (!payload) {
        return fallback;
    }

    if (typeof payload.message === 'string' && payload.message.trim() !== '') {
        return payload.message;
    }

    if (payload.errors && typeof payload.errors === 'object') {
        const firstError = Object.values(payload.errors).flat()[0];
        if (firstError) {
            return firstError;
        }
    }

    return fallback;
}

async function requestJson(url, options = {}, fallbackMessage = 'Erro ao processar a solicitacao.') {
    const response = await fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
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
        return '';
    }

    try {
        const parsed = new URL(url);
        const compact = `${parsed.host}${parsed.pathname}${parsed.search}${parsed.hash}`;
        return compact.length <= maxLength ? compact : `${compact.slice(0, maxLength - 3)}...`;
    } catch {
        return url.length <= maxLength ? url : `${url.slice(0, maxLength - 3)}...`;
    }
}

function formatDate(isoString) {
    if (!isoString) {
        return '-';
    }

    return new Intl.DateTimeFormat('pt-BR', {
        dateStyle: 'short',
        timeStyle: 'short',
    }).format(new Date(isoString));
}

function setFeedback(element, message, isError = false) {
    element.textContent = message;
    element.classList.remove('is-success');
    element.classList.toggle('is-error', isError);
    element.classList.toggle('text-danger', isError);
}

function setSuccessFeedback(element, message) {
    setFeedback(element, message, false);
    element.classList.add('is-success');
}

function setupLinksPage() {
    const createForm = document.querySelector('[data-links-form]');
    if (!createForm) {
        return;
    }

    const urlInput = document.querySelector('[data-links-url]');
    const feedback = document.querySelector('[data-links-feedback]');
    const result = document.querySelector('[data-links-result]');
    const resultLink = document.querySelector('[data-result-link]');
    const resultQr = document.querySelector('[data-result-qr]');
    const list = document.querySelector('[data-links-list]');
    const emptyState = document.querySelector('[data-links-empty]');
    const detailModal = document.querySelector('[data-detail-modal]');
    const qrPlaceholder = document.querySelector('[data-qr-placeholder]');
    const detailTitle = document.querySelector('[data-detail-title]');
    const detailUrl = document.querySelector('[data-detail-url]');
    const detailDestination = document.querySelector('[data-detail-destination]');
    const detailCreatedAt = document.querySelector('[data-detail-created]');
    const detailUpdatedAt = document.querySelector('[data-detail-updated]');
    const detailQr = document.querySelector('[data-detail-qr]');
    const editModal = document.querySelector('[data-edit-modal]');
    const editForm = document.querySelector('[data-edit-form]');
    const editSlug = document.querySelector('[data-edit-slug]');
    const editUrl = document.querySelector('[data-edit-url]');
    const editFeedback = document.querySelector('[data-edit-feedback]');
    const modeInputs = document.querySelectorAll('[data-link-mode]');
    const whatsappFields = document.querySelector('[data-whatsapp-fields]');
    const waPhoneInput = document.querySelector('[data-wa-phone]');
    const waMessageInput = document.querySelector('[data-wa-message]');

    let editingSlug = null;

    function getSelectedMode() {
        if (modeInputs.length === 0) {
            return body.dataset.page === 'whatsapp' ? 'whatsapp' : 'url';
        }

        const selected = Array.from(modeInputs).find((input) => input.checked);
        return selected ? selected.value : 'url';
    }

    function syncLinkModeUI() {
        const isWhatsAppMode = getSelectedMode() === 'whatsapp';
        if (whatsappFields) {
            whatsappFields.hidden = !isWhatsAppMode;
        }

        const urlFieldWrapper = urlInput ? urlInput.closest('.mb-2') : null;
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
        const digits = rawPhone.replace(/\D/g, '');

        if (!digits || digits.length < 12) {
            throw new Error('Informe um numero WhatsApp valido com codigo do pais + DDD + numero.');
        }

        const query = message ? `?text=${encodeURIComponent(message)}` : '';

        return `https://wa.me/${digits}${query}`;
    }

    function ensureWhatsAppPrefix() {
        if (waPhoneInput && waPhoneInput.value.trim() === '') {
            waPhoneInput.value = '55';
        }
    }

    function renderLinks(links) {
        list.innerHTML = '';
        emptyState.hidden = links.length > 0;

        const maxClicks = links.reduce((highest, current) => {
            const clicks = Number(current.clickCount ?? 0);
            return clicks > highest ? clicks : highest;
        }, 0);

        links.forEach((item) => {
            const clicks = Number(item.clickCount ?? 0);
            const isMostAccessed = maxClicks > 0 && clicks === maxClicks;
            const badge = isMostAccessed
                ? '<span class="link-badge-top">Mais acessado</span>'
                : '';

            const article = document.createElement('article');
            article.className = 'link-item';
            article.innerHTML = `
                    <div class="link-item-info">
                        <div class="link-head">
                            <a href="${item.shortUrl}" target="_blank" rel="noreferrer" class="link-short">${item.shortUrl}</a>
                            ${badge}
                        </div>
                        <span class="link-target" title="${item.originalUrl}">${compactUrlText(item.originalUrl, 60)}</span>
                        <span class="link-target">Cliques: ${clicks}</span>
                    </div>
                    <div class="link-actions">
                        <button type="button" class="btn-action" data-action="detail" data-slug="${item.slug}">Ver QR</button>
                        <button type="button" class="btn-action" data-action="edit" data-slug="${item.slug}" data-url="${item.originalUrl}">Editar</button>
                    </div>
                `;
            list.appendChild(article);
        });
    }

    async function loadLinks() {
        try {
            const links = await requestJson('/api/links', {}, 'Nao foi possivel carregar os links.');
            renderLinks(links);
        } catch (error) {
            setFeedback(feedback, error.message, true);
        }
    }

    function openModal(modal) {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    function closeModal(modal) {
        bootstrap.Modal.getOrCreateInstance(modal).hide();
    }

    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        setFeedback(feedback, 'Gerando link curto...');

        try {
            const mode = getSelectedMode();
            const urlToShorten = mode === 'whatsapp'
                ? buildWhatsAppUrl()
                : (urlInput ? urlInput.value.trim() : '');

            const payload = await requestJson('/api/shorten', {
                method: 'POST',
                body: JSON.stringify({ url: urlToShorten }),
            }, 'Nao foi possivel gerar o link curto.');

            result.hidden = false;
            if (qrPlaceholder) qrPlaceholder.hidden = true;
            resultLink.href = payload.shortUrl;
            resultLink.textContent = payload.shortUrl;
            resultQr.src = payload.qrCodeDataUrl;
            resultQr.alt = `QR Code do link ${payload.slug}`;
            if (mode === 'whatsapp') {
                if (waPhoneInput) waPhoneInput.value = '55';
                if (waMessageInput) waMessageInput.value = '';
            } else if (urlInput) {
                urlInput.value = '';
            }
            setSuccessFeedback(feedback, payload.message || 'Link curto criado com sucesso.');
            await loadLinks();
        } catch (error) {
            setFeedback(feedback, error.message, true);
        }
    });

    modeInputs.forEach((input) => {
        input.addEventListener('change', syncLinkModeUI);
    });

    ensureWhatsAppPrefix();
    syncLinkModeUI();

    list.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const { action, slug } = button.dataset;
        if (action === 'detail') {
            try {
                const payload = await requestJson(`/api/links/${slug}`, {}, 'Nao foi possivel carregar os detalhes do link.');
                detailTitle.textContent = payload.slug;
                detailUrl.href = payload.shortUrl;
                detailUrl.textContent = payload.shortUrl;
                detailDestination.textContent = payload.originalUrl;
                detailCreatedAt.textContent = formatDate(payload.createdAt);
                detailUpdatedAt.textContent = formatDate(payload.updatedAt);
                detailQr.src = payload.qrCodeDataUrl;
                detailQr.alt = `QR Code do link ${payload.slug}`;
                openModal(detailModal);
            } catch (error) {
                setFeedback(feedback, error.message, true);
            }
        }

        if (action === 'edit') {
            editingSlug = slug;
            editSlug.textContent = slug;
            editUrl.value = button.dataset.url || '';
            setFeedback(editFeedback, '');
            openModal(editModal);
            editUrl.focus();
            editUrl.select();
        }
    });

    editForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!editingSlug) {
            return;
        }

        setFeedback(editFeedback, 'Salvando alteracoes...');

        try {
            const payload = await requestJson(`/api/links/${editingSlug}`, {
                method: 'PUT',
                body: JSON.stringify({ url: editUrl.value.trim() }),
            }, 'Nao foi possivel atualizar o link.');

            closeModal(editModal);
            setSuccessFeedback(feedback, payload.message || 'Link atualizado com sucesso.');
            await loadLinks();
        } catch (error) {
            setFeedback(editFeedback, error.message, true);
        }
    });

    editModal.addEventListener('hidden.bs.modal', () => {
        editingSlug = null;
        editForm.reset();
        setFeedback(editFeedback, '');
    });

    loadLinks();
}

function setupPixPage() {
    const form = document.querySelector('[data-pix-form]');
    if (!form) {
        return;
    }

    const feedback = document.querySelector('[data-pix-feedback]');
    const keyType = document.querySelector('[data-pix-key-type]');
    const key = document.querySelector('[data-pix-key]');
    const payloadOutput = document.querySelector('[data-pix-payload]');
    const qrImage = document.querySelector('[data-pix-qr]');
    const result = document.querySelector('[data-pix-result]');
    const copyButton = document.querySelector('[data-pix-copy]');
    const copyFeedback = document.querySelector('[data-pix-copy-feedback]');
    const qrPlaceholder = document.querySelector('[data-qr-placeholder]');

    function syncPlaceholder() {
        key.placeholder = KEY_PLACEHOLDERS[keyType.value] || 'Informe sua chave Pix';
    }

    keyType.addEventListener('change', syncPlaceholder);
    syncPlaceholder();

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setFeedback(feedback, 'Gerando payload Pix e QR Code...');

        const payloadBody = {
            mode: 'pix',
            key_type: keyType.value,
            key: key.value.trim(),
            name: document.querySelector('[data-pix-name]').value.trim(),
            city: document.querySelector('[data-pix-city]').value.trim(),
            amount: document.querySelector('[data-pix-amount]').value.trim(),
            txid: document.querySelector('[data-pix-txid]').value.trim(),
        };

        try {
            const payload = await requestJson('/api/qr', {
                method: 'POST',
                body: JSON.stringify(payloadBody),
            }, 'Nao foi possivel gerar o payload Pix.');

            payloadOutput.value = payload.payload;
            qrImage.src = payload.qrCodeDataUrl;
            result.hidden = false;
            if (qrPlaceholder) qrPlaceholder.hidden = true;
            setSuccessFeedback(feedback, payload.message || 'Payload Pix gerado com sucesso.');
        } catch (error) {
            setFeedback(feedback, error.message, true);
        }
    });

    copyButton.addEventListener('click', async () => {
        if (!payloadOutput.value) {
            return;
        }

        try {
            await navigator.clipboard.writeText(payloadOutput.value);
        } catch {
            payloadOutput.focus();
            payloadOutput.select();
            document.execCommand('copy');
        }

        copyFeedback.textContent = 'Codigo Pix copiado.';
        window.setTimeout(() => {
            copyFeedback.textContent = '';
        }, 1800);
    });
}

if (page === 'links') {
    setupLinksPage();
}

if (page === 'whatsapp') {
    setupLinksPage();
}

if (page === 'pix') {
    setupPixPage();
}
