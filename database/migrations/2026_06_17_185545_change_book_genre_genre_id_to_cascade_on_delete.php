<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * book_genre テーブルの genre_id 外部キーを cascade 削除に変更
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('book_genre', function (Blueprint $table) {
            $table->dropForeign(['genre_id']);
            $table->foreign('genre_id')->references('id')->on('genres')->cascadeOnDelete();
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('book_genre', function (Blueprint $table) {
            $table->dropForeign(['genre_id']);
            $table->foreign('genre_id')->references('id')->on('genres')->restrictOnDelete();
        });
    }
};
