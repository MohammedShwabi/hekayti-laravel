<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use Intervention\Image\ImageManagerStatic as Image;

use App\Models\Story;
use App\Models\StoryMedia;
use App\Http\Requests\StoreStoryMediaRequest;
use App\Http\Requests\UpdateStoryMediaRequest;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class StoryMediaController extends Controller
{

    //this prevent rout coming from non-auth attempt 
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // show all slides of the clicked story 
    public function index(Request $request)
    {
        // validate from the story id 
        $validatedData = request()->validate(
            [
                'story_id' => 'required|exists:stories,id',
            ],
            [
                'story_id.required' => 'لطفا قم بإدخال رقم القصة',
                'story_id.exists' => 'لايوجد قصة بهذا الرقم',
            ]
        );

        // get the validate id 
        $story_id = $validatedData['story_id'];

        // get story data 
        $story = Story::find($story_id);

        // get the story slides in the specific story
        $slides = StoryMedia::where('story_id', $story_id)->get();

        // return data to blade file 
        return view('story_slides', compact('slides', 'story'));
    }

    //delete slide from the list
    public function deleteSlide(Request $request)
    {
        // get slide data 
        $slide = StoryMedia::find($request->slide_id);

        // get the media path
        $path = public_path('upload/slides_photos/' . $slide->image);
        $soundPathe = public_path('upload/slides_sounds/' . $slide->audio);
        $thumpPath = public_path('upload/slides_photos/thumbs/' . $slide->image);
        if (file_exists($path)) {
            unlink($path);
        }
        if (file_exists($thumpPath)) {
            unlink($thumpPath);
        }
        if (file_exists($soundPathe)) {
            unlink($soundPathe);
        }

        // delete slide 
        $slide->delete();
        return back();
    }

    // edit slide text 
    public function editSlideText(Request $request)
    {
        // validate from the id 
        $validatedData = request()->validate(
            [
                'id' => 'required|exists:stories_media,id',
            ],
            [
                'id.required' => 'لطفا قم بإدخال رقم الصفحة',
                'id.exists' => 'لايوجد صفحة بهذا الرقم',
            ]
        );
        $id = $validatedData['id'];

        // validate from the text
        $request->validate(
            [
                'text' => 'required|string'
            ],
            [
                'text' => 'لطفا قم بإدخال النص'
            ]
        );

        $story_media = StoryMedia::find($id);
        $story_media->text = $request->text;
        // remove diacritical from slide text and save it to the new column
        $story_media->text_no_desc = $this->removeDiacritics($request->text);
        $story_media->save();

        return response()->json(['success' => true]);
    }


    // edit slide photo  
    public function editSlideImage(Request $request)
    {
        // validate from the id 
        $validatedData = request()->validate(
            [
                'id' => 'required|exists:stories_media,id',
            ],
            [
                'id.required' => 'لطفا قم بإدخال رقم الصفحة',
                'id.exists' => 'لايوجد صفحة بهذا الرقم',
            ]
        );
        $id = $validatedData['id'];

        // Validate the uploaded file
        $request->validate(
            [
                'image' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) use ($request) {
                        $filename = $value->getClientOriginalName();
                        $existingStory = StoryMedia::where('image', $filename)->where('id', '<>', $request->id)->first();
                        if ($existingStory) {
                            $fail('هذه الصورة موجودة مسبقا');
                        }
                    },
                ],
            ],
            [
                'image.image' => 'يجب ان يكون الملف صورة ',
                'image.mimes' => 'فقط الانواع التالية متاحة jpeg, png, jpg, gif,svg',
                'image.max' => '2MB حجم الصورة اكبر ',
            ]
        );

        // Store the image on the server
        $image = $request->file('image');
        $imageName = $image->getClientOriginalName();
        // to replace image in files
        $image->move(public_path('upload/slides_photos/'), $imageName);

        // create thumbnail
        $thumbWidth = 200; // Set the width of the thumbnail
        $thumbHeight = null; // Set the height of the thumbnail to null to maintain aspect ratio
        $imagePath = public_path('upload/slides_photos/' . $imageName);
        $thumbPath = public_path('upload/slides_photos/thumbs/' . $imageName); // Set the path of the thumbnail
        $thumb = Image::make($imagePath)->resize($thumbWidth, $thumbHeight, function ($constraint) {
            $constraint->aspectRatio();
        });
        $thumb->save($thumbPath);

        //get the row id 
        $story_media = StoryMedia::find($id);
        //  this while generate error if you useing the same image so the default image will deleted and when you try again he can not find it 
        // to delete old image from files  and thumb file
        if ($story_media->image !== 'default.png') {
            $path = public_path('upload/slides_photos/' . $story_media->image);
            $thumbPath = public_path('upload/slides_photos/thumbs/' . $story_media->image);
            if (file_exists($path)) {
                unlink($path);
            }
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }

        // Update the URL of the image in the database
        $imageUrl = asset('upload/slides_photos/' . $imageName);
        // Assuming you have a Model called Photo and the photo that you want to update has an ID of 1:
        $story_media->image = $imageName;
        $story_media->save();

        return response()->json(['url' => $imageUrl]);
    }

    // edit slide Audio 
    public function editSlideAudio(Request $request)
    {
        // validate from the id 
        $validatedData = request()->validate(
            [
                'id' => 'required|exists:stories_media,id',
            ],
            [
                'id.required' => 'لطفا قم بإدخال رقم الصفحة',
                'id.exists' => 'لايوجد صفحة بهذا الرقم',
            ]
        );
        $id = $validatedData['id'];

        // Validate the uploaded file
        $request->validate(
            [
                'audio' => [
                    'required',
                    'file',
                    'mimes:mp3,wav,ogg',
                    'max:2048',
                    function ($attribute, $value, $fail) use ($request) {
                        $filename = $value->getClientOriginalName();
                        $existingStory = StoryMedia::where('audio', $filename)->where('id', '<>', $request->id)->first();
                        if ($existingStory) {
                            $fail('هذه الصوت موجودة مسبقا');
                        }
                    },
                ],
            ],
            [
                'audio.mimes' => 'فقط الانواع التالية متاحة mp3,wav',
            ]
        );

        // Store the audio file on the server
        $audio = $request->file('audio');
        $audioName = $audio->getClientOriginalName();
        // to replace audio file in the directory
        $audio->move(public_path('upload/slides_sounds/'), $audioName);

        // Get the row from the database
        $story_media = StoryMedia::find($id);
        // Delete old audio file from the directory
        // this if we have defult value
        if ($story_media->audio !== 'default.mp3') {
            $path = public_path('upload/slides_sounds/' . $story_media->audio);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Update the URL of the audio file in the database
        $audioUrl = asset('upload/slides_sounds/' . $audioName);
        $story_media->audio = $audioName;
        $story_media->save();

        // return audio url
        return response()->json(['url' => $audioUrl]);
    }

    // remove diacritical from slide text and save it to the new column
    public function removeDiacritics($str)
    {
        $cleanText = strtr($str, [
            // Diacritics
            'ّ' => '', // Tashdid (Shadda)
            'َ' => '', // Fatha
            'ً' => '', // Fathatan
            'ُ' => '', // Damma
            'ٌ' => '', // Dammatan
            'ِ' => '', // Kasra
            'ٍ' => '', // Kasratan
            'ْ' => '', // Sukun
            'ٓ' => '', // Maddah
            'ٰ' => '', // alfe_madd

            // alfe_hamza
            'أ' => 'ا', // alfe_hamza
            'إ' => 'ا', // alfe_kasra
            'آ' => 'ا', // alfe_madd

            // punctuation
            '،' => '', // Arabic comma
            '؛' => '', // Arabic semicolon
            '!' => '', // Exclamation mark
            '?' => '', // Question mark
            '؟' => '', // Arabic Question mark
            '.' => '', // Period
            ',' => '', // Comma
            ':' => '', // Comma
            '\'' => '', // single quote
            '"' => '', // double quote
        ]);

        return $cleanText;
    }

    // ****
    public function addNewSlide(Request $request)
    {
        // i need this to add page no
        // to get the last item 
        //$lastRow = $story_media::latest()->first();

        // validate from the story id 
        $validatedData = request()->validate(
            [
                'story_id' => 'required|exists:stories,id',
            ],
            [
                'story_id.required' => 'لطفا قم بإدخال رقم القصة',
                'story_id.exists' => 'لايوجد قصة بهذا الرقم',
            ]
        );
        $story_id = $validatedData['story_id'];

        $validated = $request->validate(
            [
                'image' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) use ($request) {
                        $filename = $value->getClientOriginalName();
                        $existingStory = StoryMedia::where('image', $filename)->first();
                        if ($existingStory) {
                            $fail('هذه الصورة موجودة مسبقا');
                        }
                    },
                ],

                // '' => 'required|file||max:2048|unique:stories_media,sound',
                'audio' => [
                    'required',
                    'file',
                    'mimes:mp3,wav,ogg',
                    'max:2048',
                    function ($attribute, $value, $fail) use ($request) {
                        $filename = $value->getClientOriginalName();
                        $existingStory = StoryMedia::where('audio', $filename)->first();
                        if ($existingStory) {
                            $fail('هذه الصوت موجودة مسبقا');
                        }
                    },
                ],

                'text' => ['required', 'string'],
            ],
            [
                'image.image' => 'يجب ان يكون الملف صورة ',
                'image.mimes' => 'فقط الانواع التالية متاحة jpeg, png, jpg, gif,svg',
                'image.max' => '2MB حجم الصورة اكبر ',
                'audio.mimes' => 'فقط الانواع التالية متاحة ogg,mp3,wav',
                'text' => 'لطفا قم بإدخال النص'

            ]
        );


        // Get the uploaded file from the request
        $image = $request->file('image');
        $imageName = $image->getClientOriginalName();
        // check if img file uploaded
        if ($image->move(public_path('upload/slides_photos/'), $imageName)) {

            // create thumbnail
            $thumbWidth = 200; // Set the width of the thumbnail
            $thumbHeight = null; // Set the height of the thumbnail to null to maintain aspect ratio
            $imagePath = public_path('upload/slides_photos/' . $imageName);
            $thumbPath = public_path('upload/slides_photos/thumbs/' . $imageName); // Set the path of the thumbnail
            $thumb = Image::make($imagePath)->resize($thumbWidth, $thumbHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumb->save($thumbPath);

            $audio = $request->file('audio');
            $audioName = $audio->getClientOriginalName();

            if ($audio->move(public_path('upload/slides_sounds/'), $audioName)) {

                $slideText = $request->input('text');

                // her to insert to DB
                // Create a new slide model and save it to the database
                try {
                    // to get the last number of page 
                    $new_page =  StoryMedia::where('story_id', $story_id)->max('page_no');

                    // to start numbering the page no from 0 and then increment it by one
                    $new_page = ($new_page !== null) ? $new_page + 1 : 0;


                    $slide = new StoryMedia;
                    $slide->page_no = $new_page;
                    $slide->story_id  = $story_id;
                    $slide->image = $imageName;
                    $slide->audio = $audioName;
                    $slide->text = $slideText;
                    // remove diacritical from slide text and save it to the new column
                    $slide->text_no_desc = $this->removeDiacritics($slideText);
                    // $slide->text_no_desc = $slideText;
                    $slide->save();
                } catch (\Exception $e) {
                    // if any error accrose then delete uploaded files from files
                    // $ImagePath = public_path('upload/slides_photos/', $imageName);
                    // $thumbPath = public_path('upload/slides_photos/thumbs/' . $imageName);
                    // if (file_exists($ImagePath)) {
                    //     unlink($ImagePath);
                    // }
                    // if (file_exists($thumbPath)) {
                    //     unlink($thumbPath);
                    // }
                    return response()->json(['error' => 'some thing wrong to insert slide'], 500);
                }
            } else {
                // if the sound not uplod
                $ImagePath = public_path('upload/slides_photos/', $imageName);
                $thumbPath = public_path('upload/slides_photos/thumbs/' . $imageName);
                $audioPath = public_path('upload/slides_sounds/', $audioName);
                if (file_exists($ImagePath)) {
                    unlink($ImagePath);
                    unlink($thumbPath);
                    unlink($audioPath);
                }
            }
        }

        // Return a response indicating success
        return response()->json(['success' => true, 'massege' => "تم إضافة صفحة جديدة بنجاح"]);
    }
}
