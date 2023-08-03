<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            return back()->withErrors(["email" => "معلوماتك خاطئة"]);
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
    // public function show(Admin $admin)
    // {
    //     //
    // }

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



    // show all manager except admin
    public function show(Admin $admin, Request $request)
    {

        $search = $request->query('search');

        // Construct the query to retrieve managers
        $query = $admin::query()->where('role', 'manager')->when($search, function ($query, $search) {
            // get only the name that match the search
            return $query->where('name', 'like', '%' . $search . '%');
        });

        if ($request->ajax()) {
            // Fetch a maximum of 8 manager names for Ajax response
            return $query->take(8)->pluck('name');
        }

        // Fetch all managers for non-Ajax request
        $admins = $query->get();

        // Pass managers and search query to the view
        return view('admin', compact('admins', 'search'));
    }

    // change manager statue
    public function adminChangeLocked(Request $request)
    {
        // Find the admin by ID
        $admin = Admin::findOrFail($request->admin_id);

        // Update the locked status
        $admin->locked = $request->locked;
        $admin->save();

        // Return a JSON response indicating success
        return response()->json(['success' => 'Status changed successfully.']);
    }


    // to delete manager from the list
    public function delete(Request $request, Admin $admin)
    {
        // Retrieve the admin by ID
        $admin = Admin::find($request->admin_id);

        // Get the photo filename
        $photo = $admin->photo;

        // Delete the admin's photo if it is not the default profile image
        if ($photo !== 'profile.svg') {
            // Define the file paths for the photo and its thumbnail
            $photoPath = public_path('upload/profiles_photos/' . $photo);
            $thumbPath = public_path('upload/profiles_photos/thumbs/' . $photo);

            // Delete the photo file if it exists
            file_exists($photoPath) && unlink($photoPath);

            // Delete the thumbnail file if it exists
            file_exists($thumbPath) && unlink($thumbPath);
        }

        // Delete the admin record
        $admin->delete();

        // Redirect back to the previous page
        return back();
    }

    // to edit manager from the list
    public function editManager(Request $request, Admin $admin)
    {
        // Define the common validation rules and messages
        $commonRules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('admins')->ignore($request->input('edit_admin_id')),
            ],
        ];

        $commonMessages = [
            'name.required' => 'لطفا قم بإدخال الأسم',
            'email.required' => 'لطفا قم بإدخال البريد الإلكتروني',
            'email.email' => 'لطفا قم بإدخال بريد إلكتروني صحيح',
            'email.unique' => 'عنوان البريد الإلكتروني موجود مسبقا',
        ];

        // Check if password is provided
        if ($request->filled('password') || $request->filled('password_confirmation')) {
            // Add password validation rules and messages
            $commonRules['password'] = 'required|string|min:8|confirmed';

            $commonMessages = array_merge($commonMessages, [
                'password.required' => 'لطفا قم بإدخال كلمة المرور',
                'password.min' => 'يجب أن تكون كلمة المرور قوية ولا تقل عن 8 خانات',
                'password.confirmed' => 'يجب أن تتطابق كلمة المرور مع تأكيد كلمة المرور',
            ]);


            // Add confirmation password validation rules and messages
            $commonRules['password_confirmation'] = 'required|string';

            $commonMessages = array_merge($commonMessages, [
                'password_confirmation.required' => 'لطفا قم بإدخال تاكيد كلمة المرور'
            ]);
        }

        // Validate the request data using the common rules and messages
        $validated = $request->validate($commonRules, $commonMessages);

        // get the admin form database
        $admin = Admin::find($request->edit_admin_id);

        // Update the admin object with the validated input data
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];

        // Update the password if provided
        if ($request->filled('password')) {
            $admin->password = Hash::make($validated['password']);
        }

        // Save the changes to the admin object
        $admin->save();

        // Redirect back to the previous page
        return back();
    }

    // **************** these function in profile page *************
    // show the profile page
    public function profile()
    {
        return view('profile');
    }

    /******  Done  *************/
    // to edit name in profile page
    public function editName(Request $request)
    {
        // Validate the request data
        $validated = $request->validate(
            ['name' => ['required', 'string', 'max:255']]
        );

        // Update the admin's name
        $admin = auth()->user();
        $admin->name = $request->name;
        $admin->save();

        return back();
    }

    /******  Done  *************/
    // to change password in profile page
    public function changePassword(Request $request, Admin $admin)
    {
        // Validate the request data
        $validated = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check if the old password matches the authenticated admin's password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return response()->json(['message' => "كلمة المرور القديمة غير صحيحة "], 401);
        }

        // Update the admin's password
        Admin::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['success' => 'تم تغيير كلمة المرور بنجاح.']);
    }

    /******  Done  *************/
    // edit profile photo in profile page
    public function editProfilePhoto(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        // image processing
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image->isValid()) {
                // get admin data
                $admin = auth()->user();

                // Delete old file
                $oldImagePath = 'upload/profiles_photos/' . $admin->image;
                $oldThumbnailPath = 'upload/profiles_photos/thumbs/' . $admin->image;

                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }

                if ($oldThumbnailPath && Storage::disk('public')->exists($oldThumbnailPath)) {
                    Storage::disk('public')->delete($oldThumbnailPath);
                }

                // create image name
                $imageName = "admin_" . auth()->user()->id . "_" . now()->timestamp . '.' . $image->getClientOriginalExtension();
                // store image
                $image->storeAs('upload/profiles_photos/', $imageName, ['disk' => 'public']);

                // Generate thumbnail
                $thumbnailPath = '/storage/upload/profiles_photos/thumbs/' . $imageName;
                Image::make($image)->fit(200, 200)->save(public_path($thumbnailPath));

                // Update the image in the database
                $admin->image = $imageName;
                $admin->save();

                // Update the URL of the image to return it to the view
                $imageUrl = asset('storage/upload/profiles_photos/' . $imageName);
                $thumbUrl = asset('storage/upload/profiles_photos/thumbs/' . $imageName);


                // Return the updated image URLs
                return response()->json([
                    'url' => $imageUrl,
                    'thumbUrl' => $thumbUrl,
                ]);
            } else {
                // Invalid image file
                return response()->json(['image' => 'الملف غير صالح'], 422);
            }
        }
    }

}