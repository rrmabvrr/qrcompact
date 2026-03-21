<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Services\LinkService;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinkController extends Controller
{
    public function __construct(
        private readonly LinkService $linkService,
        private readonly QrCodeService $qrCodeService,
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
        $link = $this->linkService->create($request->validated('url'));
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
        try {
            $link = $this->linkService->update($slug, $request->validated('url'));
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
}
