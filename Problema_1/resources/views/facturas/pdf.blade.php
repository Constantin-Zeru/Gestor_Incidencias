<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .table { width:100%; border-collapse: collapse; margin-top:15px; }
    .table th, .table td { border:1px solid #ddd; padding:8px; }
    .right { text-align:right; }
  </style>
</head>
<body>

<div style="text-align:center">
    <h2>Factura {{ $factura->numero_factura ?? ($numero ?? '—') }}</h2>
    <div>Fecha: {{ isset($factura) && $factura->fecha ? $factura->fecha->format('d/m/Y') : ($fecha ?? now()->format('d/m/Y')) }}</div>
</div>

<hr>

<h4>Datos del cliente</h4>
<div><strong>{{ $cliente->nombre }}</strong></div>
<div>{{ $cliente->cif ?? '' }}</div>
<div>{{ $cliente->telefono }}</div>
<div>{{ $cliente->email }}</div>

<h4>Detalle</h4>

<table class="table">
    <thead>
        <tr>
            <th>Concepto</th>
            <th class="right">Importe ({{ $cuota->moneda ?? ($factura->moneda ?? 'EUR') }})</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $cuota->concepto }}</td>
            <td class="right">
                {{ number_format($cuota->importe, 2) }} {{ $cuota->moneda ?? ($factura->moneda ?? 'EUR') }}
            </td>
        </tr>
    </tbody>
</table>

<p class="right">
  <strong>
    Total en EUR:
    {{ number_format($factura->importe_euros ?? ($factura->pagada ? $cuota->importe : null) , 2) }} €
  </strong>
</p>

@if(isset($factura) && $factura->pagada)
<p style="margin-top:15px; font-size:10px; color:#666;">
    Tipo de cambio aplicado: {{ $factura->tipo_cambio ?? '—' }}<br>
    Fecha de cambio (fecha pago): {{ $factura->fecha_pago ? $factura->fecha_pago->format('d/m/Y') : '—' }}
</p>
@else
<p style="margin-top:10px; font-size:10px; color:#666;">
    Nota: factura preliminar. El importe en EUR se calculará al marcar la cuota como pagada.
</p>
@endif

</body>
</html>
