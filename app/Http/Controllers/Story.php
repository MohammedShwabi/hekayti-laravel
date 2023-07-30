<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;
    // public $table = "story";

    protected $fillable = [
        'name',
        'cover_photo',
        'author',
        'level',
        'story_order',
        'required_stars',
        'published',
    ];

    public function storyMedia()
    {
        return $this->hasMany(StoryMedia::class);
    }
    public function completion()
    {
        return $this->hasMany(Completion::class);
    }
}
