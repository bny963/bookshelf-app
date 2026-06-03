<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * 💡 ジャンルとのリレーション（所属）も、もし未定義ならここに追記しておくと安全です
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
