<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Blade::precompiler(function (string $template): string {
            return preg_replace_callback(
                '~(<(?!script\\b|style\\b)[^>]+>)([^<>{}\\r\\n]*[\\p{Arabic}][^<>{}\\r\\n]*)(</[^>]+>)~u',
                static fn (array $match): string => $match[1].'{{ \\App\\Support\\LocalizedText::get('.var_export($match[2], true).') }}'.$match[3],
                $template
            );
        });

        // Rate limit login attempts: 5 per minute per email+IP
        RateLimiter::for('login', function (Request $request) {
            $key = strtolower($request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });
    }
}
