<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 失敗したジョブ（failed_jobs）テーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();                                  // 主キー
            $table->string('uuid')->unique();              // ユニーク識別子
            $table->text('connection');                    // 使用した接続名
            $table->text('queue');                         // キュー名
            $table->longText('payload');                   // ジョブのシリアライズデータ
            $table->longText('exception');                 // 発生した例外内容
            $table->timestamp('failed_at')->useCurrent();  // 失敗日時
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};