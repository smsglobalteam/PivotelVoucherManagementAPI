<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherModel extends Model
{
    public $table = "voucher";
    use HasFactory;
    protected $guarded = [];
}
