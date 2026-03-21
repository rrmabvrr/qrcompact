<?php

namespace App\Http\Controllers;

use App\Services\LinkService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectController extends Controller
{
    public function __construct(private readonly LinkService $linkService) {}

    public function __invoke(string $slug): RedirectResponse
    {
        try {
            $link = $this->linkService->findBySlugOrFail($slug);
        } catch (NotFoundHttpException $exception) {
            abort(404, 'Link curto nao encontrado');
        }

        return redirect()->away($link->original_url, 302);
    }
}
