<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use App\Services\SafeBrowsingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use Tests\TestCase;

class ShortLinkApiTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(string $email = 'user@example.com'): User
    {
        $user = User::query()->create([
            'email' => $email,
            'password' => 'segura123',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function test_it_registers_a_new_user_with_email_and_password(): void
    {
        $response = $this->post('/cadastro/senha', [
            'email' => 'novo@example.com',
            'password' => 'segura123',
            'password_confirmation' => 'segura123',
            '_panel' => 'register',
        ]);

        $response->assertRedirect(route('links.index'));

        $user = User::query()->where('email', 'novo@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('segura123', $user->password));
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_it_does_not_register_an_existing_email(): void
    {
        $existingUser = User::query()->create([
            'email' => 'existente@example.com',
            'password' => 'segura123',
            'email_verified_at' => now(),
        ]);

        $response = $this->from(route('login'))->post('/cadastro/senha', [
            'email' => 'existente@example.com',
            'password' => 'outrasenha123',
            'password_confirmation' => 'outrasenha123',
            '_panel' => 'register',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);

        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
        $this->assertEquals($existingUser->id, User::query()->first()->id);
    }

    public function test_it_shortens_a_valid_url(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/shorten', [
            'url' => 'https://example.com/produto',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('originalUrl', 'https://example.com/produto')
            ->assertJsonStructure(['name', 'slug', 'shortUrl', 'qrCodeDataUrl', 'qrCodeSvgDataUrl', 'message']);

        $this->assertDatabaseCount('links', 1);
        $link = \App\Models\Link::first();
        $this->assertEquals($link->slug, $link->name);
        $this->assertEquals($user->id, $link->user_id);
    }

    public function test_it_shortens_with_custom_name(): void
    {
        $user = $this->createAuthenticatedUser('owner@example.com');

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
        $this->assertEquals($user->id, $link->user_id);
    }

    public function test_it_redirects_to_the_original_url(): void
    {
        $owner = User::query()->create([
            'email' => 'owner-redirect@example.com',
            'password' => 'segura123',
            'email_verified_at' => now(),
        ]);

        $link = Link::query()->create([
            'user_id' => $owner->id,
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
        $owner = $this->createAuthenticatedUser('owner-update@example.com');

        Link::query()->create([
            'user_id' => $owner->id,
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
            'user_id' => $owner->id,
        ]);
    }

    public function test_it_lists_only_links_from_authenticated_user(): void
    {
        $owner = $this->createAuthenticatedUser('owner-list@example.com');
        $otherUser = User::query()->create([
            'email' => 'other-list@example.com',
            'password' => 'segura123',
            'email_verified_at' => now(),
        ]);

        Link::query()->create([
            'user_id' => $owner->id,
            'name' => 'Link do dono',
            'slug' => 'Ow12Er',
            'original_url' => 'https://example.com/owner',
        ]);

        Link::query()->create([
            'user_id' => $otherUser->id,
            'name' => 'Link de outro',
            'slug' => 'Ot34Re',
            'original_url' => 'https://example.com/other',
        ]);

        $response = $this->getJson('/api/links');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.slug', 'Ow12Er');
    }

    public function test_it_does_not_show_link_from_another_user(): void
    {
        $owner = User::query()->create([
            'email' => 'owner-show@example.com',
            'password' => 'segura123',
            'email_verified_at' => now(),
        ]);

        $this->createAuthenticatedUser('viewer-show@example.com');

        Link::query()->create([
            'user_id' => $owner->id,
            'name' => 'Link privado',
            'slug' => 'Pr12Iv',
            'original_url' => 'https://example.com/private',
        ]);

        $this->getJson('/api/links/Pr12Iv')
            ->assertStatus(404)
            ->assertJsonPath('message', 'Link curto nao encontrado');
    }

    public function test_it_does_not_update_link_from_another_user(): void
    {
        $owner = User::query()->create([
            'email' => 'owner-edit@example.com',
            'password' => 'segura123',
            'email_verified_at' => now(),
        ]);

        $this->createAuthenticatedUser('viewer-edit@example.com');

        Link::query()->create([
            'user_id' => $owner->id,
            'name' => 'Link privado',
            'slug' => 'Ed12It',
            'original_url' => 'https://example.com/private-edit',
        ]);

        $this->putJson('/api/links/Ed12It', [
            'url' => 'https://example.com/hijack',
        ])
            ->assertStatus(404)
            ->assertJsonPath('message', 'Link curto nao encontrado');

        $this->assertDatabaseHas('links', [
            'slug' => 'Ed12It',
            'original_url' => 'https://example.com/private-edit',
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
            ->assertJsonStructure(['payload', 'qrCodeDataUrl', 'qrCodeSvgDataUrl']);

        $payload = $response->json('payload');

        $this->assertStringContainsString('0014BR.GOV.BCB.PIX', $payload);
        $this->assertStringContainsString('540519.90', $payload);
        $this->assertMatchesRegularExpression('/6304[0-9A-F]{4}$/', $payload);
    }

    public function test_it_requires_pix_fields_in_pix_mode(): void
    {
        $response = $this->postJson('/api/qr', [
            'mode' => 'pix',
            'key_type' => 'phone',
            'key' => '(11) 99999-9999',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'city']);
    }

    public function test_it_rejects_invalid_pix_key_type(): void
    {
        $response = $this->postJson('/api/qr', [
            'mode' => 'pix',
            'key_type' => 'invalid',
            'key' => '123',
            'name' => 'Jose da Silva',
            'city' => 'Sao Paulo',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['key_type']);
    }

    public function test_it_rejects_invalid_monetary_amount_in_pix_mode(): void
    {
        $response = $this->postJson('/api/qr', [
            'mode' => 'pix',
            'key_type' => 'phone',
            'key' => '(11) 99999-9999',
            'name' => 'Jose da Silva',
            'city' => 'Sao Paulo',
            'amount' => 'valor-invalido',
            'txid' => 'PEDIDO123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'Informe um valor monetario valido.');
    }

    public function test_it_rejects_pix_key_without_digits_for_phone_type(): void
    {
        $response = $this->postJson('/api/qr', [
            'mode' => 'pix',
            'key_type' => 'phone',
            'key' => 'telefone-invalido',
            'name' => 'Jose da Silva',
            'city' => 'Sao Paulo',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'Informe uma chave Pix valida.');
    }

    public function test_it_sanitizes_and_truncates_pix_txid(): void
    {
        $response = $this->postJson('/api/qr', [
            'mode' => 'pix',
            'key_type' => 'phone',
            'key' => '(11) 99999-9999',
            'name' => 'Jose da Silva',
            'city' => 'Sao Paulo',
            'txid' => 'PEDIDO-123_ABC!@#LONG-LONG-LONG-XYZ',
        ]);

        $response->assertOk();

        $payload = $response->json('payload');

        $this->assertStringContainsString('PEDIDO123ABCLONGLONGLONGX', $payload);
    }

    public function test_it_uses_default_txid_when_empty(): void
    {
        $response = $this->postJson('/api/qr', [
            'mode' => 'pix',
            'key_type' => 'phone',
            'key' => '(11) 99999-9999',
            'name' => 'Jose da Silva',
            'city' => 'Sao Paulo',
            'txid' => '',
        ]);

        $response->assertOk();

        $payload = $response->json('payload');

        $this->assertStringContainsString('0503***', $payload);
    }

    public function test_it_blocks_dangerous_urls_when_shortening(): void
    {
        $this->createAuthenticatedUser('security@example.com');

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
