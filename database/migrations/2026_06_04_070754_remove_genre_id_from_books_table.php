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
        Schema::table('books', function (Blueprint $table) {
            // SQLiteの場合は dropForeign を実行しない
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['genre_id']); // 外部キー名を確認してください
            }
            $table->dropColumn('genre_id');
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('genre_id')->nullable()->constrained();
        });
    }
};
