<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ((bool) $request->user()->employee_of) {
            Notification::make()
                ->danger()
                ->title('You are not allowed')
                ->send();

            return redirect()->route('filament.outlet.pages.dashboard');
        }

        return $next($request);
    }
}
