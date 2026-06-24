<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Book;
use App\Policies\BookPolicy;
use App\Models\ReadingPlan;
use App\Policies\ReadingPlanPolicy;
use App\Models\Review;
use App\Policies\ReviewPolicy;

/**
 * 認証・認可サービスプロバイダー
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * モデルとポリシーの紐付け設定
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Book::class => BookPolicy::class,
        ReadingPlan::class => ReadingPlanPolicy::class,
        Review::class => ReviewPolicy::class,
    ];

    /**
     * 認証・認可サービスの登録処理
     *
     * @return void
     */
    public function boot(): void
    {
        // 必要に応じてゲート定義などを追加
    }
}