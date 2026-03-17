const form = document.getElementById("shorten-form");
const urlInput = document.getElementById("url-input");
const feedback = document.getElementById("feedback");
const result = document.getElementById("result");
const shortUrlElement = document.getElementById("short-url");
const qrImage = document.getElementById("qr-image");
const linksList = document.getElementById("links-list");

function setFeedback(message, isError = false) {
  feedback.textContent = message;
  feedback.classList.toggle("error", isError);
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

    const shortAnchor = document.createElement("a");
    shortAnchor.href = item.shortUrl;
    shortAnchor.target = "_blank";
    shortAnchor.rel = "noopener noreferrer";
    shortAnchor.textContent = item.shortUrl;

    const targetSpan = document.createElement("span");
    targetSpan.className = "target";
    targetSpan.textContent = `Destino: ${item.original_url}`;

    li.appendChild(shortAnchor);
    li.appendChild(targetSpan);
    linksList.appendChild(li);
  });
}

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

loadLinks();
