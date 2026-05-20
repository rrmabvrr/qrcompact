import { describe, expect, it, vi } from "vitest";
import {
    dataURLtoBlob,
    decodeSvgDataUrl,
    downloadBlob,
    getJpgBlob,
    getPngBlob,
    svgDataUrlToBlob,
} from "../qr-downloads";

describe("qr-downloads", () => {
    it("converts PNG data URL to blob", () => {
        const pngDataUrl =
            "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAgMBgGf8W4UAAAAASUVORK5CYII=";

        const blob = dataURLtoBlob(pngDataUrl);

        expect(blob.type).toBe("image/png");
        expect(blob.size).toBeGreaterThan(0);
    });

    it("decodes SVG data URL in base64", () => {
        const svg = '<svg xmlns="http://www.w3.org/2000/svg"></svg>';
        const svgDataUrl = `data:image/svg+xml;base64,${btoa(svg)}`;

        const decoded = decodeSvgDataUrl(svgDataUrl);

        expect(decoded).toBe(svg);
    });

    it("decodes SVG data URL in URI format", () => {
        const svg =
            '<svg xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1"/></svg>';
        const svgDataUrl = `data:image/svg+xml,${encodeURIComponent(svg)}`;

        const decoded = decodeSvgDataUrl(svgDataUrl);

        expect(decoded).toBe(svg);
    });

    it("creates SVG blob with proper mime type", () => {
        const svg = '<svg xmlns="http://www.w3.org/2000/svg"></svg>';
        const svgDataUrl = `data:image/svg+xml;base64,${btoa(svg)}`;

        const blob = svgDataUrlToBlob(svgDataUrl);

        expect(blob.type).toBe("image/svg+xml");
        expect(blob.size).toBeGreaterThan(0);
    });

    it("gets PNG blob from remote URL using fetch", async () => {
        const expectedBlob = new Blob(["png"], { type: "image/png" });
        const fetchImpl = vi.fn().mockResolvedValue({
            blob: () => Promise.resolve(expectedBlob),
        });

        const blob = await getPngBlob("https://example.com/qr.png", {
            fetchImpl,
        });

        expect(fetchImpl).toHaveBeenCalledWith("https://example.com/qr.png");
        expect(blob).toBe(expectedBlob);
    });

    it("gets PNG blob from data URL without fetch", async () => {
        const pngDataUrl =
            "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAgMBgGf8W4UAAAAASUVORK5CYII=";
        const fetchImpl = vi.fn();

        const blob = await getPngBlob(pngDataUrl, { fetchImpl });

        expect(fetchImpl).not.toHaveBeenCalled();
        expect(blob.type).toBe("image/png");
    });

    it("converts image source to JPG blob", async () => {
        const fakeImage = {
            width: 40,
            height: 40,
            onload: null,
            onerror: null,
            set src(_) {
                this.onload();
            },
        };

        const fakeCanvas = {
            width: 0,
            height: 0,
            getContext: () => ({
                fillStyle: "",
                fillRect: vi.fn(),
                drawImage: vi.fn(),
            }),
            toBlob: (callback, type) => {
                callback(new Blob(["jpg"], { type }));
            },
        };

        const blob = await getJpgBlob("data:image/png;base64,abc", {
            createImage: () => fakeImage,
            createCanvas: () => fakeCanvas,
        });

        expect(blob.type).toBe("image/jpeg");
    });

    it("triggers browser download via anchor element", () => {
        const click = vi.fn();
        const anchor = {
            href: "",
            download: "",
            click,
        };

        const documentRef = {
            body: {
                appendChild: vi.fn(),
                removeChild: vi.fn(),
            },
            createElement: vi.fn(() => anchor),
        };

        const urlRef = {
            createObjectURL: vi.fn(() => "blob:test"),
            revokeObjectURL: vi.fn(),
        };

        const schedule = vi.fn((fn) => fn());

        downloadBlob(new Blob(["x"]), "qrcode.svg", {
            documentRef,
            urlRef,
            schedule,
        });

        expect(documentRef.createElement).toHaveBeenCalledWith("a");
        expect(anchor.download).toBe("qrcode.svg");
        expect(click).toHaveBeenCalledTimes(1);
        expect(urlRef.revokeObjectURL).toHaveBeenCalledWith("blob:test");
    });
});
