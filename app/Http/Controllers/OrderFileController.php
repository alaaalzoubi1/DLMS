<?php

namespace App\Http\Controllers;

use App\Models\OrderFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Aws\S3\S3Client;
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

        $filePath = 'orders/' . $request->order_id . '/' . \Str::uuid() . '.' . $request->extension;

        $file = OrderFile::create([
            'order_id' => $request->order_id,
            'file_path' => $filePath,
            'original_name' => $request->original_name,
            'extension' => $request->extension,
            'size' => $request->size,
            'status' => 'pending',
        ]);

        $client = $this->s3Client();

        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.b2.bucket'),
            'Key' => $filePath,
            'ContentType' => 'application/octet-stream',
        ]);

        $presignedRequest = $client->createPresignedRequest(
            $command,
            '+10 minutes'
        );

        return response()->json([
            'upload_url' => (string) $presignedRequest->getUri(),
            'file_id' => $file->id,
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
    private function s3Client(): S3Client
    {
        return new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => config('filesystems.disks.b2.endpoint'),
            'credentials' => [
                'key' => config('filesystems.disks.b2.key'),
                'secret' => config('filesystems.disks.b2.secret'),
            ],
            'use_path_style_endpoint' => true,
        ]);
    }
}
