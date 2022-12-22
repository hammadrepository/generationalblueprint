<?php

use App\Conversation;
use App\Events\NewMessage;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


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

        Route::get('/conversation/history/{id}', 'ConversationController@loadChat')->name('conversation.history');
        Route::post('/banUser', 'UserController@banUser')->name('banUser');

        Route::post('/session/create', 'LiveSessionController@create');
        Route::get('/sessions', 'LiveSessionController@index');
        Route::get('/sessions/users', 'LiveSessionController@userSessions');
        Route::post('/session/update', 'LiveSessionController@update')->name('session.update');
        Route::get('/session/destroy/{id}', 'LiveSessionController@destroy')->name('session.delete');
        Route::get('/session/groups', 'LiveSessionController@getAllGroups');

        Route::post('conversation/sendFile','ConversationController@sendFile');

        Route::get('/documents', 'DocumentController@index');
        Route::post('document-upload', 'DocumentController@store');
        Route::delete('document/{id}', 'DocumentController@destroy');
        Route::apiResource('conversations', 'ConversationController');
        Route::post('/otp/verify','UserController@verifyOtp')->name('otp.verify');
        Route::post('/otp/resend','UserController@resendOtp')->name('otp.resend');
        Route::apiResources(['user' => 'UserController']);

        Route::get('/profile','UserController@current_user');
        Route::put('/profiles/{id}','UserController@profile_update');
        Route::get('user/{id}','UserController@show');
        Route::post('conversations','ConversationController@store');
    });

    Route::post('/contact','ContactController@contactUs');
});

