<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class DebugSanctumMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract token
        $bearerToken = $request->bearerToken();
        
        // Debug data
        $debugData = [
            'path' => $request->path(),
            'method' => $request->method(),
            'token_exists' => !is_null($bearerToken),
            'token_length' => $bearerToken ? strlen($bearerToken) : 0,
        ];
        
        if ($bearerToken) {
            $tokenParts = explode('|', $bearerToken);
            $debugData['token_parts_count'] = count($tokenParts);
            
            if (count($tokenParts) === 2) {
                $tokenId = $tokenParts[0];
                $tokenPart = $tokenParts[1];
                
                $debugData['token_id'] = $tokenId;
                $debugData['token_part_length'] = strlen($tokenPart);
                
                // Check if this token exists in the database
                $token = PersonalAccessToken::find($tokenId);
                $debugData['token_in_db'] = !is_null($token);
                
                if ($token) {
                    $debugData['token_name'] = $token->name;
                    $debugData['token_created_at'] = $token->created_at->toDateTimeString();
                    $debugData['token_last_used_at'] = $token->last_used_at ? $token->last_used_at->toDateTimeString() : null;
                    $debugData['token_user_id'] = $token->tokenable_id;
                    $debugData['token_user_type'] = $token->tokenable_type;
                }
            }
        }
        
        // Log this information
        Log::info('Sanctum Debug', $debugData);
        
        // Add to response
        $response = $next($request);
        
        return $response;
    }
}
