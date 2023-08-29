<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/** Stories Routes  */
// get all stories
Route::get('/get-all-stories', [App\Http\Controllers\Api\StoryApiController::class, 'getStories'])->name('get-all-stories');
// get all story media
Route::get('/get-all-storiesMedia', [App\Http\Controllers\Api\StoryApiController::class, 'getStoriesMedia'])->name('get-all-storiesMedia');

/** User Routes  */
// signup user
Route::post('/signup', [App\Http\Controllers\Api\UserController::class, 'signup'])->name('signup');
// login user
Route::post('/login', [App\Http\Controllers\Api\UserController::class, 'login'])->name('login');
// update user
Route::post('/update-user', [App\Http\Controllers\Api\UserController::class, 'update'])->name('update-user');

/** Accuracy Routes  */
// get accuracy of the user
Route::get('/get-accuracy', [App\Http\Controllers\Api\AccuracyController::class, 'getAccuracy'])->name('get-accuracy');
// add accuracy to user
Route::post('/upload-accuracy', [App\Http\Controllers\Api\AccuracyController::class, 'store'])->name('upload-accuracy');
// update accuracy of the user
Route::post('/update-accuracy', [App\Http\Controllers\Api\AccuracyController::class, 'update'])->name('upload-accuracy');

/** Completion Routes  */
// get completion of the user
Route::get('/get-completion', [App\Http\Controllers\Api\CompletionController::class, 'getCompletion'])->name('get-completion');
// add completion to user
Route::post('/upload-completion', [App\Http\Controllers\Api\CompletionController::class, 'store'])->name('upload-completion');
// update completion to user
Route::post('/update-completion', [App\Http\Controllers\Api\CompletionController::class, 'update'])->name('update-completion');
