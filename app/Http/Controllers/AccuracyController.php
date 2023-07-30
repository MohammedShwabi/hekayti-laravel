<?php

namespace App\Http\Controllers;

use App\Models\Accuracy;
use Illuminate\Http\Request;

class AccuracyController extends Controller
{
    //Added by Maryam
    //*****************Start */
    // this function return the accuracy of the user for the mobile app
    public function  getAccuracy(Request $request)
    {
        $accuray = Accuracy::where('user_id',$request->user_id)->get();
        return response()->json($accuray);
    }
    // this function update the accuracy of the user for the mobile app
    public function  updateAccuracy(Request $request)
    {
        $accuray = Accuracy::where('user_id',$request->user_id)->where('media_id',$request->media_id)->update([
        'accuracy_stars'=>$request->accuracy_stars,
        'media_id'=>$request->media_id,
        'user_id'=>$request->user_id,
        'readed_text'=>$request->readed_text,
        ]);
       

        return response()->json('Update Accuracy Successfully');
    }
    // this function insert the accuracy of the user to the accuracy table for the mobile app
    public function   addAccuracy(Request $request)
    {
            $accuray=Accuracy::create([
                'accuracy_stars' => $request->accuracy_stars,
                'media_id' =>  $request->media_id,
                'user_id' =>  $request->user_id,
                'readed_text' =>  $request->readed_text,
                'updated_at'=>$request->updated_at,
                ]);
            
            $accuray->save();
        
        
        return response()->json('Add Accuracy Successfully');
    }

    //*****************End */
}
