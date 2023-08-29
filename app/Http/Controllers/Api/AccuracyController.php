<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accuracy;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Hash;

class AccuracyController extends Controller
{
    use GeneralTrait;

    /**
     * Get all accuracy of the user for the mobile app
     */
    public function  getAccuracy(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:accuracies,user_id',
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }

        // get accuracy data
        $accuracy = Accuracy::where('user_id', $request->user_id)->get();
        return $this->returnData(200, 'Accuracy retrieved', 'accuracy', $accuracy);
    }

    /**
     * Update the accuracy of the user for the mobile app
     */
    public function  update(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'accuracy_stars' => 'required|integer',
            'media_id' => 'required|exists:stories_media,id',
            'user_id' => 'required|exists:accuracies,user_id',
            'readed_text' => 'required|string',
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }

        // update accuracy 
        $accuracy = Accuracy::where('user_id', $request->user_id)->where('media_id', $request->media_id)->update([
            'accuracy_stars' => $request->accuracy_stars,
            'media_id' => $request->media_id,
            'user_id' => $request->user_id,
            'readed_text' => $request->readed_text,
        ]);

        return $this->returnSuccessMessage(200, 'Update Accuracy Successfully.');
    }

    /**
     * Add accuracy of the user to the accuracy table for the mobile app
     */
    public function   store(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'accuracy_stars' => 'required|integer',
            'media_id' => 'required|exists:stories_media,id',
            'user_id' => 'required|exists:accuracies,user_id',
            'readed_text' => 'required|string',
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }
        // store new accuracy 
        $accuracy = new Accuracy();
        $accuracy->accuracy_stars = $request->accuracy_stars;
        $accuracy->media_id = $request->media_id;
        $accuracy->user_id = $request->user_id;
        $accuracy->readed_text = $request->readed_text;
        $accuracy->updated_at = $request->updated_at;

        $accuracy->save();

        return $this->returnSuccessMessage(200, 'Add Accuracy Successfully.');
    }
}
