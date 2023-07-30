<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accuracy extends Model
{
    use HasFactory;

    protected $fillable = [
        'accuracy_stars',
        'media_id',
        'user_id',
        'readed_text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function storyMedia()
    {
        return $this->belongsTo(StoryMedia::class);
    }
}
