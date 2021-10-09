<?php

namespace App\Http\Controllers\API;

use App\Conversation;
use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function store()
    {
        try{
            $conversation = Conversation::create([
                'message' => request('message'),
                'group_id' => request('group_id'),
                'user_id' => request('user_id'),
            ]);

            event(new NewMessage($conversation))->dontBroadcastToCurrentUser();

            return response()->json($conversation->load('user'));
        }catch(\Throwable $e){

            if($e->getMessage() == "Cannot use object of type stdClass as array"){
                return response()->json($conversation->load('user'),200);
            }
        }
    }

}
