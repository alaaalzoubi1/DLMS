<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabHeaderRequest;
use App\Http\Requests\UpdateLabHeaderRequest;
use App\Models\LabInvoiceHeader;
use Illuminate\Support\Facades\Storage;

class LabHeaderController extends Controller
{
    public function store(LabHeaderRequest $request)
    {
        $subscriber_id = auth('admin')->user()->subscriber_id;

        $data = $request->validated();
        $exists = LabInvoiceHeader::where('subscriber_id', $subscriber_id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'تم إنشاء ترويسة لهذا المخبر مسبقًا.'
            ], 422);
        }

        $file = $request->file('logo');
        $extension = $file->getClientOriginalExtension();
        $uniqueName = uniqid('logo_', true) . '.' . $extension;

        $data['logo'] = $file->storeAs('lab_headers', $uniqueName, 'public');
        $data['subscriber_id'] = $subscriber_id;

        $header = LabInvoiceHeader::create($data);

        return response()->json([
            'message' => 'تم إنشاء ترويسة المخبر بنجاح',
            'data' => $header
        ]);
    }

    public function update(UpdateLabHeaderRequest $request)
    {
        $subscriber_id = auth('admin')->user()->subscriber_id;

        $header = LabInvoiceHeader::where('subscriber_id', $subscriber_id)->first();

        if (!$header) {
            return response()->json([
                'message' => 'Lab header not found.'
            ], 404);
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {

            if ($header->logo && Storage::disk('public')->exists($header->logo)) {
                Storage::disk('public')->delete($header->logo);
            }

            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $uniqueName = uniqid('logo_', true) . '.' . $extension;

            $data['logo'] = $file->storeAs(
                'lab_headers',
                $uniqueName,
                'public'
            );
        }

        $header->update($data);

        return response()->json([
            'message' => 'Lab header updated successfully.',
            'data' => $header
        ]);
    }

    public function getHeader()
    {
        $subscriber_id = auth('admin')->user()->subscriber_id;

        return response()->json([
            'header' => LabInvoiceHeader::where('subscriber_id', $subscriber_id)->first()
        ]);
    }

    public function delete()
    {
        $subscriber_id = auth('admin')->user()->subscriber_id;

        $header = LabInvoiceHeader::where('subscriber_id', $subscriber_id)->first();

        if (!$header) {
            return response()->json([
                'message' => 'لا يوجد ترويسة لحذفها'
            ], 404);
        }

        if ($header->logo && Storage::disk('public')->exists($header->logo)) {
            Storage::disk('public')->delete($header->logo);
        }

        $header->delete();

        return response()->json([
            'message' => 'تم حذف ترويسة المخبر بنجاح'
        ]);
    }
}
