<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

/**
 * 書籍モデルの認可ポリシー
 */
class BookPolicy
{
    /**
     * 一覧表示が可能か判定
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * 詳細表示が可能か判定
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function view(User $user, Book $book): bool
    {
        return true;
    }

    /**
     * 新規作成が可能か判定
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 更新が可能か判定（作成者のみ許可）
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }

    /**
     * 削除が可能か判定（作成者のみ許可）
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }

    /**
     * 復元が可能か判定
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function restore(User $user, Book $book): bool
    {
        return false;
    }

    /**
     * 完全削除が可能か判定
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return false;
    }
}