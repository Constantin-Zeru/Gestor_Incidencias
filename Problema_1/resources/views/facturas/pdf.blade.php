<!doctype html>
<html><head><meta charset="utf-8"><style>body{font-family: DejaVu Sans, sans-serif;} .table{width:100%;border-collapse:collapse;} .table th,.table td{border:1px solid #ddd;padding:8px;}</style></head><body>
<div style="text-align:center"><h2>Factura {{ $numero }}</h2><div>{{ $fecha }}</div></div>
<h4>Datos cliente</h4>
<div>{{ $cliente->nombre }} - {{ $cliente->cif ?? '' }}</div>
<div>{{ $cliente->telefono }}</div>
<div>{{ $cliente->email }}</div>
<h4>Detalle</h4>
<table class="table"><thead><tr><th>Concepto</th><th>Importe</th></tr></thead><tbody>
<tr><td>{{ $cuota->concepto }}</td><td style="text-align:right">{{ number_format($cuota->importe,2) }}</td></tr>
</tbody></table>
<p style="text-align:right"><strong>Total: {{ number_format($cuota->importe,2) }} {{ $cliente->moneda ?? 'EUR' }}</strong></p>
</body></html>
