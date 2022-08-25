<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class userDetails extends Model
{
    protected $primaryKey   =    'id';
    protected $table        =    'users_details';
    protected $guarded      =     [];
}
