<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Favorite;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_date',
        'description',
        'image_url',
        'genre_id',
    ];
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genre', 'book_id', 'genre_id');
    }
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_user')->withTimestamps();
    }
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_user')->withTimestamps();
    }
}
