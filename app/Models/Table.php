<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'name',
        'size',
        'type',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class);
    }
}
