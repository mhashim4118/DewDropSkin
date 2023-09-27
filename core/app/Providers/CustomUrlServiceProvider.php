<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;

class CustomUrlServiceProvider extends ServiceProvider
{
    public function boot(UrlGenerator $url)
    {
        if (env('FORCE_HTTP', false)) {
            $url->forceScheme('http');
        }
    }
    public function register()
    {
        //
    }

  
}
