<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Database extends Model
{
    protected $fillable = [
        'name',
        'size'
    ];

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }
}
