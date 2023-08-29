<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\GeneralTrait;

class CheckApiKey
{
    use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->api_key != 'Mohammed_Shwabi' ){
            return $this->returnError(401, 'Unauthenticated.');
        }
        return $next($request);
    }
}
