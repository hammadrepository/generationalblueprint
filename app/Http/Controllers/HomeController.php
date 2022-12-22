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




    public function currentUser()
    {
        return Auth::check();
    }
}
