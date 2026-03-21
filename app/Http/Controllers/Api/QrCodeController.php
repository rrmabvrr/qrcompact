<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateQrCodeRequest;
use App\Services\PixPayloadService;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class QrCodeController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService,
        private readonly PixPayloadService $pixPayloadService,
    ) {}

    public function __invoke(GenerateQrCodeRequest $request): JsonResponse
    {
        if ($request->isPixMode()) {
            try {
                $payload = $this->pixPayloadService->buildPayload($request->pixData());
            } catch (InvalidArgumentException $exception) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 422);
            }

            return response()->json([
                'mode' => 'pix',
                'payload' => $payload,
                'qrCodeDataUrl' => $this->qrCodeService->toDataUri($payload, 300),
                'message' => 'Payload Pix e QR Code gerados com sucesso.',
            ]);
        }

        $data = trim($request->validated('data'));
        if ($data === '') {
            return response()->json([
                'message' => 'Informe os dados para gerar o QR Code.',
            ], 422);
        }

        return response()->json([
            'mode' => 'generic',
            'qrCodeDataUrl' => $this->qrCodeService->toDataUri($data, 300),
            'message' => 'QR Code gerado com sucesso.',
        ]);
    }
}
