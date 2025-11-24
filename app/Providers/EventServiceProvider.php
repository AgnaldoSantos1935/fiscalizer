<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \App\Events\MedicaoHomologada::class => [
            \App\Listeners\CriarSolicitacaoPagamento::class,
            \App\Listeners\AtualizarSLA::class,
        ],
        \App\Events\PagamentoAutorizado::class => [
            \App\Listeners\AtualizarSLA::class,
        ],
        \App\Events\PagamentoEfetuado::class => [
            \App\Listeners\AtualizarSLA::class,
        ],
        \App\Events\EmpenhoRegistrado::class => [
            \App\Listeners\MarcarSolicitacaoComoEmpenhada::class,
            \App\Listeners\GerarOrdemFornecimentoBens::class,
        ],
        \App\Events\ContratoAssinado::class => [
            \App\Listeners\LiberarProjetos::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
