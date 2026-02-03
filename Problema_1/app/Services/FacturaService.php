<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Cuota;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturaMail;
use Carbon\Carbon;

class FacturaService
{
    public function generarFacturaDesdeCuota(int $cuotaId, bool $enviarEmail = true): Factura
    {
        $cuota = Cuota::with('cliente')->findOrFail($cuotaId);
        $cliente = $cuota->cliente;

        $numero = 'FAC-' . now()->format('YmdHis') . '-' . $cuota->id;

        $data = [
            'cuota' => $cuota,
            'cliente' => $cliente,
            'numero' => $numero,
            'fecha' => now()->toDateString(),
        ];

        $pdf = Pdf::loadView('facturas.pdf', $data)->setPaper('A4', 'portrait');

        $filename = 'factura_' . Str::slug($cliente->nombre) . '_' . $numero . '.pdf';
        $path = 'facturas/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        // Guardar moneda de la cuota (fallback al cliente o EUR)
        $moneda = $cuota->moneda ?? ($cliente->moneda ?? 'EUR');

        $factura = Factura::create([
            'cuota_id' => $cuota->id,
            'numero_factura' => $numero,
            'fecha' => now()->toDateString(),
            'pdf_path' => $path,
            'moneda' => $moneda,
            'pagada' => false,
        ]);

        if ($enviarEmail && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(new FacturaMail($factura));
            } catch (\Exception $e) {
                \Log::error('Error enviando factura: ' . $e->getMessage());
            }
        }

        return $factura;
    }

    public function marcarComoPagada(Factura $factura): void
    {
        if ($factura->pagada) {
            return;
        }

        $cuota = $factura->cuota()->with('cliente')->first();
        $cliente = $cuota->cliente ?? null;

        $moneda = $factura->moneda ?? $cuota->moneda ?? ($cliente->moneda ?? 'EUR');
        $importeLocal = $cuota->importe;

        $currencySvc = app(\App\Services\CurrencyService::class);

        if ($moneda === 'EUR') {
            $importeEuros = round($importeLocal, 2);
            $tipoCambio = 1.0;
        } else {
            $res = $currencySvc->convertToEUR((float)$importeLocal, $moneda, Carbon::now());
            $importeEuros = $res['importe_euros'] ?? null;
            $tipoCambio = $res['tipo_cambio'] ?? null;
        }

        $factura->update([
            'pagada' => true,
            'moneda' => $moneda,
            'importe_euros' => $importeEuros,
            'tipo_cambio' => $tipoCambio,
            'fecha_pago' => now(),
        ]);

        // mantener consistencia: marcar la cuota como pagada también
        try {
            $cuota->update(['pagada' => true, 'fecha_pago' => now()]);
        } catch (\Throwable $e) {
            \Log::warning('No se ha podido actualizar la cuota tras marcar factura pagada: '.$e->getMessage());
        }
    }
}
