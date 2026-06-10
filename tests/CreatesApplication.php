<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * アプリケーションを生成し、初期化して返却する
     */
    public function createApplication(): Application
    {
        // 1. bootstrap/app.php を読み込んで、アプリケーションインスタンスを生成
        $app = require __DIR__ . '/../bootstrap/app.php';

        // 2. カーネルを通じてアプリケーションのブートストラップを実行
        // これにより、サービスプロバイダーの登録や設定の読み込みが行われる
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}