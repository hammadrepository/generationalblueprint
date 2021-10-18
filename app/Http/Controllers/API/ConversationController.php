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
                'user_id' => auth('sanctum')->id(),
            ]);

            event(new NewMessage($conversation))->dontBroadcastToCurrentUser();

            return response()->json($conversation->load('user'));
        }catch(\Throwable $e){

            if($e->getMessage() == "Cannot use object of type stdClass as array"){
                return response()->json($conversation->load('user'),200);
            }
        }
    }

    public function sendFile(Request $request)
    {
dd($request->all());
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
        ]);

        if($request->file()) {
            $file_name = request('group_id').time().'_'.$request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads/chat/'.auth('sanctum')->id().time() .'/', $file_name, 'public');

            try{
                $conversation = Conversation::create([
                    'type' => 'media',
                    'message' => route('storage').'/' .$file_path,
                    'group_id' => request('group_id'),
                    'user_id' => auth('sanctum')->id(),
                ]);
                $conversation->message = route('storage') .'/' . $file_path;
                $type = 'media';
                event(new NewMessage($conversation,$type))->toOthers();

                return $conversation->load('user');
            }catch(\Throwable $e){
//               Took this approach due to pusher package issue
                if($e->getMessage() == "Cannot use object of type stdClass as array"){
                    return response()->json($conversation->load('user'),200);
                }
            }

            return response()->json(['success'=>'File uploaded successfully.']);
        }
    }


}
