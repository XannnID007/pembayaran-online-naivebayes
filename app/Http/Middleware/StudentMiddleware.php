<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'siswa') {
            abort(403, 'Unauthorized. Student access required.');
        }

        // Pastikan siswa memiliki data siswa
        if (!auth()->user()->siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan. Hubungi administrator.');
        }

        return $next($request);
    }
}
