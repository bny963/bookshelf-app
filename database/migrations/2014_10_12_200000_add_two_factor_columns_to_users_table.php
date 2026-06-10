<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーテーブルへ二要素認証（2FA）カラムを追加
 */
return new class extends Migration {
    /**
     * マイグレーション実行
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 2FA用暗号化シークレットキー
            $table->text('two_factor_secret')
                ->after('password')
                ->nullable();

            // 2FAリカバリーコード（暗号化保存）
            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            // 2FA確定日時
            $table->timestamp('two_factor_confirmed_at')
                ->after('two_factor_recovery_codes')
                ->nullable();
        });
    }

    /**
     * マイグレーション取り消し
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }
};