<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ProductResource;

class ProductCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => ProductResource::collection($this->collection),
            'pagination' => [
                'total'        => $this->resource->total(),
                'count'        => $this->resource->count(),
                'per_page'     => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'total_pages'  => $this->resource->lastPage(),
            ],
        ];
    }

}
