<?php

namespace App\Http\Controllers;

use App\Models\OrderFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderFileController extends Controller
{
    public function createUpload(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'extension' => 'required|string|max:10',
            'original_name' => 'required|string',
            'size' => 'required|integer|max:104857600',
        ]);

        $filePath = 'orders/'
            . Str::uuid() . '.'
            . $request->extension;

        $orderFile = OrderFile::create([
            'order_id' => $request->order_id,
            'file_path' => $filePath,
            'original_name' => $request->original_name,
            'extension' => $request->extension,
            'size' => $request->size,
            'status' => 'pending',
        ]);

        $disk = Storage::disk('b2');
        $client = $disk->getAdapter()->getClient();

        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.b2.bucket'),
            'Key' => $filePath,
            'ContentType' => 'application/octet-stream',
        ]);

        $presigned = $client->createPresignedRequest($command, '+10 minutes');

        return response()->json([
            'upload_url' => (string) $presigned->getUri(),
            'file_id' => $orderFile->id,
        ]);
    }
    public function markUploaded($id)
    {
        $file = OrderFile::findOrFail($id);

        $file->update([
            'status' => 'uploaded',
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
        ]);
    }

}
