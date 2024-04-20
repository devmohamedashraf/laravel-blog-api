<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'thumbnail',
        'content',
        'likes',
        'dislikes',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->where('is_published', true);
    }
    public function getLikes()
    {
        return $this->hasMany(Like::class);
    }
    public function increaseLikes()
    {
        $this->likes = $this->liked + 1;
        $this->save();
    }
    public function increaseDislikes()
    {
        $this->dislikes = $this->dislikes + 1;
        $this->save();
    }

    public function decreaseLikes()
    {
        $this->likes = $this->likes - 1;
        $this->save();
    }
    public function decreaseDislikes()
    {
        $this->dislikes = $this->dislikes - 1;
        $this->save();
    }
}
