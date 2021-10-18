<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{

    protected $table = 'group_user';
    protected $fillable = ['user_id','group_id'];

    public function category()
    {
        return $this->belongsTo('App\Group');
    }
}
