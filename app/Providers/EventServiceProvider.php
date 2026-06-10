<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * アプリケーションのイベント・リスナー登録用サービスプロバイダー
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * イベントとリスナーの紐付け設定
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * アプリケーションのイベントを登録
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * イベントとリスナーを自動検出するかどうかの判定
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}