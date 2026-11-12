<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Policies\BookPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * 書籍の認可ポリシー（BookPolicy）のテストクラス
 */
class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 書籍の所有者が更新権限を持っていること
     */
    #[Test]
    public function 所有者は書籍を更新できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('update', $book));
    }

    /**
     * 他人は書籍の更新権限を持っていないこと
     */
    #[Test]
    public function 他人は書籍を更新できない(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($other->can('update', $book));
    }

    /**
     * 書籍の所有者が削除権限を持っていること
     */
    #[Test]
    public function 所有者は書籍を削除できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('delete', $book));
    }

    /**
     * 他人は書籍の削除権限を持っていないこと
     */
    #[Test]
    public function 他人は書籍を削除できない(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($other->can('delete', $book));
    }

    /**
     * Policyクラスの各メソッドの挙動を直接検証
     */
    #[Test]
    public function その他のポリシーも許可または拒否を判定する(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new BookPolicy();

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->view($user, $book));
        $this->assertTrue($policy->create($user));
        $this->assertFalse($policy->restore($user, $book));
        $this->assertFalse($policy->forceDelete($user, $book));
    }
}