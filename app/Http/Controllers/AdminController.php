<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{

      // to view the login page 
      public function login()
      {
          if (auth()->check()) {
              return  redirect('/stories');
          }
          return view('auth.login');
      }
      // check from user data  
      public function trylogin(Request $request)
      {
          $validatedData = $request->validate(
              [
                  'email' => 'required',
                  'password' => 'required|min:5',
              ]
          );
  
          if (auth()->attempt([
              'email' => request()->email,
              'password' => request()->password,
          ])) {
              return redirect('/home');
          } else {
              return back()->withErrors(["email"=>"معلوماتك خاطئة"]);
          }
      }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
    // logout user
    public function logout()
    {
        auth()->logout();
        session()->flush();

        return redirect('/');
    }
}
