<?php

namespace App\Http\Resources;

use App\Http\Resources\BookResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public $collects = BookResource::class;

    public function toArray($request)
    {
        return [
            'Books' => $this->collection,
        ];
    }
}
