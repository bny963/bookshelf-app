<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 通知管理テーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();               // UUIDを主キーとして使用
            $table->string('type');                      // 通知クラス名
            $table->morphs('notifiable');                // 通知対象（Userモデル等への関連付け）
            $table->text('data');                        // 通知内容（JSONデータ）
            $table->timestamp('read_at')->nullable();    // 既読日時
            $table->timestamps();                        // 作成・更新日時
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};