<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class OrdersResource extends JsonResource
{
    public function toArray($request): array
    {
        $hideMap = $request->get('hide_map', []);
        $hideSpecializationMap = $request->get('hide_specialization_map', []);

        $hidePrice = $hideMap[$this->subscriber_id] ?? false;
        $hideSpecialization = $hideSpecializationMap[$this->subscriber_id] ?? false;

        return [
            'id' => $this->id,
            'subscriber_id' => $this->subscriber_id,
            'doctor_id' => $this->doctor_id,

            'status' => $this->status,
            'patient_name' => $this->patient_name,
            'patient_id' => $this->patient_id,

            'cost' => $hidePrice ? null : $this->cost,
            'paid' => $hidePrice ? null : $this->paid,

            'invoiced' => $this->invoiced,
            'delivery' => $this->delivery,
            'receive' => $this->receive,

            'type' => $this->type,
            'subscriber' => $this->subscriber,
            'doctor' => $this->doctor,

            'products' => OrderProductResource::collection(
                $this->orderProducts->map(function ($product) use ($hidePrice, $hideSpecialization) {
                    $product->hide_price = $hidePrice;
                    $product->hide_specialization = $hideSpecialization;

                    return $product;
                })
            ),

            'discount' => $this->discount,
            'files' => $this->files,

            'zatca_document' => $this->whenLoaded('zatcaDocument'),
            'credit_notes' => $this->whenLoaded('creditNotes'),

            'created_at' => $this->created_at,
        ];
    }
}
