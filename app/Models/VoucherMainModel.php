<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherMainModel extends Model
{
    public $table = "voucher_main";
    // use HasFactory;
    protected $guarded = [];


    public function voucherChildren()
    {
        return $this->hasMany(VoucherChildModel::class, 'voucher_code_reference', 'voucher_code');
    }

}
