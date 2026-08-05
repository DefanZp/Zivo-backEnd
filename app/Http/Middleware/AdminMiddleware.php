<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->role !== 'admin')
        {
            return response()->json([
                'message' => 'Forbidden. Admin Access Only.'
            ], 403);
        };

        // jika user admin lanjutkan request
        return $next($request);
    }
}
