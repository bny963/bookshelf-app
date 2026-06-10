<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * お気に入りテーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();                                  // 主キー

            // 外部キー：ユーザー（ユーザー削除時にお気に入りも削除）
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 外部キー：書籍（書籍削除時にお気に入りも削除）
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            $table->timestamps();                          // 作成・更新日時

            // 複合ユニーク制約：同一ユーザーによる同一書籍のお気に入り重複を防止
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
        Schema::dropIfExists('favorites');
    }
};