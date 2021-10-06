<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Category extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "name" => isset($this->name) ? $this->name : '',
            "avatar" => isset($this->avatar) ? $this->avatar :'',
            "unique_id" => isset($this->unique_id) ? $this->unique_id :  '',
        ];
    }
}
