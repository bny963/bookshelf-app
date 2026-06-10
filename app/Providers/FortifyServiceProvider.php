<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

/**
 * 認証機能（Fortify）の設定用サービスプロバイダー
 */
class FortifyServiceProvider extends ServiceProvider
{
    /**
     * サービスの登録処理
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * 認証サービスの初期化処理
     *
     * @return void
     */
    public function boot(): void
    {
        // 1. 会員登録画面のビューを登録
        Fortify::registerView(fn() => view('auth.register'));

        // 2. ログイン画面のビューを登録
        Fortify::loginView(fn() => view('auth.login'));

        // 3. ユーザー作成アクションのバインディング
        $this->app->singleton(
            \Laravel\Fortify\Contracts\CreatesNewUsers::class,
            CreateNewUser::class
        );

        // 4. ログイン時のレート制限（ブルートフォース攻撃対策）
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email . $request->ip());
        });
    }
}