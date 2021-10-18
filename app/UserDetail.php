<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{

    protected $table = 'user_detail';

    protected $fillable = ['user_id','avatar','race','age_group','nationality'];
}
