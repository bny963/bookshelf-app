<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_genre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            // 業務要件：「書籍紐付きがある場合は削除を制限すること」を満たすため restrict
            $table->foreignId('genre_id')->constrained()->onDelete('restrict');
            $table->timestamps();

            // ER図通りの複合ユニーク：同じ本に同じジャンルが重複して紐づくのを防ぐ
            $table->unique(['book_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_genre');
    }
};
