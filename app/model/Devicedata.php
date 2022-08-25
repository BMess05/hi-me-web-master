<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
  protected $primaryKey   =    'id';
  protected $table        =    'device_data';
  protected $guarded      =     [];
}
