<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationEvent extends Model
{
    protected $table = 'notification_events';

    protected $casts = [
        'channels' => 'array',
        'enabled' => 'boolean',
        'recipient_roles' => 'array',
        'recipient_users' => 'array',
        'should_generate' => 'boolean',
        'rules' => 'array',
        'workflow' => 'array',
    ];

    protected $fillable = [
        'codigo', 'dominio', 'title', 'message', 'channels', 'enabled',
        'priority', 'recipient_scope', 'recipient_roles', 'recipient_users', 'should_generate', 'rules', 'workflow',
    ];
}
