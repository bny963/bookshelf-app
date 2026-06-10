<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * ルーティングおよびレート制限設定用サービスプロバイダー
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * ログイン後のデフォルトリダイレクト先
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * ルートモデルバインディングやレート制限の設定
     *
     * @return void
     */
    public function boot(): void
    {
        // APIリクエストのレート制限設定（1分間に60回まで）
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // ルート定義の登録
        $this->routes(function () {
            // APIルートの定義
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Webルートの定義
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}