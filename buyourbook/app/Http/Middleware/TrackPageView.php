<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET HTML requests (not AJAX, not assets)
        if ($request->isMethod('GET')
            && !$request->ajax()
            && !$request->wantsJson()
            && $response->getStatusCode() === 200
        ) {
            try {
                PageView::create([
                    'url'        => substr($request->path(), 0, 500),
                    'ip'         => $request->ip(),
                    'session_id' => substr(session()->getId(), 0, 64),
                    'user_id'    => auth()->id(),
                    'viewed_at'  => now()->toDateString(),
                ]);
            } catch (\Throwable) {
                // Silently ignore — tracking must never break the app
            }
        }

        return $response;
    }
}
