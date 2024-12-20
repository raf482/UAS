<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Device;

class ValidateApiKey
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('API-Key');

        if (!$apiKey || !Device::where('api_key', $apiKey)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
