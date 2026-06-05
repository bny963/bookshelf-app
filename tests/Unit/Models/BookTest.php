<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 書籍は複数のジャンルに所属できる()
    {
        // 1. 書籍とジャンルを生成
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(2)->create();

        // 2. 中間テーブルに紐付け
        $book->genres()->attach($genres->pluck('id'));

        // 3. リレーションが正しく取得できるか検証
        $this->assertCount(2, $book->genres);
        $this->assertTrue($book->genres->contains($genres->first()));
    }

    /** @test */
    public function 書籍は複数のレビューを持つことができる()
    {
        $book = Book::factory()->create();

        // Factoryを使用してレビューを3つ作成
        Review::factory()->count(3)->create(['book_id' => $book->id]);

        $this->assertCount(3, $book->reviews);
    }
}