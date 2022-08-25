<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class UserSubscriptions extends Model
{
    protected $table = "user_subscriptions";
    protected $fillable = ['id', 'user_id', 'package_name', 'product_id', 'purchase_token', 'google_response', 'created_at', 'updated_at'];
}
