<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

/**
 * ブロードキャスト（リアルタイム通信）サービスプロバイダー
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションのブロードキャストサービスを初期化
     *
     * @return void
     */
    public function boot(): void
    {
        // ブロードキャスト用のルートを登録
        Broadcast::routes();

        // チャンネル認証用のルートファイルを読み込み
        require base_path('routes/channels.php');
    }
}