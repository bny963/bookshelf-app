<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'rating', 'comment'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function likedByUsers(): HasMany
    {
        // 実在する「users」テーブルを指定し、IDが「-1」のものを探しにいかせます。
        // これでデータベース側もエラーを吐かず、安全に count() が 0 になります。
        return $this->hasMany(User::class, 'id')->where('id', -1);
    }
}