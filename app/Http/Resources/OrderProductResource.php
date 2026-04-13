<?php

namespace App\Http\Resources;

use App\Services\PriceSittingsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $hide = $this->hide_price ?? false;

        return [
            'id' => $this->id,
            'note' => $this->note,
            'tooth_numbers' => $this->tooth_numbers,
            'product_id' => $this->product_id,

            'unit_price' => $hide ? null : $this->unit_price,

            'product_name' => $this->product_name,
            'status' => $this->status,

            'tooth_color' => $this->toothColor,
            'specialization_user' => $this->specializationUser,

            'created_at' => $this->created_at,
        ];
    }}
