export function downloadBlob(blob, filename, deps = {}) {
    const documentRef = deps.documentRef || document;
    const urlRef = deps.urlRef || URL;
    const schedule = deps.schedule || window.setTimeout;

    const url = urlRef.createObjectURL(blob);
    const anchor = documentRef.createElement("a");
    anchor.href = url;
    anchor.download = filename;
    documentRef.body.appendChild(anchor);
    anchor.click();

    schedule(() => {
        documentRef.body.removeChild(anchor);
        urlRef.revokeObjectURL(url);
    }, 100);
}

export function dataURLtoBlob(dataUrl) {
    const arr = dataUrl.split(",");
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    const n = bstr.length;
    const u8arr = new Uint8Array(n);

    for (let i = 0; i < n; i++) {
        u8arr[i] = bstr.charCodeAt(i);
    }

    return new Blob([u8arr], { type: mime });
}

export function decodeSvgDataUrl(svgDataUrl) {
    if (!svgDataUrl || !svgDataUrl.startsWith("data:image/svg+xml")) {
        throw new Error("Invalid SVG data URL");
    }

    const parts = svgDataUrl.split(",");
    const payload = parts[1] || "";

    if (svgDataUrl.includes(";base64")) {
        return atob(payload);
    }

    return decodeURIComponent(payload);
}

export function svgDataUrlToBlob(svgDataUrl) {
    const svgData = decodeSvgDataUrl(svgDataUrl);
    return new Blob([svgData], { type: "image/svg+xml" });
}

export async function getPngBlob(imgSrc, deps = {}) {
    if (imgSrc.startsWith("data:")) {
        return dataURLtoBlob(imgSrc);
    }

    const fetchImpl = deps.fetchImpl || fetch;
    const response = await fetchImpl(imgSrc);
    return response.blob();
}

export function getJpgBlob(imgSrc, deps = {}) {
    const createImage = deps.createImage || (() => new window.Image());
    const createCanvas =
        deps.createCanvas || (() => document.createElement("canvas"));
    const quality = deps.quality ?? 0.95;

    return new Promise((resolve, reject) => {
        const img = createImage();
        img.crossOrigin = "anonymous";

        img.onload = () => {
            const canvas = createCanvas();
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext("2d");

            if (!ctx) {
                reject(new Error("Canvas 2D context not available"));
                return;
            }

            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);

            canvas.toBlob(
                (blob) => {
                    if (!blob) {
                        reject(new Error("Failed to convert QR image to JPG"));
                        return;
                    }

                    resolve(blob);
                },
                "image/jpeg",
                quality,
            );
        };

        img.onerror = () => reject(new Error("Failed to load image for JPG"));
        img.src = imgSrc;
    });
}
