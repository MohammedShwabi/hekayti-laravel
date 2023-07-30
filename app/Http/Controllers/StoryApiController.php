<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\StoryMedia;


class StoryApiController extends Controller
{
     //Added by Maryam
    //*****************Start */
    // this function return all the stories table for the mobile app
    public function getStories()
    {
        $stories = Story::where('published',1)->get();
     
     return response()->json($stories);
    }
    // this function return all the storyMedia table for the mobile app
    public function  getStoriesMedia()
    {
        $storMedia = StoryMedia::all();
        return response()->json($storMedia);
    }
    //*****************End */
}

