<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceDatabaseTableJoin extends Model
{
    protected $fillable = [
        'service_database_table_id',
        'type',
        'service_database_table_column_id',
        'from_column',
        'to_column',
    ];

    public function service_table(): BelongsTo {
        return $this->belongsTo(ServiceDatabaseTable::class);
    }

    public function service_column(): BelongsTo {
        return $this->belongsTo(ServiceDatabaseTableColumn::class);
    }

}
