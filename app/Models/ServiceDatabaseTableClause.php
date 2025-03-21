<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceDatabaseTableClause extends Model
{
    protected $fillable = [
        'type',
        'clause',
        'field',
        'operator',
        'value',
    ];

    public function table(): BelongsTo {
        return $this->belongsTo(ServiceDatabaseTable::class);
    }

}
