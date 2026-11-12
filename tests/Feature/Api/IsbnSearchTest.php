<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * ISBN検索APIの機能テスト
 */
class IsbnSearchTest extends TestCase
{
    /**
     * Google Books APIから書籍情報を正常に取得できること
     */
    #[Test]
    public function test_isbn_search_returns_book_data_successfully(): void
    {
        // Google Books API のレスポンスを偽装
        Http::fake([
            'googleapis.com/*' => Http::response([
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'テスト書籍',
                            'authors' => ['テスト著者'],
                        ]
                    ]
                ]
            ], 200)
        ]);

        $this->getJson('/api/v1/books/isbn/9784101010014')
            ->assertStatus(200)
            ->assertJsonPath('title', 'テスト書籍');
    }

    /**
     * 外部APIがエラーを返した場合に適切に404を返却すること
     */
    #[Test]
    public function test_isbn_search_handles_api_error(): void
    {
        // APIエラー（404 Not Found）をシミュレート
        Http::fake([
            'googleapis.com/*' => Http::response([], 404)
        ]);

        $this->getJson('/api/v1/books/isbn/9784101010014')
            ->assertStatus(404);
    }

    /**
     * ISBNの形式が不正な場合にバリデーションエラー（422）が返却されること
     */
    #[Test]
    public function test_isbn_search_invalid_format(): void
    {
        // 13桁未満など、バリデーションルールに違反するリクエスト
        $this->getJson('/api/v1/books/isbn/123')
            ->assertStatus(422);
    }
}