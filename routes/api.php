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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method, Access-Control-Request-Headers");
header("Access-Control-Allow-Credentials: true");

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
Route::get('/conversation/history/{id}', 'HomeController@loadChat')->name('conversation.history');
Route::post('/banUser', 'API\UserController@banUser')->name('banUser');

Route::post('/session/create', 'API\LiveSessionController@create');
Route::get('/sessions', 'API\LiveSessionController@index');
Route::get('/sessions/users', 'API\LiveSessionController@userSessions');
Route::post('/session/update', 'API\LiveSessionController@update')->name('session.update');
Route::get('/session/destroy/{id}', 'API\LiveSessionController@destroy')->name('session.delete');
Route::get('/session/groups', 'API\LiveSessionController@getAllGroups');

Route::post('conversation/sendFile','API\ConversationController@sendFile');

Route::get('/documents', 'API\DocumentController@index');
Route::post('document-upload', 'API\DocumentController@store');
Route::delete('document/{id}', 'API\DocumentController@destroy');
Route::apiResource('conversations', 'API\ConversationController');
Route::post('/otp/verify','API\UserController@verifyOtp')->name('otp.verify');
Route::post('/otp/resend','API\UserController@resendOtp')->name('otp.resend');
Route::get('/testClient',function (){
    $account_sid = getenv("TWILIO_SID");
    $auth_token = getenv("TWILIO_AUTH_TOKEN");
    $twilio_number = '+12152686931';
    $client = new Client($account_sid,$auth_token);
//    $client = new Client($account_sid, $auth_token);
    $client->messages->create('+18623397010',
        ['from' => '+15405017641', 'body' => 'Han be kallu. Kaisa dia?'] );
});
Route::apiResources(['user' => 'API\UserController']);

Route::get('/profile','API\UserController@current_user');
Route::put('/profiles/{id}','API\UserController@profile_update');
