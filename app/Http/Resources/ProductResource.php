<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'price' => $this->price,
            'discount' => $this->discount,
            'status' => $this->status,
            'photo' => $this->photo,
            'stock' => $this->stock,
            'size' => $this->size,
            'is_featured' => (bool) $this->is_featured,
            'condition' => $this->condition,
            'brand' => [
                'id' => optional($this->brand)->id,
                'name' => optional($this->brand)->name,
            ],
            'category' => [
                'id' => optional($this->cat_info)->id,
                'name' => optional($this->cat_info)->title,
            ],
            'sub_category' => [
                'id' => optional($this->sub_cat_info)->id,
                'name' => optional($this->sub_cat_info)->title,
            ],
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
