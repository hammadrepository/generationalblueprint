<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupDocument extends Model
{
    protected $fillable = ['group_id', 'name','link','file_type'];

    public function getLinkAttribute($value)
    {
        return url('/').'/storage/'.$value;
    }
    public function storeDocument($data)
    {
        $document = GroupDocument::create([
            'group_id' => $data['group_id'],
            'name' => $data['file_name'],
            'link' => $data['file_link'],
            'file_type' => $data['file_type'],
        ]);
        return $document;
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }
}
