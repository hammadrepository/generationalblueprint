<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{

    protected $table = 'user_answer';
    protected $fillable = ['user_id','question_id','option_id'];
}
