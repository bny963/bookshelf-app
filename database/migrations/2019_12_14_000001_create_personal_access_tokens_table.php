<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * API認証用トークン（personal_access_tokens）テーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();                                    // 主キー
            $table->morphs('tokenable');                     // トークン所有者（Userモデル等への関連付け）
            $table->string('name');                          // トークン名（用途など）
            $table->string('token', 64)->unique();           // SHA-256トークン本体
            $table->text('abilities')->nullable();           // トークンに許可された権限（スコープ）
            $table->timestamp('last_used_at')->nullable();   // 最終使用日時
            $table->timestamp('expires_at')->nullable();     // 有効期限
            $table->timestamps();                            // 作成・更新日時
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};