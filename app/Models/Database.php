<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Database extends Model
{
    protected $fillable = [
        'connection_id',
        'name',
        'size'
    ];

    public function connection(): BelongsTo {
        return $this->belongsTo(Connection::class);
    }
    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }
}
