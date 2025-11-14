<?php

// app/Services/WhatsAppService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function send(string $numero, string $mensagem): void
    {
        // Exemplo genérico (Twilio, Zenvia, etc.)
        // Http::post('https://api.seu-provedor.com/send', [...]);
        logger()->info("WhatsApp → {$numero}: {$mensagem}");
    }
}
