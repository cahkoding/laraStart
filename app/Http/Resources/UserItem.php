<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email'=> $this->email,
            'type' => $this->type,
            'bio' => $this->bio,
            'photo' => $this->photo,
            'created_at' => $this->created_at->toDateTimeString()            
        ];
    }
}
