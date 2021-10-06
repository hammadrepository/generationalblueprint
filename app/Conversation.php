<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['type','message', 'user_id', 'group_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
