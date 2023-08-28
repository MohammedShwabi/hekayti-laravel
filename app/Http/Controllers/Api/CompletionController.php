<?php

namespace App\Http\Controllers;

use App\Models\Completion;
use Illuminate\Http\Request;

class CompletionController extends Controller
{
    //Added by Maryam
    //*****************Start */
    // this function return the completion of the user for the mobile app
    public function  getCompletion(Request $request)
    {
        $completion = Completion::where('user_id',$request->user_id)->get(['percentage','stars','story_id','updated_at','id']);
        return response()->json($completion);
    }
    // this function update the completion of the user for the mobile app
    public function  updateCompletion(Request $request)
    {
        $completion = Completion::where('user_id',$request->user_id)->where('story_id',$request->story_id)->update([
    
        'stars'=>$request->stars,
        'user_id'=>$request->user_id,
        'story_id'=>$request->story_id,
        'percentage'=>$request->percentag,

        ]);

        return response()->json('Update Completion Successfully');
    }
    // this function insert the completion of the user to the completion table for the mobile app
    public function  addCompletion(Request $request)
    {
        $completion=Completion::create([
            'stars' => $request->stars,
            'user_id' => $request->user_id,
            'story_id' => $request->story_id,
            'percentage' => $request->percentage,
            ]);
        
        $completion->save();
        
        return response()->json('Add Completion Successfully');
    }

    //*****************End */
}