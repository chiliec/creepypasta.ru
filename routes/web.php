<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('users', 'UserController@index')->name('users.index');
Route::get('users/{id}', 'UserController@show')->name('users.show');

Route::redirect('/', '/posts');
Route::redirect('/home', '/posts');
Route::get('posts/{id}-{slug}.html', 'PostController@detail')->name('posts.detail');
Route::get('posts/{post}/vote/{reaction}', 'PostController@vote')->name('posts.vote');
Route::get('tag/{tag}', 'PostController@tag')->name('posts.tag');
Route::resource('posts', 'PostController');

Route::prefix('profile')->group(function () {
    Route::get('invites', 'InviteController@index')->name('invites.index');
    Route::get('invites/new', 'InviteController@store')->name('invites.new');
});

Route::get('fetch-link', 'LinkController@fetch')->name('fetch-link');
