<?php

namespace App\Services;

use Illuminate\Support\Str;
use InvalidArgumentException;

class PixPayloadService
{
    public function buildPayload(array $data): string
    {
        $key = $this->formatKey($data['key_type'] ?? '', (string) ($data['key'] ?? ''));
        if ($key === '') {
            throw new InvalidArgumentException('Informe uma chave Pix valida.');
        }

        $name = $this->sanitizeText((string) ($data['name'] ?? ''), 25);
        if ($name === '') {
            throw new InvalidArgumentException('Informe o nome do beneficiario.');
        }

        $city = $this->sanitizeText((string) ($data['city'] ?? ''), 15);
        if ($city === '') {
            throw new InvalidArgumentException('Informe a cidade do beneficiario.');
        }

        $merchantAccount = $this->tlv(
            '26',
            $this->tlv('00', 'BR.GOV.BCB.PIX') .
                $this->tlv('01', $key)
        );

        $amountField = '';
        $amount = $this->normalizeAmount($data['amount'] ?? null);
        if ($amount !== null && $amount > 0) {
            $amountField = $this->tlv('54', number_format($amount, 2, '.', ''));
        }

        $txid = preg_replace('/[^A-Za-z0-9]/', '', (string) ($data['txid'] ?? '')) ?: '***';
        $txid = substr($txid, 0, 25);

        $payload =
            $this->tlv('00', '01') .
            $merchantAccount .
            $this->tlv('52', '0000') .
            $this->tlv('53', '986') .
            $amountField .
            $this->tlv('58', 'BR') .
            $this->tlv('59', $name) .
            $this->tlv('60', $city) .
            $this->tlv('62', $this->tlv('05', $txid)) .
            '6304';

        return $payload . $this->crc16Ccitt($payload);
    }

    private function formatKey(string $type, string $rawKey): string
    {
        $key = trim($rawKey);

        return match ($type) {
            'phone' => $this->formatPhone($key),
            'cpf', 'cnpj' => preg_replace('/\D+/', '', $key) ?? '',
            'email' => Str::lower($key),
            'random' => $key,
            default => '',
        };
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (! str_starts_with($digits, '55')) {
            $digits = '55' . $digits;
        }

        return '+' . $digits;
    }

    private function sanitizeText(string $value, int $limit): string
    {
        $sanitized = Str::upper(Str::ascii($value));
        $sanitized = preg_replace('/[^A-Z0-9 ]+/', ' ', $sanitized) ?? '';
        $sanitized = preg_replace('/\s+/', ' ', $sanitized) ?? '';
        $sanitized = trim($sanitized);

        return mb_substr($sanitized, 0, $limit);
    }

    private function normalizeAmount(mixed $amount): ?float
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $normalized = str_replace(',', '.', trim((string) $amount));
        if (! is_numeric($normalized)) {
            throw new InvalidArgumentException('Informe um valor monetario valido.');
        }

        return (float) $normalized;
    }

    private function tlv(string $id, string $value): string
    {
        return $id . str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    private function crc16Ccitt(string $payload): string
    {
        $crc = 0xFFFF;

        foreach (str_split($payload) as $char) {
            $crc ^= ord($char) << 8;

            for ($index = 0; $index < 8; $index++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
