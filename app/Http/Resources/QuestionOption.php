<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionOption extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return             [
            'id'=> $this->id,
            'name'=> $this->option,
            'is_last'=> $this->is_last ? true: false,
            $this->mergeWhen(isset($this->is_last) && $this->is_last == false, [
                'next_question_id'=> $this->next_question_id,
            ]),
            $this->mergeWhen(isset($this->is_last) && $this->is_last == true, [
                'group_id'=> $this->group_id,
            ]),
        ];
    }
}
