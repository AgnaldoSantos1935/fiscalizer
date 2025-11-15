<?php

namespace App\Mail;

use App\Models\Demanda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DemandaEmpresaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Demanda $demanda,
        public string $urlUpload
    ) {}

    public function build()
    {
        return $this->subject("SEDUC/PA – Envio de Documento Técnico – Demanda {$this->demanda->id}")
            ->view('emails.demanda_empresa');
    }
}
