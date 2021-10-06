<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSignature extends Model
{
    protected $table = 'email_signature';
    protected $fillable = ['user_id','name','image','imageLink'];

}
