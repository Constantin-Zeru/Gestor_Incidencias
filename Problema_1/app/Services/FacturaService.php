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
use Illuminate\Support\Facades\Log;

class FacturaService
{
    /**
     * Genera (crea) una factura a partir de una cuota.
     * Acepta $cuota como objeto App\Models\Cuota o como id (int).
     * Crea la fila en BD (con pdf_path inicial), genera el PDF y lo actualiza.
     *
     * @param  \App\Models\Cuota|int $cuotaOrId
     * @param  bool $enviarEmail
     * @return \App\Models\Factura
     */
    public function generarFacturaDesdeCuota($cuotaOrId, bool $enviarEmail = true): Factura
    {
        $cuota = $cuotaOrId instanceof Cuota ? $cuotaOrId : Cuota::with('cliente')->findOrFail($cuotaOrId);
        $cliente = $cuota->cliente;

        $numero = $this->generarNumero();

        // filename y path previos para evitar NOT NULL constraint en pdf_path
        $filename = 'factura_' . Str::slug($cliente->nombre ?? 'cliente') . '_' . $numero . '.pdf';
        $path = 'facturas/' . $filename;

        $factura = Factura::create([
            'cuota_id' => $cuota->id,
            'numero_factura' => $numero,
            'fecha' => now()->toDateString(),
            'pdf_path' => $path,
            'moneda' => $cuota->moneda ?? ($cliente->moneda ?? 'EUR'),
            'pagada' => false,
        ]);

        // Generar PDF (incluso aunque no esté pagada; PDF con importe local)
        try {
            $this->generarPdf($factura);
        } catch (\Throwable $e) {
            Log::error('FacturaService: error al generar PDF inicial: '.$e->getMessage());
            // no rompemos la creación si falla el PDF — pero lo logueamos
        }

        // enviar correo si procede
        if ($enviarEmail && $cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(new FacturaMail($factura));
            } catch (\Throwable $e) {
                Log::error('Error enviando factura: ' . $e->getMessage());
            }
        }

        return $factura;
    }

    /**
     * Alias/compatibilidad si en algún sitio se llama crearDesdeCuota
     */
    public function crearDesdeCuota($cuotaOrId): Factura
    {
        return $this->generarFacturaDesdeCuota($cuotaOrId, false);
    }

    /**
     * Marca la factura como pagada: obtiene la cuota, hace la conversión (si procede),
     * guarda importe_euros/tipo_cambio/fecha_pago, marca cuota como pagada y regenera el PDF.
     *
     * @param \App\Models\Factura $factura
     * @return void
     */
    public function marcarComoPagada(Factura $factura): void
    {
        if ($factura->pagada) {
            return;
        }

        $cuota = $factura->cuota()->with('cliente')->first();

        $cliente = $cuota->cliente ?? null;
        $moneda = $cuota->moneda ?? $factura->moneda ?? ($cliente->moneda ?? 'EUR');
        $importeLocal = (float)$cuota->importe;

        $currencySvc = app(\App\Services\CurrencyService::class);

        if ($moneda === 'EUR') {
            $importeEuros = round($importeLocal, 2);
            $tipoCambio = 1.0;
        } else {
            $res = $currencySvc->convertToEUR($importeLocal, $moneda, Carbon::now());
            $importeEuros = $res['importe_euros'] ?? null;
            $tipoCambio = $res['tipo_cambio'] ?? null;
        }

        $factura->update([
            'pagada' => true,
            'moneda' => $moneda,               // moneda original
            'importe_euros' => $importeEuros,
            'tipo_cambio' => $tipoCambio,
            'fecha_pago' => now(),
        ]);

        // Marcar cuota como pagada también (consistencia)
        try {
            $cuota->update(['pagada' => true, 'fecha_pago' => now()]);
        } catch (\Throwable $e) {
            Log::warning('No se pudo marcar cuota pagada tras factura: '.$e->getMessage());
        }

        // Regenerar PDF para incluir importe_euros y tipo_cambio
        try {
            $this->generarPdf($factura);
        } catch (\Throwable $e) {
            Log::error('FacturaService: error al regenerar PDF tras marcar pagada: '.$e->getMessage());
        }
    }

    /**
     * Genera/actualiza el PDF de una factura (usa la vista facturas.pdf).
     */
    private function generarPdf(Factura $factura): void
    {
        // recargar relaciones para asegurar datos actualizados
        $factura = $factura->fresh('cuota.cliente');
        $cuota = $factura->cuota;
        $cliente = $cuota->cliente ?? null;

        $data = [
            'factura' => $factura,
            'cuota'   => $cuota,
            'cliente' => $cliente,
        ];

        $pdf = Pdf::loadView('facturas.pdf', $data)->setPaper('A4', 'portrait');

        // Guardar en disco público (asegúrate que disk 'public' esté configurado)
        $path = $factura->pdf_path ?: 'facturas/factura_' . $factura->numero_factura . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        // Actualizar ruta (por si era placeholder)
        if ($factura->pdf_path !== $path) {
            $factura->pdf_path = $path;
            $factura->save();
        }
    }

    /**
     * Generador simple de número de factura.
     */
    private function generarNumero(): string
    {
        return 'F-' . now()->format('Y') . '-' . str_pad(
            Factura::count() + 1,
            5,
            '0',
            STR_PAD_LEFT
        );
    }
}
