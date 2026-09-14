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
            'photo' => ProductImageResource::collection($this->whenLoaded('media')),
            'stock' => $this->stock,
            'size' => $this->size,
            'colors' => $this->colors ?? [],
            'colors_with_codes' => collect($this->variants ?? [])
                ->map(fn ($variant) => [
                    'name' => $variant['color'] ?? null,
                    'code' => $variant['color_code'] ?? '#000000',
                ])
                ->filter(fn ($color) => $color['name'])
                ->unique('name')
                ->values()
                ->all(),
            'variants' => collect($this->variants ?? [])->map(function ($variant) {
                $variant['image_url'] = !empty($variant['image'])
                    ? asset($variant['image'])
                    : null;
                $variant['color_code'] = $variant['color_code'] ?? '#000000';

                return $variant;
            })->values()->all(),
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
            'is_favorite' => $request->user() ? $request->user()->isFavorite($this->id) : false,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
