<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $fillable = [
        'url',
        'event',
        'secret',
        'status',
    ];
}
