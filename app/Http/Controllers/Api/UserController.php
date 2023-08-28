<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //Added by Maryam
    //*****************Start */
    // this function sign up the user and return his id
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            abort(434);
        }
        $user=User::create([
            'user_name'=> $request->user_name,
            'email'=> $request->email,
            'password'=>  $request->password,
            'character'=> $request->character,
            'level'=> $request->level,
        ]);
        $user->save();
       
        return response()->json($user->id);
    }

    // this function login the user and return his information
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json('User not Found!');
        }
        $user = User::where('email', $request->email)->first();
        if((strcmp($request->password,$user->password))==0) { 
                $userInfo=[
                    'id' => $user->id,
                    'user_name'=> $user->user_name,
                    'email'=> $user->email,
                    'character'=> $user->character,
                    'level'=> $user->level,
                ]; 
            
            return response()->json($userInfo);
        } else {
            return response()->json('Password is not correct!');
        }
    }
    
    // this function update the information of the user for the mobile app
    public function  updateUser(Request $request)
    {
        $user = User::where('id',$request->id)->update([
            'user_name'=>$request->user_name,
            'password'=>$request->password,
            'character'=>$request->character,
            'level'=>$request->level,
            'email'=>$request->email,
            'updated_at'=>$request->updated_at,

            
            ]
        );
    
       
        return response()->json('Update User Successfully');
    }
    // this function insert the accuracy of the user to the accuracy table for the mobile app
    
    //*****************End */
}
