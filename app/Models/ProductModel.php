<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    public $table = "product";
    // use HasFactory;
    protected $guarded = [];

    public function voucher()
    {
        return $this->hasMany(VoucherModel::class, 'product_code_reference', 'product_code');
    }

    public function batch_order()
    {
        return $this->hasMany(BatchOrderModel::class, 'product_id', 'id')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }
}
