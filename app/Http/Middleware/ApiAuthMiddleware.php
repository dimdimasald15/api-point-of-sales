<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $authenticate = true;

        if (!$token) {
            $authenticate = false;
        }

        $employee = Employee::where('token', $token)->first();
        if (!$employee) {
            $authenticate = false;
        } else {
            Auth::login($employee);
        }
        // Tambahkan ini di dalam handle()
        Log::info('Token from request header: ' . $token);
        Log::info('Employee found: ', ['employee' => $employee]);
        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
