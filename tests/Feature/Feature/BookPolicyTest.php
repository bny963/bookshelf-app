<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 所有者は書籍を更新できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('update', $book));
    }

    /** @test */
    public function 他人は書籍を更新できない()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($other->can('update', $book));
    }

    /** @test */
    public function 所有者は書籍を削除できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('delete', $book));
    }

    /** @test */
    public function 他人は書籍を削除できない()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($other->can('delete', $book));
    }
    /** @test */
    public function その他のポリシーも許可または拒否を判定する()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new \App\Policies\BookPolicy();

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->view($user, $book));
        $this->assertTrue($policy->create($user));
        $this->assertFalse($policy->restore($user, $book));
        $this->assertFalse($policy->forceDelete($user, $book));
    }
}