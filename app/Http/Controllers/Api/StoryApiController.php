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

    // this function return all the stories table for the mobile app
    public function getStories()
    {
        $stories = Story::where('published', 1)->get();
        if ($stories->isEmpty()) {
            return $this->returnError(404, 'No Story Found.');
        }
        return $this->returnData(200, 'Stories retrieved', 'stories', $stories);
    
    }
    // this function return all the storyMedia table for the mobile app
    public function  getStoriesMedia()
    {
        $storiesMedia = StoryMedia::all();
        if ($storiesMedia->isEmpty()) {
            return $this->returnError(404, 'No Story Found.');
        }
        return $this->returnData(200, 'StoriesMedia retrieved', 'storiesMedia', $storiesMedia);

    }
}
