<?php

namespace App\Http\Resources;

use App\Group;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LiveSessionCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $group = Group::whereId($this->group_id)->get(['id','name'])[0];
        return [
            'id' => $this->id,
            'topic' => $this->topic,
            'description' => $this->description,
            'date' => $this->date,
            'time' => $this->time,
            'link' => $this->link,
            'group' => $group,
            'group_id' => $group->id
        ];
    }
}
