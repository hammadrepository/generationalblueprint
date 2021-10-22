<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Group;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Exception;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
//    public function index()
//    {
//        return view('layouts.master');
//    }
    public function index()
    {
        $groups = auth()->user()->groups;

        $users = User::where('id', '<>', auth()->user()->id)->get();
        $user = auth()->user();

        return view('home', ['groups' => $groups, 'users' => $users, 'user' => $user]);
    }

    public function groupsList()
    {
        $groups = auth()->user()->groups;

        $users = User::where('id', '<>', auth()->user()->id)->get();
        $user = auth()->user();
        $response = [
            'groups'=> $groups,
            'user' => $user
        ];
        return response()->json($response);
    }

    public function chat()
    {
        $groups = auth()->user()->groups;

        $users = User::where('id', '<>', auth()->user()->id)->get();
        $user = auth()->user();

        return view('chat', ['groups' => $groups, 'users' => $users, 'user' => $user]);
    }

    public function loadChat($id)
    {
        try{
        $chat = Conversation::where('group_id',$id)->orderBy('id', 'DESC')->orderBy('created_at', 'DESC')->paginate(10);
        $ban =  collect(['user_ban' => !auth('sanctum')->user()->status]);
        if(count($chat) > 0){

            $chat->load('user','group');
//            dd(auth('sanctum')->user()->status);

        }else{
            $group = Group::find($id);
            return response()->json(collect($group)->merge($ban));
        }


        return response()->json(collect($chat)->reverse()->merge($ban));

        }catch(Exception $e){
            return $this->sendError($e->getMessage(),$e,400);
        }
    }
    public function currentUser()
    {
        return Auth::check();
    }
}
