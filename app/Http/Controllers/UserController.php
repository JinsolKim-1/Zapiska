<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function showProfileImage($filename)
    {
        $path = 'profiles/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mime = Storage::disk('public')->mimeType($path);

        return response($file, 200)->header('Content-Type', $mime);
    }
}
