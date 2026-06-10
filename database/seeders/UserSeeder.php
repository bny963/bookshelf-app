<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * テストユーザーデータ投入用シーダー
 */
class UserSeeder extends Seeder
{
    /**
     * データベースへ初期ユーザーデータを投入
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            ['name' => '山田太郎', 'email' => 'yamada@example.com'],
            ['name' => '鈴木花子', 'email' => 'suzuki@example.com'],
            ['name' => '田中一郎', 'email' => 'tanaka@example.com'],
            ['name' => '佐藤美咲', 'email' => 'sato@example.com'],
            ['name' => '高橋健太', 'email' => 'takahashi@example.com'],
        ];

        foreach ($users as $user) {
            // firstOrCreate を使用し、メールアドレスの重複を防いで安全に登録
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    // パスワードを安全にハッシュ化
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}