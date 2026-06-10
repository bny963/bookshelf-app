<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーとレビューの関連（いいね等）テーブルの作成
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('review_user', function (Blueprint $table) {
            $table->id();                                  // 主キー

            // 外部キー：ユーザー（ユーザー削除時に紐付けも削除）
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 外部キー：レビュー（レビュー削除時に紐付けも削除）
            $table->foreignId('review_id')->constrained()->onDelete('cascade');

            $table->timestamps();                          // 作成・更新日時

            // 複合ユニーク制約：同一ユーザーによる同一レビューの二重登録を防止
            $table->unique(['user_id', 'review_id']);
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('review_user');
    }
};