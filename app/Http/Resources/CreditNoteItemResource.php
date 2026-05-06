<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'credit_note_id' => $this->credit_note_id,
            'order_product_id' => $this->order_product_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total' => $this->quantity * $this->unit_price,

                'product' => $this->whenLoaded('orderProduct', function () {
                return [
                    'id' => $this->orderProduct->id,
                    'product_name' => $this->orderProduct->product_name ?? null,
                    'price' => $this->orderProduct->price,
                ];
            }),
        ];
    }
}
