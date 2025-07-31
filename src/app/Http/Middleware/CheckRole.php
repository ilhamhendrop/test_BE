<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();

        try {
            $expectedRole = RoleEnum::from($role);
        } catch (\ValueError $e) {
            abort(403, 'Role tidak dikenal.');
        }

        if (!$user || !$user->hasRole($expectedRole)) {
            Auth::logout();
            session()->flash('message', 'Akses tidak diizinkan');
            return redirect()->route('login');
        }

        return $next($request);
    }
}
