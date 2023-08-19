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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class StoryMediaController extends Controller
{
    /**
     * Show all slides of the clicked story 
     */
    public function show(Story $story, StoryMedia $storyMedia)
    {
        // get the story slides in the specific story and order them by page no
        $slides = StoryMedia::where('story_id', $story->id)->orderBy('page_no')->get();

        // return data to blade file 
        return view('story_slides', compact('slides', 'story'));
    }

    /**
     * Remove diacritical from slide text and save it to the new column
     */
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

    /**
     * Add new slide
     */
    public function store(Request $request)
    {
        // validate from the story id 
        $validatedData = request()->validate(
            [
                'story_id' => 'required|exists:stories,id',
            ],
            [
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
        );

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            if ($image->isValid()) {
                // Get the uploaded file from the request

                // store image
                $image->storeAs('upload/slides_photos/', $imageName, ['disk' => 'public']);

                // Generate thumbnail
                $imagePath = '/storage/upload/slides_photos/' . $imageName;
                $thumbPath = '/storage/upload/slides_photos/thumbs/' . $imageName;
                Image::make($image)->fit(200, 200)->save(public_path($thumbPath));

                // create thumbnail

                $audio = $request->file('audio');
                $audioName = $audio->getClientOriginalName();

                if ($request->hasFile('audio')) {
                    $audio = $request->file('audio');
                    $audioName = $audio->getClientOriginalName();
                    if ($audio->isValid()) {
                        $audio->storeAs('upload/slides_sounds/', $audioName, ['disk' => 'public']);
                        $audioPath = '/storage/upload/slides_sounds/' . $audioName;
                        $slideText = $request->input('text');

                        try {
                            // here should store page number
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

                            $slide->save();
                        } catch (\Exception $e) {
                            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                                Storage::disk('public')->delete($imagePath);
                            }
                            if ($thumbPath && Storage::disk('public')->exists($thumbPath)) {
                                Storage::disk('public')->delete($thumbPath);
                            }
                            if ($audioPath && Storage::disk('public')->exists($audioPath)) {
                                Storage::disk('public')->delete($audioPath);
                            }
                            return response()->json(['error' => 'some thing wrong to insert slide'], 500);
                        }
                    }
                }
            }
        }
        // Return a response indicating success
        return response()->json(['success' => true, 'massege' => "تم إضافة صفحة جديدة بنجاح"]);
    }

    /**
     * Edit slide photo
     */
    public function editSlideImage(Request $request)
    {
        // data validation
        $validatedData = request()->validate(
            [
                'id' => 'required|exists:stories_media,id',
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

        );
        $id = $validatedData['id'];

        // Store the image on the server
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            if ($image->isValid()) {
                // store image
                $image->storeAs('upload/slides_photos/', $imageName, ['disk' => 'public']);

                // Generate thumbnail
                $thumbnailPath = '/storage/upload/slides_photos/thumbs/' . $imageName;
                Image::make($image)->fit(200, 200)->save(public_path($thumbnailPath));

                //get the row id 
                $story_media = StoryMedia::find($id);
                // to delete old image from files  and thumb file
                if ($story_media->image !== 'default.png') {
                    $thumbPath = '/storage/upload/slides_photos/thumbs/' . $story_media->image;
                    $imagePath = '/storage/upload/slides_photos/' . $story_media->image;

                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    if ($thumbPath && Storage::disk('public')->exists($thumbPath)) {
                        Storage::disk('public')->delete($thumbPath);
                    }
                }

                // Update the URL of the image in the database
                $imageUrl = asset('/storage/upload/slides_photos/' . $imageName);
                // Assuming you have a Model called Photo and the photo that you want to update has an ID of 1:
                $story_media->image = $imageName;
                $story_media->save();

                return response()->json(['url' => $imageUrl]);
            }
        }
    }

    /**
     * Edit slide Audio
     */
    public function editSlideAudio(Request $request)
    {
        // data validation
        $validatedData = request()->validate(
            [
                'id' => 'required|exists:stories_media,id',
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
        );
        $id = $validatedData['id'];

        // Store the audio file on the server
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $audioName = $audio->getClientOriginalName();
            if ($audio->isValid()) {
                $audio->storeAs('upload/slides_sounds/', $audioName, ['disk' => 'public']);

                // Get the row from the database
                $story_media = StoryMedia::find($id);

                // Delete old audio file from the directory
                // This if we have default value
                if ($story_media->audio !== 'default.mp3') {
                    $audioPath = '/storage/upload/slides_sounds/' . $story_media->audio;
                    if ($audioPath && Storage::disk('public')->exists($audioPath)) {
                        Storage::disk('public')->delete($audioPath);
                    }
                }

                // Update the URL of the audio file in the database
                $audioUrl = asset('/storage/upload/slides_sounds/' . $audioName);
                $story_media->audio = $audioName;
                $story_media->save();

                // return audio url
                return response()->json(['url' => $audioUrl]);
            }
        }
    }

    /**
     * Edit slide text
     */
    public function editSlideText(Request $request)
    {

        // data validation
        $validatedData =  $request->validate(
            [
                'id' => 'required|exists:stories_media,id',
                'text' => 'required|string'

            ]
        );

        $story_media = StoryMedia::find($request->id);
        $story_media->text = $request->text;
        // remove diacritical from slide text and save it to the new column
        $story_media->text_no_desc = $this->removeDiacritics($request->text);
        $story_media->save();

        return response()->json(['success' => true]);
    }

    /**
     * Delete slide
     */
    public function destroy(Request $request)
    {
        // data validation
        $validatedData =  $request->validate(
            [
                'del_slide_id' => 'required|exists:stories_media,id',
            ]
        );
        // get slide data 
        $slide = StoryMedia::find($request->del_slide_id);

        // delete all files
        $imagePath = '/storage/upload/slides_photos/' . $slide->image;
        $thumbPath = '/storage/upload/slides_photos/thumbs/' . $slide->image;
        $audioPath = '/storage/upload/slides_sounds/' . $slide->audio;


        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        if ($thumbPath && Storage::disk('public')->exists($thumbPath)) {
            Storage::disk('public')->delete($thumbPath);
        }
        if ($audioPath && Storage::disk('public')->exists($audioPath)) {
            Storage::disk('public')->delete($audioPath);
        }

        // delete slide 
        $slide->delete();
        return back();
    }

    // to change the order of story slid
    public function updateSlideOrder(Request $request)
    {
        $slideOrder = $request->slideOrder;

        // Loop through the slideOrder array and update the order in the database
        foreach ($slideOrder as $index => $slideId) {
            StoryMedia::where('id', $slideId)->update(['page_no' => $index]);
        }

        return response()->json(['message' => 'Slide order updated successfully']);
    }
}
