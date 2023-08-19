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

// non authenticated users 
Route::get('/', [App\Http\Controllers\AdminController::class, 'login'])->name('root');
Route::get('/login', [App\Http\Controllers\AdminController::class, 'login'])->name('login');
// check the login data
Route::post('/login', 'App\Http\Controllers\AdminController@tryLogin')->name('login');

Route::middleware(['auth'])->group(function () {
    // logout the user
    Route::post('/logout', 'App\Http\Controllers\AdminController@logout')->name('logout');

    /** profile Routes  */
    // show profile page 
    Route::get('/profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('profile');
    // edit profile name
    Route::post('/editName', [App\Http\Controllers\AdminController::class, 'editName'])->name('editName');
    // edit profile photo 
    Route::post('/editProfilePhoto', [App\Http\Controllers\AdminController::class, 'editProfilePhoto'])->name('editProfilePhoto');
    // change the password 
    Route::post('/changePassword', [App\Http\Controllers\AdminController::class, 'changePassword'])->name('changePassword');

    /** stories Routes  */
    // show the story in the specific level 
    Route::get('/stories/{level}', [App\Http\Controllers\StoryController::class, 'show'])->name('stories');
    // get last order of the story depend on level 
    Route::get('/getLastOrder', [App\Http\Controllers\StoryController::class, 'getLastOrder'])->name('getLastOrder');
    // add New Story 
    Route::post('/addStory', [App\Http\Controllers\StoryController::class, 'store'])->name('addStory');
    // edit Story 
    Route::post('/editStory', [App\Http\Controllers\StoryController::class, 'edit'])->name('editStory');
    // delete story
    Route::post('/deleteStory', [App\Http\Controllers\StoryController::class, 'destroy'])->name('deleteStory');

    /** Slides Routes  */
    // show slides of the story
    Route::get('/slide/{story}', [App\Http\Controllers\StoryMediaController::class, 'show'])->name('storyslide');
    // add new slide
    Route::post('/addNewSlide', [App\Http\Controllers\StoryMediaController::class, 'store'])->name('addNewSlide');
    // edit slide photo
    Route::post('/editSlideImage', [App\Http\Controllers\StoryMediaController::class, 'editSlideImage'])->name('editSlideImage');
    // edit slide sound
    Route::post('/editSlideAudio', [App\Http\Controllers\StoryMediaController::class, 'editSlideAudio'])->name('editSlideAudio');
    // edit slide text
    Route::post('/editSlideText', [App\Http\Controllers\StoryMediaController::class, 'editSlideText'])->name('editSlideText');
    // delete slide
    Route::post('/deleteSlide', [App\Http\Controllers\StoryMediaController::class, 'destroy'])->name('deleteSlide');
    // sort slid rout
    Route::post('/updateSlideOrder', [App\Http\Controllers\StoryMediaController::class, 'updateSlideOrder'])->name('updateSlideOrder');


    // Routes for admin only
    Route::middleware(['auth', 'admin'])->group(function () {
        //dashboard 
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'showChart'])->name('home');
        // to publish story
        Route::post('/publishStory', [App\Http\Controllers\StoryController::class, 'publishStory'])->name('publishStory');
        // manage all managers
        Route::get('/manage', [App\Http\Controllers\AdminController::class, 'show'])->name('manage');
        // add new manager
        Route::post('/register', [App\Http\Controllers\AdminController::class, 'store'])->name('register');
        // edit manager info
        Route::post('/editManager', [App\Http\Controllers\AdminController::class, 'update'])->name('editManager');
        // change the status of the manager
        Route::get('/adminChangeLocked', [App\Http\Controllers\AdminController::class, 'adminChangeLocked'])->name('delete');
        // delete manager
        Route::post('/deleteAdmin', [App\Http\Controllers\AdminController::class, 'destroy'])->name('delete-admin');
    });
});