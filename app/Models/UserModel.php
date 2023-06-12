<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    public $table = "users";
    use HasFactory;
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
