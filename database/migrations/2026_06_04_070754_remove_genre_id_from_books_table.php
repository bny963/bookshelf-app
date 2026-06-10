<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * booksテーブルのgenre_idカラムの修正マイグレーション
 */
return new class extends Migration {
    /**
     * マイグレーション実行（カラム削除）
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // SQLiteは外部キー制約の削除が複雑なため、sqlite以外の場合のみ実行
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['genre_id']);
            }
            $table->dropColumn('genre_id');
        });
    }

    /**
     * マイグレーション取り消し（カラム再追加）
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('genre_id')->nullable()->constrained();
        });
    }
};