<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SafeBrowsingService
{
    private string $apiKey;
    private string $apiUrl = 'https://safebrowsing.googleapis.com/v4/threatMatches:find';

    public function __construct()
    {
        $this->apiKey = config('services.google_safe_browsing.key') ?? '';
    }

    /**
     * Verifica se uma URL é considerada perigosa pelo Google.
     *
     * @param string $url
     * @return bool Retorna true se a URL for segura, false se for perigosa.
     */
    public function isSafe(string $url): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('Google Safe Browsing API Key não configurada. Pulando verificação.');
            return true;
        }

        try {
            $response = Http::post("{$this->apiUrl}?key={$this->apiKey}", [
                'client' => [
                    'clientId' => config('app.name'),
                    'clientVersion' => '1.0.0',
                ],
                'threatInfo' => [
                    'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'],
                    'platformTypes' => ['ANY_PLATFORM'],
                    'threatEntryTypes' => ['URL'],
                    'threatEntries' => [
                        ['url' => $url],
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Erro ao consultar Google Safe Browsing API', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return true; // Em caso de erro na API, assumimos como seguro para não bloquear o serviço
            }

            $data = $response->json();

            // Se o campo 'matches' existir, significa que a URL foi encontrada em alguma lista de ameaças
            return empty($data['matches']);
        } catch (\Exception $e) {
            Log::error('Exceção ao consultar Google Safe Browsing API: ' . $e->getMessage());
            return true;
        }
    }
}
