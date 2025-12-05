<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClinicHeaderRequest;
use App\Http\Requests\UpdateClinicHeaderRequest;
use App\Models\ClinicInvoiceHeader;
use Illuminate\Support\Facades\Storage;

class ClinicHeaderController extends Controller
{
    public function store(ClinicHeaderRequest $request)
    {
        $clinic_id = auth('api')->user()->doctor->clinic_id;

        $data = $request->validated();

        $file = $request->file('logo');
        $extension = $file->getClientOriginalExtension();
        $uniqueName = uniqid('logo_', true) . '.' . $extension;

        $data['logo'] = $file->storeAs('clinic_headers', $uniqueName, 'public');
        $data['clinic_id'] = $clinic_id;

        $header = ClinicInvoiceHeader::create($data);

        return response()->json([
            'message' => 'تم إنشاء الترويسة بنجاح',
            'data' => $header
        ]);
    }

    public function update(UpdateClinicHeaderRequest $request)
    {
        $clinic_id = auth('api')->user()->doctor->clinic_id;

        $header = ClinicInvoiceHeader::where('clinic_id', $clinic_id)->first();

        if (!$header) {
            return response()->json([
                'message' => 'Clinic header not found.'
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

            $data['logo'] = $file->storeAs('clinic_headers', $uniqueName, 'public');
        }

        $header->update($data);

        return response()->json([
            'message' => 'Clinic header updated successfully.',
            'data' => $header
        ]);
    }

    public function getHeader()
    {
        $clinic_id = auth('api')->user()->doctor->clinic_id;

        return response()->json([
            'header' => ClinicInvoiceHeader::where('clinic_id', $clinic_id)->first()
        ]);
    }

    public function delete()
    {
        $clinic_id = auth('api')->user()->doctor->clinic_id;

        $header = ClinicInvoiceHeader::where('clinic_id', $clinic_id)->first();

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
            'message' => 'تم حذف الترويسة بنجاح'
        ]);
    }
}
