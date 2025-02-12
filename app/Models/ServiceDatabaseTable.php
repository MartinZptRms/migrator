<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceDatabaseTable extends Model
{
    protected $fillable = [
        'table_id',
        'source',
    ];

    public function table(): BelongsTo {
        return $this->belongsTo(Table::class);
    }

    public function columns(): HasMany {
        return $this->hasMany(ServiceDatabaseTableColumn::class);
    }

    public function clauses(): HasMany {
        return $this->hasMany(ServiceDatabaseTableClause::class);
    }
}
