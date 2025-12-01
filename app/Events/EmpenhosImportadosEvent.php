<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmpenhosImportadosEvent implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new Channel('empenhos');
    }

    public function broadcastAs()
    {
        return 'EmpenhosAtualizados';
    }
}
