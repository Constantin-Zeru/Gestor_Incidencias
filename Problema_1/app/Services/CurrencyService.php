<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CurrencyService
{
    /**
     * Convierte una cantidad desde una moneda a EUR usando exchangerate.host.
     *
     * @param float $amount
     * @param string $currency ISO code (e.g. USD)
     * @param Carbon|null $date fecha para la tasa (opcional)
     * @return array ['importe_euros'=>float|null, 'tipo_cambio'=>float|null, 'info'=>array|null]
     */
    public function convertToEUR(float $amount, string $currency, ?Carbon $date = null): array
    {
        $currency = strtoupper(trim($currency));
        $date = $date ?? now();

        if ($currency === 'EUR') {
            return [
                'importe_euros' => round($amount, 2),
                'tipo_cambio' => 1.0,
                'info' => ['note' => 'same currency'],
            ];
        }

        try {
            $params = [
                'from' => $currency,
                'to' => 'EUR',
                'amount' => $amount,
                'date' => $date->format('Y-m-d'),
            ];

            $response = Http::timeout(6)->get('https://api.exchangerate.host/convert', $params);

            if (! $response->successful()) {
                Log::warning('CurrencyService: non-success response', ['status' => $response->status(), 'body' => $response->body()]);
                return ['importe_euros' => null, 'tipo_cambio' => null, 'info' => null];
            }

            $data = $response->json();

            if (isset($data['result']) && isset($data['info']['rate'])) {
                return [
                    'importe_euros' => round((float)$data['result'], 2),
                    'tipo_cambio' => (float)($data['info']['rate'] ?? 0),
                    'info' => $data,
                ];
            }

            Log::warning('CurrencyService: unexpected payload', ['body' => $data]);
        } catch (\Throwable $e) {
            Log::error('CurrencyService exception: '.$e->getMessage());
        }

        return ['importe_euros' => null, 'tipo_cambio' => null, 'info' => null];
    }
}
