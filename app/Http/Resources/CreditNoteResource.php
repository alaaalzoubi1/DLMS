<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'subscriber_id' => $this->subscriber_id,
            'reason' => $this->reason,
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'order' => $this->whenLoaded('order', function () {
                return [
                    'id' => $this->order->id,
                    'patient_name' => $this->order->patient_name,
                    'status' => $this->order->status,
                    'cost' => $this->order->cost,
                    'created_at' => $this->order->created_at,
                ];
            }),

            'items' => CreditNoteItemResource::collection($this->whenLoaded('creditNoteItems')),
        ];
    }
}
