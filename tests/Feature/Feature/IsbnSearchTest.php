<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IsbnSearchTest extends TestCase
{
    public function test_isbn_search_returns_book_data_successfully()
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

    public function test_isbn_search_handles_api_error()
    {
        // APIエラー（例えば404や500）をシミュレート
        Http::fake([
            'googleapis.com/*' => Http::response([], 404)
        ]);

        $this->getJson('/api/v1/books/isbn/9784101010014')
            ->assertStatus(404);
    }

    public function test_isbn_search_invalid_format()
    {
        // 13桁未満など、バリデーションエラーのテスト
        $this->getJson('/api/v1/books/isbn/123')
            ->assertStatus(422);
    }
}