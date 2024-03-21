<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchOrderModel extends Model
{
    public $table = "batch_order";
    // use HasFactory;
    protected $guarded = [];

    public function voucher()
    {
        return $this->hasMany(VoucherModel::class, 'batch_id', 'batch_id');
    }
}
