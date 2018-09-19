<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Event::listen('tymon.jwt.absent', function () {
            return response()->json([
                'code' => 6,
                'message' => '请登录',
            ], 401);
        });

        Event::listen('tymon.jwt.invalid', function () {
            return response()->json([
                'code' => 6,
                'message' => '请求失败，请重新登录',
            ], 401);
        });

        Event::listen('tymon.jwt.expired', function () {
            return response()->json([
                'code' => 6,
                'message' => '登录过期，请重新登录',
            ], 401);
        });

        Event::listen('tymon.jwt.user_not_found', function () {
            return response()->json([
                'code' => 5,
                'message' => '用户不存在',
            ], 404);
        });
    }
}
