<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceDatabaseTableColumn extends Model
{
    protected $fillable = [
        'column_id',
        'custom_column_id',
    ];

    protected $appends = [
        'alias'
    ];

    public function column(): BelongsTo {
        return $this->belongsTo(Column::class);
    }

    public function custom_column(): BelongsTo {
        return $this->belongsTo(Column::class);
    }

    public function getAliasAttribute(){
        if($this->custom_column){
            return $this->column->name.' AS '.$this->custom_column->name;
        }
        return $this->column->name;
    }
}
