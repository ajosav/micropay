<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "user_info" => [
                'id'  => $this->encodedKey,
                'first_name'  => $this->first_name,
                'last_name'  => $this->last_name,
                'middle_name'  => $this->middle_name,
                'phone_number'  => $this->phone_number,
                'email' => $this->email,
                'created_at' => $this->created_at

            ]
        ];
    }
}
