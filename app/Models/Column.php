<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Column extends Model
{
    protected $fillable = [
        'name',
        'data_type',
    ];

    public function table(): BelongsTo {
        return $this->belongsTo(Table::class);
    }
}
