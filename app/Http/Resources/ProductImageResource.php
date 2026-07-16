<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $thumbnailPath = $this->path
            ? \Illuminate\Support\Str::replaceLast('.', '_thumbnail.', $this->path)
            : null;

        // Agar thumbnail file maujood hai to uska URL, warna full image
        $thumbnailUrl = $thumbnailPath && file_exists(public_path($thumbnailPath))
            ? asset($thumbnailPath)
            : ($this->path ? asset($this->path) : null);

        return [
            'id'            => $this->id,
            'url'           => $this->path ? asset($this->path) : null,
            'thumbnail_url' => $thumbnailUrl,
            'is_primary'    => $this->is_primary,
            'sort_order'    => $this->sort_order,
        ];
    }
}
