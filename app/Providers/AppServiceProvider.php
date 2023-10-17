<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        if (function_exists('bcscale')) {
            bcscale(18);
        }

        App::setLocale('cn');

        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            DB::listen(function ($query) {
                 file_put_contents(
                    storage_path(date('Y-m-d') . '_sql_log.log'),
                    '[' . date('Y-m-d H:i:s') . ']  ' . Str::replaceArray('?', $query->bindings, $query->sql) . '                    ' . $query->time . 'ms' . PHP_EOL,
                    FILE_APPEND
                );
             });
        }      
    }
}
