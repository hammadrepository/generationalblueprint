<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

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
        $chat = Conversation::where('group_id',$id)->orderBy('id', 'DESC')->orderBy('created_at', 'DESC')->paginate(10);
        $chat->load('user','group');

        return response()->json(collect($chat)->reverse());
    }
    public function currentUser()
    {
        return Auth::check();
    }
}
