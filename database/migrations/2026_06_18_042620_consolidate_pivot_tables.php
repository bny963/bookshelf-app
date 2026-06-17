<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 中間テーブルの整理:
 * - book_user / review_user は favorites / review_likes の重複テーブルのため削除
 * - book_genre を book_genres に改名（複数形に統一）
 */
return new class extends Migration {
    public function up(): void
    {
        // favorites と重複する book_user を削除
        Schema::dropIfExists('book_user');

        // review_likes と重複する review_user を削除
        Schema::dropIfExists('review_user');

        // book_genre を複数形の book_genres に改名
        Schema::rename('book_genre', 'book_genres');
    }

    public function down(): void
    {
        Schema::rename('book_genres', 'book_genre');

        Schema::create('book_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'book_id']);
        });

        Schema::create('review_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'review_id']);
        });
    }
};
