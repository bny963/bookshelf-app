<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function APIから書籍一覧が正しい形式で取得できる()
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'isbn']
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function 必須項目が欠けているとエラーが返る()
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => '', // バリデーションエラーを誘発
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'isbn']);
    }
}