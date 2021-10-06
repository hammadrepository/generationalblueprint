<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

    protected $fillable = ['question'];

    public function options()
    {
        return $this->hasMany('App\QuestionOption');
    }
}
