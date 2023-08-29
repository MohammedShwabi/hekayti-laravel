<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    use GeneralTrait;

    /**
     * Sign up the user 
     */
    // this function 
    public function signup(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'character' => 'required|integer',
            'level' => 'required|integer'
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }

        // store new user 
        $newUser = new User();
        $newUser->user_name = $request->user_name;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);
        $newUser->character = $request->character;
        $newUser->level = $request->level;

        $newUser->save();

        // custom response array
        $response = [
            'id' => $newUser->id,
            'user_name' => $newUser->user_name,
            'email' => $newUser->email,
            'character' => $newUser->character,
            'level' => $newUser->level,
        ];

        return $this->returnData(200, 'User Added', 'user', $response);
    }

    /**
     * Login the user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }
        $user = User::where('email', $request->email)->first();
        if (Hash::check($request->password, $user->password)) {
            $response = [
                'id' => $user->id,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'character' => $user->character,
                'level' => $user->level,
            ];
            return $this->returnData(200, 'User Found', 'user', $response);
        } else {
            return $this->returnError(401, 'Unauthenticated.');
        }
    }

    /**
     * Update the information of the user
     */
    public function  update(Request $request)
    {
        // validate from data in the request
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'user_name' => 'required|min:3',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($request->id),
            ],
            'password' => 'required|string',
            'character' => 'required|integer',
            'level' => 'required|integer'
        ]);

        // return validation errors
        if ($validator->fails()) {
            return $this->returnValidations(422, $validator);
        }
        // update user
        $user = User::where('id', $request->id)->update(
            [
                'user_name' => $request->user_name,
                'password' => Hash::make($request->password),
                'character' => $request->character,
                'level' => $request->level,
                'email' => $request->email,
                'updated_at' => $request->updated_at,
            ]
        );
        return $this->returnSuccessMessage(200, 'Update User Successfully.');
    }
    
}
