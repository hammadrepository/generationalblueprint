<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{

    protected $table = 'user_category';
    protected $fillable = ['user_id','category_id'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
