<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('auth.login');
// });
// Route::get('/', 'App\Http\Controllers\AdminController@show')->name('show');

Route::get('/', 'App\Http\Controllers\AdminController@login')->name('login');
Route::post('/login', 'App\Http\Controllers\AdminController@trylogin')->name('login');
Route::post('/login', 'App\Http\Controllers\AdminController@trylogin')->name('logout');


Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('profile');

// Route::get('/stories', 'App\Http\Controllers\StoryController@getAllStories')->name('stories');
// // Route::get('/stories', [App\Http\Controllers\StoryController::class, 'getAllStories'])->name('stories');

// Route::get('/stories/{level}', 'App\Http\Controllers\HomeController@index')->name('home');
// // Route::get('/story-slides/{story}', 'App\Http\Controllers\HomeController@index')->name('home');

// Route::get('/storieshh/{level}', 'App\Http\Controllers\HomeController@index')->name('storyslide');
// // Route::get('/stories/{level}', function () {
// //     return view('welcome');
// // });
// // Route::get('/story-slides/{story}', function () {
// //     return view('home');
// // });





// Route::get('/', function () {
//     return view('auth.login');
// });


// profile Routes 
// show profile page 
Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
// edit profile photo 
Route::post('/editProfilePhoto', [App\Http\Controllers\AdminController::class, 'editProfilePhoto'])->name('editProfilePhoto');
// edit profile name
Route::post('/editName', [App\Http\Controllers\AdminController::class, 'editName'])->name('editName');
// change the password 
Route::post('/changePassword', [App\Http\Controllers\AdminController::class, 'changePassword'])->name('changePassword');



// stories Routes
// show the story in the specific level send in the get parameter
Route::get('/stories', [App\Http\Controllers\StoryController::class, 'getAllStories'])->name('stories');
// add New Story 
Route::post('/addStory', [App\Http\Controllers\StoryController::class, 'addStory'])->name('addStory');
// edit Story 
Route::post('/editStory', [App\Http\Controllers\StoryController::class, 'editStory'])->name('editStory');
// delete story
Route::post('/deleteStory', [App\Http\Controllers\StoryController::class, 'deleteStory'])->name('deleteStory');
// to publish story
Route::post('/publishStory', [App\Http\Controllers\StoryController::class, 'publishStory'])->name('publishStory');
// get last order of the story depand on level 
Route::get('/get-last-order', [App\Http\Controllers\StoryController::class, 'getLastOrder'])->name('getLastOrder');


// Slides Routes
// show slides of the story
Route::get('/storyslide', [App\Http\Controllers\StoryMediaController::class, 'index'])->name('storyslide');
// delete slide
Route::post('/deleteSlide', [App\Http\Controllers\StoryMediaController::class, 'deleteSlide'])->name('deleteSlide');
// edit slide text
Route::post('/editSlideText', [App\Http\Controllers\StoryMediaController::class, 'editSlideText'])->name('editSlideText');
// edit slide photo
Route::post('/editSlidePhoto', [App\Http\Controllers\StoryMediaController::class, 'editSlidePhoto'])->name('editSlidePhoto');
// edit slide sound
Route::post('/editSlideSound', [App\Http\Controllers\StoryMediaController::class, 'editSlideSound'])->name('editSlideSound');
// add new slide
Route::post('/addNewSlide', [App\Http\Controllers\StoryMediaController::class, 'addNewSlide'])->name('addNewSlide');


// Routes for admin only
Route::middleware(['auth', 'admin'])->group(function () {
    //dashboard 
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'showChart'])->name('home');
    // manage all managers
    Route::get('/manage', [App\Http\Controllers\AdminController::class, 'show'])->name('manage');
    // add new manager
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
    // edit manager info
    Route::post('/editManager', [App\Http\Controllers\AdminController::class, 'editManager'])->name('editManager');
    // delete manager
    Route::post('/delete', [App\Http\Controllers\AdminController::class, 'delete'])->name('delete');
    // change the status of the manager
    Route::get('/adminChangeLocked', [App\Http\Controllers\AdminController::class, 'adminChangeLocked'])->name('delete');

    // check filed
    Route::post('/checkFiled', [App\Http\Controllers\StoryController::class, 'checkFiled'])->name('checkFiled');

});

// test
    // Route::get('/slide/{id}', [App\Http\Controllers\StoryMediaController::class, 'getSlideDetails'])->name('deleteStory');
