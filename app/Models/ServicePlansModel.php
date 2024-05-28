<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePlansModel extends Model
{
    use HasFactory;

    public $table = "service_plans";
    // use HasFactory;
    protected $guarded = [];

}
