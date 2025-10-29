<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;
}
