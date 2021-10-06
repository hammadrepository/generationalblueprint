<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{

    protected $fillable = ['question_id','option','is_last','next_question_id','group_id'];
}
