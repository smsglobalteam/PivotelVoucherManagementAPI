<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherModel extends Model
{
    //
    public $table = "voucher_main";
    // use HasFactory;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_code_reference', 'product_code');
    }
}
