<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Completion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;

class CompletionController extends Controller
{
    use GeneralTrait;

    /**
     * Get all completion of the user for the mobile app
     */
    public function  getCompletion(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:completions,user_id',
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }

        // get data
        $completion = Completion::where('user_id', $request->user_id)->get(['id', 'stars', 'percentage', 'story_id', 'updated_at']);

        return $this->returnData(200, 'Completion Added', 'completion', $completion);
    }

    /**
     * Add completion of the user to the completion table for the mobile app
     */
    public function  store(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'stars' => 'required|integer',
            'story_id' => 'required|exists:stories,id',
            'user_id' => 'required|exists:accuracies,user_id',
            'percentage' => 'required|integer',
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }

        // store new completion 
        $completion = new Completion();
        $completion->stars = $request->stars;
        $completion->story_id = $request->story_id;
        $completion->user_id = $request->user_id;
        $completion->percentage = $request->percentage;

        $completion->save();

        return $this->returnSuccessMessage(200, 'Add Completion Successfully.');
    }

    /**
     * Update the completion of the user for the mobile app
     */
    public function  update(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'stars' => 'required|integer',
            'story_id' => 'required|exists:stories,id',
            'user_id' => 'required|exists:accuracies,user_id',
            'percentage' => 'required|integer',
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }
        
        // update completion
        $completion = Completion::where('user_id', $request->user_id)->where('story_id', $request->story_id)->update([
            'stars' => $request->stars,
            'user_id' => $request->user_id,
            'story_id' => $request->story_id,
            'percentage' => $request->percentage,

        ]);

        return $this->returnSuccessMessage(200, 'Update Completion Successfully.');
    }
}
