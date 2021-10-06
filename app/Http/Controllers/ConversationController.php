<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Pusher\PusherException;

class ConversationController extends Controller
{
    public function store()
    {

        try{
        $conversation = Conversation::create([
            'message' => request('message'),
            'group_id' => request('group_id'),
            'user_id' => auth()->user()->id,
        ]);

        event(new NewMessage($conversation))->toOthers();

        return response()->json($conversation->load('user'));
        }catch(\Throwable $e){

            if($e->getMessage() == "Cannot use object of type stdClass as array"){
                return response()->json($conversation->load('user'),200);
            }
        }
    }

    public function sendFile(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        if($request->file()) {
            $file_name = time().'_'.$request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

            try{
                $conversation = Conversation::create([
                    'type' => 'media',
                    'message' => url('/').'/storage/' .$file_path,
                    'group_id' => request('group_id'),
                    'user_id' => auth()->user()->id,
                ]);
                $conversation->message = $request->url() . $file_path;
                $type = 'media';
                event(new NewMessage($conversation,$type))->toOthers();

                return $conversation->load('user');
            }catch(\Throwable $e){

                if($e->getMessage() == "Cannot use object of type stdClass as array"){
                    return response()->json($conversation->load('user'),200);
                }
            }

            return response()->json(['success'=>'File uploaded successfully.']);
        }
    }
}
