<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 書籍テーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();                                  // 主キー
            // 外部キー：ユーザー（削除時に書籍も削除）
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('title');                       // 書籍タイトル
            $table->string('author');                      // 著者名
            $table->string('isbn');                        // ISBNコード
            $table->date('published_date');                // 出版日

            // 外部キー：ジャンル（削除時にnullへ設定）
            $table->foreignId('genre_id')->nullable()->constrained()->onDelete('set null');

            $table->text('description')->nullable();       // 概要
            $table->string('image_url')->nullable();       // 画像パス
            $table->timestamps();                          // 作成・更新日時
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};