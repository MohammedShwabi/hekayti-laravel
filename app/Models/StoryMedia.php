<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryMedia extends Model
{
    public $table = "stories_media";
    use HasFactory;
    protected $fillable = [
        'story_id',
        'page_no',
        'photo',
        'sound',
        'text',
        'text_no_desc',
    ];
    
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
    public function accuracy()
    {
        return $this->hasMany(Accuracy::class);
    }
}
