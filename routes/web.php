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


Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');

// Route::get('/stories/{level}', 'App\Http\Controllers\HomeController@index')->name('home');
// Route::get('/story-slides/{story}', 'App\Http\Controllers\HomeController@index')->name('home');

// Route::get('/stories/{level}', function () {
//     return view('welcome');
// });
// Route::get('/story-slides/{story}', function () {
//     return view('home');
// });
