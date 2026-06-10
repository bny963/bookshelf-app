<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ジャンルテーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();                                // 主キー
            $table->string('name', 100)->unique();       // ジャンル名（最大100文字・重複不可）
            $table->timestamps();                        // 作成・更新日時
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};