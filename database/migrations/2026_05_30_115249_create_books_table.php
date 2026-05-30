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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 登録ユーザー
            $table->string('title', 255);       // 業務要件：最大255桁
            $table->string('author', 255);      // 業務要件：最大255桁
            $table->string('isbn', 13)->unique(); // 業務要件：厳密に13桁・重複不可
            $table->date('published_at');
            $table->text('description')->nullable(); // 業務要件：長文テキスト（任意）
            $table->string('image_path', 255)->nullable(); // 業務要件：最大255桁（任意）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
