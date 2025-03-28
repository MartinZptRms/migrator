<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
    ];
}
