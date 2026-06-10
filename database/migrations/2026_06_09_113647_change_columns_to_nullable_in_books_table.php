<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * booksテーブルの既存カラム制約変更マイグレーション
 */
return new class extends Migration {
    /**
     * マイグレーション実行（isbnとpublished_dateをnullableに変更）
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // ISBNと出版日をnull許容に変更
            $table->string('isbn')->nullable()->change();
            $table->date('published_date')->nullable()->change();
        });
    }

    /**
     * マイグレーション取り消し（元に戻す）
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // null非許容に戻す
            $table->string('isbn')->nullable(false)->change();
            $table->date('published_date')->nullable(false)->change();
        });
    }
};