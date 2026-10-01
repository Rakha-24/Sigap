<?php

namespace App\Providers;

use App\Cache\NeonSafeDatabaseStore;
use App\Database\NeonPostgresConnector;
use App\Models\Comment;
use App\Models\Ticket;
use App\Observers\CommentObserver;
use App\Observers\TicketObserver;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('db.connector.pgsql', function () {
            return new NeonPostgresConnector;
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

        // Driver cache "database" bawaan Laravel memakai transaksi eksplisit
        // untuk increment/decrement (rate limiter). Di Neon/PgBouncer
        // transaction-mode, transaksi multi-pernyataan bisa di-abort →
        // SQLSTATE 25P02. Ganti dengan store autocommit (lihat
        // app/Cache/NeonSafeDatabaseStore.php).
        Cache::extend('database', function ($app, array $config) {
            $store = new NeonSafeDatabaseStore(
                $app['db']->connection($config['connection'] ?? null),
                $config['table'],
                $config['prefix'] ?? $app['config']->get('cache.prefix'),
                $config['lock_table'] ?? 'cache_locks',
                $config['lock_lottery'] ?? [2, 100],
                $config['lock_timeout'] ?? 86400,
                $app['config']->get('cache.serializable_classes'),
            );

            $store->setLockConnection(
                $app['db']->connection($config['lock_connection'] ?? $config['connection'] ?? null)
            );

            $repository = new Repository($store, Arr::only($config, ['store']));

            if ($app->bound(EventDispatcher::class)) {
                $repository->setEventDispatcher($app[EventDispatcher::class]);
            }

            return $repository;
        });
    }
}
