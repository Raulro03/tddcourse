<?php

namespace App;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class PaddleSignatureValidator implements SignatureValidator
{
    public const SIGNATURE_HEADER = 'Paddle-Signature';

    public const HASH_ALGORITHM_1 = 'h1';

    protected ?int $maximumVariance = 5;

    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $request->header(self::SIGNATURE_HEADER);

        if (empty($signature)) {
            return false;
        }

        [$timestamp, $hashes] = $this->parseSignature($signature);

        if ($this->maximumVariance > 0 && time() > $timestamp + $this->maximumVariance) {
            return false;
        }

        $secret = config('services.paddle.notification-endpoint-secret-key');
        $data = $request->getContent();

        foreach ($hashes as $hashAlgorithm => $possibleHashes) {
            $hash = match ($hashAlgorithm) {
                'h1' => hash_hmac('sha256', "{$timestamp}:{$data}", $secret),
            };

            foreach ($possibleHashes as $possibleHash) {
                if (hash_equals($hash, $possibleHash)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Parse the signature header.
     */
    public function parseSignature(string $header): array
    {
        $components = [
            'ts' => 0,
            'hashes' => [],
        ];

        foreach (explode(';', $header) as $part) {
            if (str_contains($part, '=')) {
                [$key, $value] = explode('=', $part, 2);

                match ($key) {
                    'ts' => $components['ts'] = (int) $value,
                    'h1' => $components['hashes']['h1'][] = $value,
                };
            }
        }

        return [
            $components['ts'],
            $components['hashes'],
        ];
    }
}
