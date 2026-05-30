<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            $favBookIds = $books->random(rand(3, 5))->pluck('id')->toArray();
            // 要件：syncWithoutDetaching を使用
            $user->favoriteBooks()->syncWithoutDetaching($favBookIds);
        }
    }
}