<?php

use App\Conversation;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Broadcast::routes(['middleware' => ['auth:sanctum']]);
//Route::post('/broadcast',function (Request $request) {
//    $pusher = new Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));
//    return $pusher->socket_auth('private-groups.1', $request->request->get('socket_id'));
//});
Route::namespace('API')->group(function () {
    Route::post('/register', 'UserController@register');
    Route::post('/login', 'UserController@login');
    Route::post('/logout', 'UserController@logout');

    Route::middleware('auth:sanctum')->group(function (){

        Route::get('/questions','QuestionController@index');
        Route::post('/questions','QuestionController@storeQuestionAnswer');

    });

    Route::get('user/{id}','UserController@show');
    Route::get('/deleteToken/{email}','UserController@deleteToken');

    Route::post('conversations','ConversationController@store');
    Route::post('/contact','ContactController@contactUs');
});
Route::get('/chat/{id}', 'HomeController@loadChat')->name('chat');
Route::post('/banUser', 'API\UserController@banUser')->name('banUser');


Route::apiResource('conversations', 'API\ConversationController');

Route::apiResources(['user' => 'API\UserController']);

Route::get('/profile','API\UserController@current_user');
Route::put('/profiles/{id}','API\UserController@profile_update');
