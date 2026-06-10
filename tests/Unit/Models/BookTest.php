<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 書籍モデルのリレーションシップテスト
 */
class BookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 書籍が複数のジャンルと多対多リレーションを持っていること
     */
    public function 書籍は複数のジャンルに所属できる(): void
    {
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(2)->create();

        $book->genres()->attach($genres->pluck('id'));

        $this->assertCount(2, $book->genres);
        $this->assertTrue($book->genres->contains($genres->first()));
    }

    /**
     * @test
     * 書籍が複数のレビューを一対多リレーションで保持できること
     */
    public function 書籍は複数のレビューを持つことができる(): void
    {
        $book = Book::factory()->create();

        Review::factory()->count(3)->create(['book_id' => $book->id]);

        $this->assertCount(3, $book->reviews);
    }

    /**
     * @test
     * 書籍がユーザーによってお気に入り登録可能であること
     */
    public function 書籍はお気に入りに登録できる(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->favoritedByUsers()->attach($user->id);

        $this->assertCount(1, $book->favoritedByUsers);
        $this->assertEquals($user->id, $book->favoritedByUsers->first()->id);
    }

    /**
     * @test
     * 書籍がお気に入りリレーションを保持し、件数をカウントできること
     */
    public function 書籍はお気に入り数を持つことができる(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->favorites()->attach($user->id);
        $book->refresh();

        $this->assertCount(1, $book->favorites);
    }
}