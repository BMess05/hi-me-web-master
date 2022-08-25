<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\model\userDetails;
use Illuminate\Database\Eloquent\SoftDeletes;

class friendList extends Model
{
    use SoftDeletes;
    protected $primaryKey   =    'id';
    protected $table        =    'friend_list';
    protected $guarded      =     [];
    protected $dates        =   ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User','sender_id');
    }

    public function userD()
    {
        return $this->belongsTo('App\User','reciver_id' ,'id');
    }

    public function usersFriend()
    {
        return $this->belongsTo('App\User', 'reciver_id');
    }

    public function usersFriendlist()
    {
        return $this->belongsTo('App\model\userDetails','reciver_id', 'user_id');
    }

    public function pendingrequest()
    {
        return $this->belongsTo('App\model\userDetails', 'reciver_id' , 'user_id');
    }
}
