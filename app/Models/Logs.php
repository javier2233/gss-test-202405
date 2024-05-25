<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $fillable = [
        'type',
        'name_service',
        'url',
        'ip',
        'method',
        'headers',
        'request',
        'response',
    ];
}
