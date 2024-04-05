<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorCodesModel extends Model
{
    public $table = "error_codes_reference";
    // use HasFactory;
    protected $guarded = [];
}
