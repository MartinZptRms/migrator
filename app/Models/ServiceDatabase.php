<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceDatabase extends Model
{
    protected $fillable = [
        'database_id',
        'type',
    ];

    public function scopeSource($query){
        return $query->where('type', 0);
    }

    public function scopeTarget($query){
        return $query->where('type', 1);
    }

    public function database() : BelongsTo {
        return $this->belongsTo(Database::class);
    }

    public function tables(): HasMany {
        return $this->hasMany(ServiceDatabaseTable::class);
    }
}
