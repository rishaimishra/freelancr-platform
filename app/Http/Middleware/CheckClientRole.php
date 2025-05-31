<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckClientRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->user_type === 'user') {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Only clients can post jobs.');
    }
} 