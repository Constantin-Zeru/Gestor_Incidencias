<p>Estimado/a {{ $factura->cuota->cliente->nombre }},</p>
<p>Adjuntamos la factura {{ $factura->numero_factura }} correspondiente a la cuota {{ $factura->cuota->concepto }} por importe {{ number_format($factura->cuota->importe,2) }}.</p>
<p>Un saludo,<br>Equipo Gestor-Incidencias</p>
