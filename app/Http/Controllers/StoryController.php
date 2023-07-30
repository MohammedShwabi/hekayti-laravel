<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\StoryMedia;
use Illuminate\Http\File;
// use App\Http\Requests\StoreStoryRequest;
// use App\Http\Requests\UpdateStoryRequest;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoryController extends Controller
{

    // this prevent rout coming from non-auth attempt 
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get all stories in the level
    public function getAllStories(Request $request, Story $story, StoryMedia $storyMedia)
    {
        $level = $request->query('level');
        $search = $request->query('search');

        // Construct the query to retrieve stories
        $query = $story::where('level', $level)->when($search, function ($query, $search) {
            // get only the name that match the search
            return $query->where('name', 'like', '%' . $search . '%');
        })->orderBy('story_order', 'asc');

        if ($request->ajax()) {
            // Fetch a maximum of 8 stories names for Ajax response
            return $query->take(8)->pluck('name');
        }

        // Fetch all stories for non-Ajax response
        $stories = $query->get();

        // Check if story has media
        $stories = $query->get()->each(function ($story) use ($storyMedia) {
            $story->hasMedia = $storyMedia->where('story_id', $story->id)->exists();
        });

        // Pass stories and search query to the view
        return view('stories', compact('stories', 'level', 'search'));
    }

    // later
    // add story to the database
    public function addStory(Request $request, Story $story)
    {
        $validated = $request->validate(
            [
                'level' => ['required', 'integer'],
                'cover_photo' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) use ($request) {
                        $filename = $value->getClientOriginalName();
                        $existingStory = Story::where('cover_photo', $filename)->first();
                        if ($existingStory) {
                            $fail('هذه الصورة موجودة مسبقا');
                        }
                    },
                ],
                'name' => 'required|string|max:255|unique:stories,name',
                'author' => ['required', 'string'],
                // this to validate from the story_order field and check if its not greeter than max story_order +1
                'story_order' => [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) use ($request) {
                        $level = $request->input('level');
                        $maxOrder = Story::where('level', $level)->max('story_order') + 1;
                        if ($value > $maxOrder) {
                            $fail("يجب أن يكون رقم القصة = $maxOrder أو أصغر إذا أردت إعادة ترتيب القصص");
                        }
                    },
                ],
            ],
            [
                'level.required' => 'لطفا قم بإدخال رقم المستوى',
                'level.integer' => 'يجب ان يكون رقم المستوى رقما',
                'cover_photo.required' => 'لطفا قم بإدخال صورة الغلاف',
                'cover_photo.image' => 'يجب ان يكون الملف صورة ',
                'cover_photo.mimes' => 'فقط الانواع التالية متاحة jpeg, png, jpg, gif,svg',
                'cover_photo.max' => '2MB حجم الصورة اكبر ',
                'cover_photo.unique' => 'هذا الصورة موجودة مسبقا',
                'name.required' => 'لطفا قم بإدخال إسم القصة',
                'name.unique' => 'هذا الاسم موجود مسبقا',
                'author.required' => 'لطفا قم بإدخال إسم المؤلف',
                'story_order.required' => 'لطفا قم بإدخال رقم القصة في المستوى',
                'story_order.integer' => '  يجيب ان يكون رقم القصة رقما صحيحا',
                'story_order.min' => '  يجيب ان يكون رقم القصة 1 او اكثر',
            ]
        );

        $imagename = '';

        if ($request->hasFile('cover_photo')) {
            $image = $request->file('cover_photo');
            // to get uniqe name
            $imagename = $image->getClientOriginalName();
            // check if file exist 

            $image->move(public_path('upload/stories_covers/'), $imagename);

            $story_order = $request->input('story_order');
            $level = $request->input('level');

            // check if the story_order already exists for the same level
            $existingStory = $story->where('level', $level)->where('story_order', $story_order)->first();

            if ($existingStory) {
                // shift the story_order values of the other stories in the same level
                $storiesToUpdate = $story->where('level', $level)
                    ->where('story_order', '>=', $story_order)
                    ->orderBy('story_order', 'desc')
                    ->get();
                foreach ($storiesToUpdate as $storyToUpdate) {
                    $storyToUpdate->story_order = $storyToUpdate->story_order + 1;
                    if ($storyToUpdate->story_order == 1 || $storyToUpdate->story_order == 2) {
                        $storyToUpdate->required_stars = 0;
                    } else {
                        $storyToUpdate->required_stars = ($storyToUpdate->story_order - 1) * 2;
                    }
                    $storyToUpdate->save();
                }
            }

            // create the new story
            try {
                $story->create([
                    'name' => $request->name,
                    'cover_photo' => $imagename,
                    'author' => $request->author,
                    'level' => $level,
                    'story_order' => $story_order,
                    'required_stars' => ($story_order == 1 || $story_order == 2) ? 0 : (($story_order - 1) * 2),
                ]);
                return response()->json(['success' => 'it\'s successful']);
            } catch (\Exception $e) {
                // if any error accrose then delete uploaded files from files
                $ImagePath = public_path('upload/stories_covers/', $imagename);
                if (file_exists($ImagePath)) {
                    unlink($ImagePath);
                }
                return response()->json(['error' => 'some thing wrong to insert slide'], 500);
            }
        }
    }

    // to delete story
    public function deleteStory(Request $request)
    {
        // Retrieve the story by ID
        $story = Story::findOrFail($request->story_id);

        $slides = $story->storyMedia;

        // Delete all related slides and their associated files
        $slides->each(function ($slide) {
            $slidePhotoPath = public_path('upload/slides_photos/' . $slide->photo);
            $slidePhotoThumbPath = public_path('upload/slides_photos/thumbs/' . $slide->photo);
            $slideSoundPath = public_path('upload/slides_sounds/' . $slide->sound);

            // Delete the slide photo file if it exists
            file_exists($slidePhotoPath) && unlink($slidePhotoPath);

            // Delete the slide thumb photo file if it exists
            file_exists($slidePhotoThumbPath) && unlink($slidePhotoThumbPath);

            // Delete the slide sound file if it exists
            file_exists($slideSoundPath) && unlink($slideSoundPath);

            $slide->delete();
        });

        // get the stroy cover photo
        $coverPhotoPath = public_path('upload/stories_covers/' . $story->cover_photo);

        // Delete the cover photo for the story
        file_exists($coverPhotoPath) && unlink($coverPhotoPath);

        // Delete the story record
        $story->delete();

        return back();
    }

    // to publish story
    public function publishStory(Request $request)
    {
        Story::where('id', $request->story_id)->update(['published' => 1]);
        return back();
    }

    // later
    // to edit story
    public function editStory(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required', 'string', 'max:255',
                    Rule::unique('stories')->ignore(request('edit_story_id')),
                ],
                'author' => ['required', 'string', 'max:255'],
                'story_order' => [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) use ($request) {
                        $level = $request->input('level');
                        $maxOrder = Story::where('level', $level)->max('story_order') + 1;
                        if ($value > $maxOrder) {
                            $fail("يجب أن يكون رقم القصة = $maxOrder أو أصغر إذا أردت إعادة ترتيب القصص");
                        }
                    },
                ],
            ],
            [
                'name.required' => 'لطفا قم بإدخال إسم القصة',
                'name.unique' => 'هذا الاسم موجود مسبقا',
                'author.required' => 'لطفا قم بإدخال إسم المؤلف',
                'story_order.required' => 'لطفا قم بإدخال رقم القصة في المستوى',
                'story_order.integer' => 'يجيب ان يكون رقم القصة رقما',
            ]
        );
        // get all info about the spicific story
        $story = Story::find($request->edit_story_id);

        $imagename = null;
        // if there is an image 
        if ($request->hasFile('cover_photo')) {
            $validated = $request->validate(
                [
                    'cover_photo' => [
                        'required',
                        'image',
                        'mimes:jpeg,png,jpg,gif',
                        'max:2048',
                        function ($attribute, $value, $fail) use ($request) {
                            $filename = $value->getClientOriginalName();
                            $existingStory = Story::where('cover_photo', $filename)->where('id', '<>', $request->edit_story_id)->first();
                            if ($existingStory) {
                                $fail('هذه الصورة موجودة مسبقا');
                            }
                        },
                    ],

                ],
                [
                    'cover_photo.image' => 'يجب ان يكون الملف صورة ',
                    'cover_photo.mimes' => 'فقط الانواع التالية متاحة jpeg, png, jpg, gif,svg',
                    'cover_photo.max' => '2MB حجم الصورة اكبر ',
                ]
            );
            $image = $request->file('cover_photo');
            // to get uniqe name
            $imagename = $image->getClientOriginalName();

            // to replace image in files
            $image->move(public_path('upload/stories_covers/'), $imagename);

            // to delete old image from files
            $path = public_path('upload/stories_covers/' . $story->cover_photo);
            if (file_exists($path)) {
                unlink($path);
            }
        }
        if ($imagename !== null) {
            // to edit the image in DB
            $story->cover_photo = $imagename;
        }
        $story->name = $request->name;
        $story->author = $request->author;

        // check if story_order or level has been changed
        if ($story->story_order != $request->story_order || $story->level != $request->level) {
            // move the story to the new position and update the story_order and level values of the other stories
            $existingStory = Story::where('level', $request->level)->where('story_order', $request->story_order)->first();
            if ($existingStory) {
                // shift the story_order values of the other stories in the same level
                $storiesToUpdate = Story::where('level', $request->level)
                    ->where('story_order', '>=', $request->story_order)
                    ->orderBy('story_order', 'desc')
                    ->get();
                foreach ($storiesToUpdate as $storyToUpdate) {
                    if ($storyToUpdate->id != $story->id) {
                        $storyToUpdate->story_order = $storyToUpdate->story_order + 1;
                        if ($storyToUpdate->story_order == 1 || $storyToUpdate->story_order == 2) {
                            $storyToUpdate->required_stars = 0;
                        } else {
                            $storyToUpdate->required_stars = ($storyToUpdate->story_order - 1) * 2;
                        }
                        $storyToUpdate->save();
                    }
                }
            }
            $story->story_order = $request->story_order;
            $story->level = $request->level;
        }

        // update the required_stars field
        $story->required_stars = ($story->story_order == 1 || $story->story_order == 2) ? 0 : (($story->story_order - 1) * 2);
        $story->save();
        return back();
    }

    // to get the last story_order
    public function getLastOrder(Request $request)
    {
        // Get the level value from the request
        $level = $request->input('level');

        // Get the last story_order value for the specified level
        $lastOrder = Story::where('level', $level)->max('story_order') ?? 0;

        // Return the last story_order value as a JSON response
        return response()->json($lastOrder);
    }

    // to check story one filed
    public function checkFiled(Request $request, Story $story)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255|unique:stories,name',
        ], [
            'value.required' => 'لطفا قم بإدخال إسم القصة',
            'value.unique' => 'هذا الاسم موجود مسبقا',
        ]);
    }

}
