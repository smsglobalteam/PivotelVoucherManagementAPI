<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ServicePlansModel extends Model
{
    use HasFactory;

    public $table = "service_plans";
    // use HasFactory;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget('servicePlans');
        });

        static::deleted(function ($model) {
            Cache::forget('servicePlans');
        });
    }

}
