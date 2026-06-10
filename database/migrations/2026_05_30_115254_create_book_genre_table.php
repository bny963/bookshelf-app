<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 書籍とジャンルの中間テーブル作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('book_genre', function (Blueprint $table) {
            $table->id();                                  // 主キー

            // 外部キー：書籍（書籍削除時に紐付けも削除）
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            // 外部キー：ジャンル（書籍に紐付いている場合、ジャンル削除を制限）
            $table->foreignId('genre_id')->constrained()->onDelete('restrict');

            $table->timestamps();                          // 作成・更新日時

            // 複合ユニーク制約：同じ書籍に同じジャンルが重複して登録されるのを防止
            $table->unique(['book_id', 'genre_id']);
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('book_genre');
    }
};