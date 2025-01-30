<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if (empty(session('user')->nik_func)) {
            //if route is home then no redireci
            //if not redirect to home
            // dd($request->route()->getName());
            $allow = ['role.list', 'home'];
            if (!in_array($request->route()->getName(), $allow)) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Unauthorized.'], 401);
                }
                return redirect()->route('home');
            }
        }

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty(session('user'))) {
            $this->unauthenticated($request, $guards);
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // dd($request->session()->all());
        // dd($request->expectsJson() ? null : route('login'));
        return $request->expectsJson() ? null : route('login');
    }
}
