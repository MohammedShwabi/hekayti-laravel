<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\StoryMedia;
use App\Traits\GeneralTrait;
use Illuminate\Database\QueryException;

class StoryApiController extends Controller
{
    use GeneralTrait;

    /**
     * Get all stories
     */
    public function getStories()
    {
        // get all stories
        $stories = Story::where('published', 1)->get();
        if ($stories->isEmpty()) {
            return $this->returnError(404, 'No Story Found.');
        }

        return $this->returnData(200, 'Stories retrieved', 'stories', $stories);
    }

    /**
     * Get all stories media
     */
    public function  getStoriesMedia()
    {
        // get all stories media
        $storiesMedia = StoryMedia::all();
        if ($storiesMedia->isEmpty()) {
            return $this->returnError(404, 'No Story Found.');
        }

        return $this->returnData(200, 'StoriesMedia retrieved', 'storiesMedia', $storiesMedia);
    }
}
