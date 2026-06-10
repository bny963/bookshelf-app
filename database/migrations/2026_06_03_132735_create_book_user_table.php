<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 書籍とユーザーの関連（お気に入り等）テーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('book_user', function (Blueprint $table) {
            $table->id();                                  // 主キー

            // 外部キー：ユーザー（ユーザー削除時に紐付けも削除）
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 外部キー：書籍（書籍削除時に紐付けも削除）
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            $table->timestamps();                          // 作成・更新日時

            // 複合ユニーク制約：同一ユーザーによる同一書籍の重複登録を防止
            $table->unique(['user_id', 'book_id']);
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('book_user');
    }
};