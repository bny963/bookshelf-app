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
        Schema::create('book_user', function (Blueprint $table) {
            $table->id();
            //  ユーザーID（外部キー）: ユーザーが削除されたらお気に入りも消えるように cascade
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            //  書籍ID（外部キー）: 書籍が削除されたらお気に入りも消えるように cascade
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // 同じ本を何度も同時にお気に入り登録できないように、組み合わせを一意にする
            $table->unique(['user_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_user');
    }
};
