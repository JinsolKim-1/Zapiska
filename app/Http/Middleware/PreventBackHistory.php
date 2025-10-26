<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        // Only redirect guests
        if (!Auth::guard('web')->check()) {
            $protectedPaths = [
                'password/reset',
                'password/email',
                'password/forgot',
                'post-verification',
                'welcmain',
            ];

            foreach ($protectedPaths as $path) {
                if ($request->is($path) || $request->is($path . '/*')) {
                    return redirect()->route('login')
                        ->with('info', 'Session expired. Please login again.');
                }
            }
        }

        return $response;
    }
}
