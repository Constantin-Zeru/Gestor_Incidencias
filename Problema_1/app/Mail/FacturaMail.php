<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Factura;
use Illuminate\Support\Facades\Storage;

class FacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;

    public function __construct(Factura $factura)
    {
        $this->factura = $factura;
    }

    public function build()
    {
        $path = Storage::disk('public')->path($this->factura->pdf_path);

        return $this->subject('Factura ' . $this->factura->numero_factura)
                    ->view('emails.factura')
                    ->attach($path, [
                        'as' => basename($path),
                        'mime' => 'application/pdf',
                    ]);
    }
}
