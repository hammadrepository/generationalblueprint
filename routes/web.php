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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/storage/',function (){})->name('storage');

Auth::routes();
Route::group(['middleware' => 'auth'], function() {

    Route::resource('groups', 'GroupController');
    Route::resource('conversations', 'ConversationController');
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/chat', 'HomeController@chat')->name('chat');
    Route::get('/chat/{id}', 'API\ConversationController@loadChat')->name('conversation.history');
    Route::get('/groupsList', 'API\ConversationController@groupsList')->name('groupsList');

    Route::get('/imagefile/{id}','API\UserController@returnImage');
    Route::post('/upload', 'ConversationController@sendFile')->name('upload');
    //Route::post('/upload', 'API\UserController@imageUpload')->name('upload');
    Route::get('/emailToUser/{id}', 'API\UserController@emailToUser')->name('emailToUser');

});

Route::get('/{any}', 'HomeController@index')->where('any', '.*');
