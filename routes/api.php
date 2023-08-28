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
Route::get('/get-all-stories', [App\Http\Controllers\Api\StoryApiController::class, 'getStories'])->name('get-all-stories');
Route::get('/get-all-storiesMedia', [App\Http\Controllers\Api\StoryApiController::class, 'getStoriesMedia'])->name('get-all-storiesMedia');

Route::post('signup', [UserController::class, 'signup']); //to signup and add user to the user table
Route::post('login', [UserController::class, 'login']); //to login and get the user information
Route::post('updateUser', [UserController::class, 'updateUser']); //to update the user table

Route::post('accuracy', [AccuracyController::class, 'getAccuracy']); //to get the accuracy table
Route::post('updateAccuracy', [AccuracyController::class, 'updateAccuracy']); //to update the accuracy table
Route::post('uploadAccuracy', [AccuracyController::class, 'addAccuracy']); //to add to the accuracy table

Route::post('completion', [CompletionController::class, 'getCompletion']); //to get the completion table
Route::post('updateCompletion', [CompletionController::class, 'updateCompletion']); //to update the completion table
Route::post('uploadCompletion', [CompletionController::class, 'addCompletion']);//to add to the completion table
