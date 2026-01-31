<?php
namespace App\Services;

use App\Models\Cuota;
use App\Models\Factura;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturaMail;

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

        $factura = Factura::create([
            'cuota_id' => $cuota->id,
            'numero_factura' => $numero,
            'fecha' => now()->toDateString(),
            'pdf_path' => $path,
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
}
