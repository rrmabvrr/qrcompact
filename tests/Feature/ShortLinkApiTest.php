<?php

namespace Tests\Feature;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertJsonStructure(['slug', 'shortUrl', 'qrCodeDataUrl', 'message']);

        $this->assertDatabaseCount('links', 1);
    }

    public function test_it_redirects_to_the_original_url(): void
    {
        $link = Link::query()->create([
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
}
