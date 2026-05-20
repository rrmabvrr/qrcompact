<?php

namespace App\Services;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinkService
{
    public function latestForUser(User $user): Collection
    {
        return Link::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(100)
            ->get();
    }

    public function createForUser(User $user, ?string $name, string $originalUrl): Link
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            try {
                $slug = $this->generateSlug();
                return Link::query()->create([
                    'user_id' => $user->id,
                    'name' => $name ?: $slug,
                    'slug' => $slug,
                    'original_url' => $originalUrl,
                ]);
            } catch (QueryException $exception) {
                if (! $this->isUniqueSlugViolation($exception)) {
                    throw $exception;
                }
            }
        }

        abort(500, 'Nao foi possivel gerar um slug unico apos 10 tentativas.');
    }

    public function updateForUser(User $user, string $slug, ?string $name, string $originalUrl): Link
    {
        $link = $this->findBySlugForUserOrFail($user, $slug);
        $link->update([
            'name' => $name ?: $slug,
            'original_url' => $originalUrl,
        ]);

        return $link->refresh();
    }

    public function findBySlugForUserOrFail(User $user, string $slug): Link
    {
        $link = Link::query()
            ->where('user_id', $user->id)
            ->where('slug', $slug)
            ->first();

        if (! $link) {
            throw new NotFoundHttpException('Link curto nao encontrado');
        }

        return $link;
    }

    public function findBySlugOrFail(string $slug): Link
    {
        $link = Link::query()->where('slug', $slug)->first();

        if (! $link) {
            throw new NotFoundHttpException('Link curto nao encontrado');
        }

        return $link;
    }

    public function shortUrl(Link $link): string
    {
        return route('links.redirect', ['slug' => $link->slug]);
    }

    public function incrementClickCount(Link $link): void
    {
        $link->increment('click_count');
    }

    public function serialize(Link $link): array
    {
        return [
            'id' => $link->id,
            'name' => $link->name,
            'slug' => $link->slug,
            'originalUrl' => $link->original_url,
            'shortUrl' => $this->shortUrl($link),
            'clickCount' => (int) $link->click_count,
            'createdAt' => optional($link->created_at)?->toIso8601String(),
            'updatedAt' => optional($link->updated_at)?->toIso8601String(),
        ];
    }

    private function generateSlug(): string
    {
        return Str::random(6);
    }

    private function isUniqueSlugViolation(QueryException $exception): bool
    {
        return str_contains(Str::lower($exception->getMessage()), 'links.slug')
            || str_contains(Str::lower($exception->getMessage()), 'unique')
            || $exception->getCode() === '23000';
    }
}
