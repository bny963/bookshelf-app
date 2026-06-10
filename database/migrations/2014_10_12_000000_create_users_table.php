<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーテーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                // 主キー
            $table->string('name');                      // ユーザー名
            $table->string('email')->unique();           // メールアドレス（一意制約）
            $table->timestamp('email_verified_at')->nullable(); // メール認証日時
            $table->string('password');                  // パスワード（ハッシュ済み）
            $table->rememberToken();                     // ログイン維持用トークン
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
        Schema::dropIfExists('users');
    }
};