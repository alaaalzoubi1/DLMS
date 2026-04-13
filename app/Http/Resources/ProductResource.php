<?php

namespace App\Http\Resources;

use App\Services\PriceSittingsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $hide = $this->hide_price ?? false;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->category_id,

            'final_price' => $hide ? null : $this->final_price,

            'created_at' => $this->created_at,
        ];
    }
}
