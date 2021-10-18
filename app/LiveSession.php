<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    protected $table = 'live_session';
    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo('App\Group','group_id','id');
    }

}
