<?php

namespace App\Http\Resources;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public $collects =      RoleResource::class;

    public function toArray($request)
    {
        return [
             $this->collection,
        ];
    }
}
