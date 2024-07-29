<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function testUpload(): string
    {
        try {
            $content = 'This is a test file';
            $path = Storage::disk('s3')->put('test-file.txt', $content);

            if ($path) {
                Log::info('File uploaded successfully', ['path' => $path]);
                return "File uploaded successfully to S3. Path: " . $path;
            } else {
                Log::warning('File upload failed');
                return "File upload failed.";
            }
        } catch (\Exception $e) {
            Log::error('Error uploading file', ['error' => $e->getMessage()]);
            return "Error uploading file: " . $e->getMessage();
        }
    }

    public function testRetrieve(): string
    {
        // Check if the file exists
        if (Storage::disk('s3')->exists('test-file.txt')) {
            // Retrieve the file contents
            $contents = Storage::disk('s3')->get('test-file.txt');
            return "File contents: " . $contents;
        } else {
            return "File does not exist.";
        }
    }

    public function testList(): string
    {
        // List all files in the S3 bucket
        $files = Storage::disk('s3')->allFiles();

        if (!empty($files)) {
            return "Files in the bucket: " . implode(", ", $files);
        } else {
            return "No files found in the bucket.";
        }
    }

    public function testDelete(): string
    {
        // Delete the file from S3
        if (Storage::disk('s3')->exists('test-file.txt')) {
            Storage::disk('s3')->delete('test-file.txt');
            return "File deleted successfully.";
        } else {
            return "File does not exist.";
        }
    }
}
