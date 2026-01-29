<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Use Bootstrap 5 pagination views
        Paginator::useBootstrapFive();

        // Share commonly used cached data with all views to prevent repeated queries
        View::composer('*', function ($view) {
            // Only load if not already set
            if (!$view->offsetExists('cachedUniversities')) {
                $view->with('cachedUniversities', Cache::remember('all_universities_simple', 3600, function () {
                    return \App\Models\University::select('id', 'name')->where('is_active', true)->orderBy('name')->get();
                }));
            }
            
            if (!$view->offsetExists('cachedPrograms')) {
                $view->with('cachedPrograms', Cache::remember('all_programs_simple', 3600, function () {
                    return \App\Models\Program::select('id', 'name')->where('is_active', true)->orderBy('name')->get();
                }));
            }
        });

        // Enable query logging for slow queries in production
        if (config('app.env') === 'production' && config('app.log_slow_queries', false)) {
            DB::listen(function ($query) {
                $threshold = config('app.slow_query_threshold', 1000);
                
                if ($query->time > $threshold) {
                    Log::warning('SLOW QUERY DETECTED', [
                        'sql' => $query->sql,
                        'time' => $query->time . 'ms',
                        'bindings' => $query->bindings,
                        'location' => request()->path()
                    ]);
                }
            });
        }
    }
}
