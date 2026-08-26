<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Observers\CommentObserver;
use App\Observers\TicketObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('db.connector.pgsql', function () {
        return new \App\Database\NeonPostgresConnector;
        });
    }

    public function boot(): void
    {
        Ticket::observe(TicketObserver::class);
        Comment::observe(CommentObserver::class);

        Blade::if('role', function (string $role) {
            return auth()->check() && auth()->user()->role === $role;
        });
        if (env('APP_ENV') !== 'local') {
        URL::forceScheme('https');
        }
    }
}