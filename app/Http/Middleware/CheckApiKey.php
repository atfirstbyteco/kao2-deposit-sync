<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        switch(env('APP_ENV','uat')){
            case "uat":
            $apiKey = "1b412a31-0f14-4fa9-9c57-30e0f83101fb";
            $apiSecret="bpBrwPQ6V4cPn68bTKuVYB4EKvyjxeuA4DUt";
            break;
            case "prod":
            $apiKey = "ef7a2a56-0cf5-4e76-8cbd-085c82e622b6";
            $apiSecret="DuSRsnBN26kcqMgDQQnJkDxAzAvsYK3bEmTe";
            break;
        }
        if($request->header('x-api-key')==$apiKey && $request->header('x-api-secret')==$apiSecret){
            return $next($request);
        }else{
            return abort(404);
        }

    }
}
