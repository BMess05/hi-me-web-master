<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = "settings";
    protected $fillable = ['access_token', 'refresh_token', 'expiry_time'];
}
