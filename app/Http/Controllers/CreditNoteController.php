<?php

namespace App\Http\Controllers;

use App\Http\Resources\CreditNoteResource;
use App\Models\CreditNote;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $subscriberId = auth('admin')->user()->subscriber_id;

        $creditNotes = CreditNote::with([
            'items.orderProduct'
        ])
            ->where('subscriber_id', $subscriberId)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return CreditNoteResource::collection($creditNotes);
    }

    public function show($id)
    {
        $subscriberId = auth('admin')->user()->subscriber_id;

        $creditNote = CreditNote::with([
            'order.zatcaDocument',
            'items.orderProduct'
        ])
            ->where('subscriber_id', $subscriberId)
            ->findOrFail($id);

        return new CreditNoteResource($creditNote);
    }
}
