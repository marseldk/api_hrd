<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Karyawan;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($_COOKIE);
        if ($request->session()->exists('user')) {
            return redirect(RouteServiceProvider::HOME);
        }
        //check remember me token if exists in cookie re put session
        if (isset($_COOKIE['remember_token'])) {
            $userToken = UserToken::where('token', $_COOKIE['remember_token'])->first();
            if ($userToken) {
                //check expire date
                if (strtotime($userToken->expires_at) < strtotime(date('Y-m-d H:i:s'))) {
                    //delete token
                    $userToken->delete();
                    setcookie('remember_token', '', time() - 3600, '/');
                } else {
                    $user = Karyawan::getOneByNikFunc($userToken->nik_func);
                    if ($user) {
                        $request->session()->put('user', $user);
                        return redirect(RouteServiceProvider::HOME);
                    }
                }
            }
        }

        return $next($request);
    }
}
