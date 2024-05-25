<?php

namespace App\Http\Middleware;

use App\Models\Logs;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class BeforeAction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logObject = [
            'type' => 'own',
            'name_service' => $request->route()->getActionMethod(),
            'url' => $request->getRequestUri(),
            'ip' => $request->getClientIp(),
            'method' => $request->getMethod(),
            'headers' => json_encode($request->headers->all() ?? ''),
            'request' => json_encode($request->all() ?? ''),
        ];

        $log = Logs::create($logObject);
        Session::put('logId', $log->id);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $log = Logs::find(Session::get('logId'));
        $log->response = json_encode($response);
        $log->save();
    }
}
