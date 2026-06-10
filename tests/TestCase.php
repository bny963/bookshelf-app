<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    // アプリケーション生成ロジック（CreatesApplicationトレイト）を使用
    use CreatesApplication;

    /**
     * 各テスト実行前に呼び出される初期化メソッド
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 【注意】例外ハンドリングを無効化しています
        $this->withoutExceptionHandling();
    }
}