<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Services\SafeBrowsingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ShortLinkApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shortens_a_valid_url(): void
    {
        $response = $this->postJson('/api/shorten', [
            'url' => 'https://example.com/produto',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('originalUrl', 'https://example.com/produto')
            ->assertJsonStructure(['name', 'slug', 'shortUrl', 'qrCodeDataUrl', 'message']);

        $this->assertDatabaseCount('links', 1);
        $link = \App\Models\Link::first();
        $this->assertEquals($link->slug, $link->name);
    }

    public function test_it_shortens_with_custom_name(): void
    {
        $response = $this->postJson('/api/shorten', [
            'name' => 'Meu link especial',
            'url' => 'https://example.com/outro',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Meu link especial')
            ->assertJsonPath('originalUrl', 'https://example.com/outro');

        $this->assertDatabaseCount('links', 1);
        $link = \App\Models\Link::first();
        $this->assertEquals('Meu link especial', $link->name);
    }

    public function test_it_redirects_to_the_original_url(): void
    {
        $link = Link::query()->create([
            'name' => 'Destino principal',
            'slug' => 'Ab12Cd',
            'original_url' => 'https://example.com/destino',
        ]);

        $this->get('/' . $link->slug)
            ->assertRedirect('https://example.com/destino');

        $this->assertDatabaseHas('links', [
            'slug' => 'Ab12Cd',
            'click_count' => 1,
        ]);
    }

    public function test_it_updates_a_link_destination(): void
    {
        Link::query()->create([
            'name' => 'Link antigo',
            'slug' => 'Xy98Za',
            'original_url' => 'https://example.com/antigo',
        ]);

        $response = $this->putJson('/api/links/Xy98Za', [
            'url' => 'https://example.com/novo',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('originalUrl', 'https://example.com/novo')
            ->assertJsonPath('message', 'Link atualizado com sucesso.');

        $this->assertDatabaseHas('links', [
            'slug' => 'Xy98Za',
            'original_url' => 'https://example.com/novo',
        ]);
    }

    public function test_it_generates_pix_payload_and_qr_code(): void
    {
        $response = $this->postJson('/api/qr', [
            'mode' => 'pix',
            'key_type' => 'phone',
            'key' => '(11) 99999-9999',
            'name' => 'Jose da Silva',
            'city' => 'Sao Paulo',
            'amount' => '19,90',
            'txid' => 'PEDIDO123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('mode', 'pix')
            ->assertJsonPath('message', 'Payload Pix e QR Code gerados com sucesso.')
            ->assertJsonStructure(['payload', 'qrCodeDataUrl']);

        $payload = $response->json('payload');

        $this->assertStringContainsString('0014BR.GOV.BCB.PIX', $payload);
        $this->assertStringContainsString('540519.90', $payload);
        $this->assertMatchesRegularExpression('/6304[0-9A-F]{4}$/', $payload);
    }

    public function test_it_blocks_dangerous_urls_when_shortening(): void
    {
        $this->mock(SafeBrowsingService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isSafe')
                ->once()
                ->with('https://malicious.example/phishing')
                ->andReturn(false);
        });

        $response = $this->postJson('/api/shorten', [
            'url' => 'https://malicious.example/phishing',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['url']);

        $this->assertDatabaseCount('links', 0);
    }
}
