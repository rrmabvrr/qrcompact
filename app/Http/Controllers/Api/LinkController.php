<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Services\LinkService;
use App\Services\QrCodeService;
use App\Services\SafeBrowsingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinkController extends Controller
{
    public function __construct(
        private readonly LinkService $linkService,
        private readonly QrCodeService $qrCodeService,
        private readonly SafeBrowsingService $safeBrowsingService,
    ) {}

    public function index(): JsonResponse
    {
        $links = $this->linkService->latest();

        return response()->json(
            $links->map(fn($link) => $this->linkService->serialize($link))->all()
        );
    }

    public function store(StoreLinkRequest $request): JsonResponse
    {
        $name = $request->validated('name') ?? '';
        $url = $request->validated('url');
        $safeBrowsingError = $this->validateSafeBrowsing($url);

        if ($safeBrowsingError instanceof JsonResponse) {
            return $safeBrowsingError;
        }

        $link = $this->linkService->create($name, $url);
        $shortUrl = $this->linkService->shortUrl($link);

        return response()->json([
            ...$this->linkService->serialize($link),
            'qrCodeDataUrl' => $this->qrCodeService->toDataUri($shortUrl, 260),
            'message' => 'Link curto criado com sucesso.',
        ], 201);
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $link = $this->linkService->findBySlugOrFail($slug);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => 'Link curto nao encontrado',
            ], 404);
        }

        $shortUrl = $this->linkService->shortUrl($link);

        return response()->json([
            ...$this->linkService->serialize($link),
            'qrCodeDataUrl' => $this->qrCodeService->toDataUri($shortUrl, 260),
        ]);
    }

    public function update(UpdateLinkRequest $request, string $slug): JsonResponse
    {
        $name = $request->validated('name') ?? '';
        $url = $request->validated('url');
        $safeBrowsingError = $this->validateSafeBrowsing($url);

        if ($safeBrowsingError instanceof JsonResponse) {
            return $safeBrowsingError;
        }

        try {
            $link = $this->linkService->update($slug, $name, $url);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => 'Link curto nao encontrado',
            ], 404);
        }

        return response()->json([
            ...$this->linkService->serialize($link),
            'message' => 'Link atualizado com sucesso.',
        ]);
    }

    private function validateSafeBrowsing(string $url): ?JsonResponse
    {
        if ($this->safeBrowsingService->isSafe($url)) {
            return null;
        }

        return response()->json([
            'message' => 'A URL informada foi classificada como perigosa.',
            'errors' => [
                'url' => ['A URL informada foi classificada como perigosa e nao pode ser encurtada.'],
            ],
        ], 422);
    }
}
