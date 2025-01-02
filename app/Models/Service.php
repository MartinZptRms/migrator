<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    protected $fillable = [
        'name',
        'every',
        'every_unit',
        'description',
        'status'
    ];

    public function databases() : HasMany {
        return $this->hasMany(ServiceDatabase::class);
    }

    public function source_database(): HasOne {
        return $this->hasOne(ServiceDatabase::class)->where('type', 0);
    }
    public function target_database(): HasOne {
        return $this->hasOne(ServiceDatabase::class)->where('type', 1);
    }
}
