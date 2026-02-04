<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CurrencyService
{
    /**
     * Convierte una cantidad desde una moneda a EUR usando primero exchangerate.host,
     * y si la respuesta no es válida, hace fallback a frankfurter.app.
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

        // 1) Intento: exchangerate.host (no key normalmente)
        try {
            $params = [
                'from'   => $currency,
                'to'     => 'EUR',
                'amount' => $amount,
                'date'   => $date->format('Y-m-d'),
            ];

            $response = Http::timeout(6)->get('https://api.exchangerate.host/convert', $params);

            if ($response->successful()) {
                $data = $response->json();

                // Validar payload esperado
                if (isset($data['result']) && isset($data['info']['rate'])) {
                    return [
                        'importe_euros' => round((float)$data['result'], 2),
                        'tipo_cambio'   => (float)$data['info']['rate'],
                        'info'          => $data,
                    ];
                }

                // si viene success:false o payload inesperado, lo logueamos y seguimos a fallback
                Log::warning('CurrencyService: unexpected payload from exchangerate.host', ['body' => $data]);
            } else {
                Log::warning('CurrencyService: exchangerate.host non-success status', ['status'=>$response->status(), 'body'=>$response->body()]);
            }
        } catch (\Throwable $e) {
            Log::warning('CurrencyService: exchangerate.host exception: ' . $e->getMessage());
        }

        // 2) Fallback: frankfurter.app (sin API key)
        try {
            // frankfurter convert: /latest?amount=125&from=CZK&to=EUR
            $params = [
                'amount' => $amount,
                'from'   => $currency,
                'to'     => 'EUR',
            ];

            $response = Http::timeout(6)->get('https://api.frankfurter.app/latest', $params);

            if ($response->successful()) {
                $data = $response->json();
                // formato: { amount:..., base: 'CZK', date:'YYYY-MM-DD', rates: { EUR: 5.12 } }
                if (isset($data['rates']['EUR'])) {
                    $importe_euros = (float) $data['rates']['EUR'];
                    $tipo_cambio = $importe_euros / (float)$amount;
                    return [
                        'importe_euros' => round($importe_euros, 2),
                        'tipo_cambio'   => round($tipo_cambio, 6),
                        'info'          => $data,
                    ];
                }

                Log::warning('CurrencyService: unexpected payload from frankfurter', ['body' => $data]);
            } else {
                Log::warning('CurrencyService: frankfurter non-success status', ['status'=>$response->status(), 'body'=>$response->body()]);
            }
        } catch (\Throwable $e) {
            Log::error('CurrencyService: frankfurter exception: '.$e->getMessage());
        }

        // Si todo falla devolvemos nulls (tu código debe manejar estos casos)
        return ['importe_euros' => null, 'tipo_cambio' => null, 'info' => null];
    }
}
