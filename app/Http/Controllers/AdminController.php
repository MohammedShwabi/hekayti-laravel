<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

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

    // edit profile photo in profile page
    public function editProfilePhoto(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'image.required' => 'لطفاً قم بإختيار صورة ليتم رفعها',
            'image.image' => 'يجب أن يكون الملف صورة',
            'image.mimes' => 'فقط الانواع التالية متاحة jpeg, png, jpg, gif',
            'image.max' => 'حجم الصورة أكبر من 2MB',
        ]);

        $image = $request->file('image');
        $adminId = auth()->user()->id;

        // Generate a unique image name
        $imageName = "admin_{$adminId}_" . now()->timestamp . '.' . $image->getClientOriginalExtension();

        // Move the uploaded image to the desired location
        if ($image->move(public_path('upload/profiles_photos/'), $imageName)) {
            // Generate the thumbnail
            $thumbWidth = 200;
            $imagePath = public_path('upload/profiles_photos/' . $imageName);
            $thumbPath = public_path('upload/profiles_photos/thumbs/' . $imageName);
            Image::make($imagePath)->resize($thumbWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($thumbPath);
        }

        // get the admin from db
        $admin = Admin::find(auth()->user()->id);

        // Delete the old image if it's not the default one
        if ($admin->image !== 'profile.svg') {
            $oldImagePath = public_path('upload/profiles_photos/' . $admin->image);
            $oldThumbPath = public_path('upload/profiles_photos/thumbs/' . $admin->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
                unlink($oldThumbPath);
            }
        }

        // Update the URL of the image
        $imageUrl = asset('upload/profiles_photos/' . $imageName);
        $thumbUrl = asset('upload/profiles_photos/thumbs/' . $imageName);

        // Update the photo attribute in the database
        $admin->image = $imageName;
        $admin->save();


        // Return the updated image URLs
        return response()->json([
            'url' => $imageUrl,
            'thumbUrl' => $thumbUrl,
        ]);
    }
}
