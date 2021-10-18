<?php

namespace App\Http\Controllers\API;

use App\Group;
use App\Http\Controllers\Controller;
use App\Http\Resources\LiveSessionCollection;
use App\LiveSession;
use App\User;
use Illuminate\Http\Request;

class LiveSessionController extends Controller
{
    public function index()
    {
        $sessions = LiveSession::all();
        $sessions->load('group');

        return LiveSessionCollection::collection($sessions);
    }

    public function userSessions()
    {
        $user = auth('sanctum')->user();
        $userGroups = $user->groups()->pluck('group_id');

        $sessions = LiveSession::whereIn('group_id',$userGroups)->get();
        $sessions->load('group');

        return LiveSessionCollection::collection($sessions);
    }

    public function create(Request $request)
    {
        $this->validateRequest($request);

        $session = LiveSession::create([
            'topic' => $request->topic,
            'description' => $request->description,
            'date' =>$request->date,
            'time' => $request->time,
            'group_id' => $request->group_id,
            'link' => $request->link
        ]);

        return response()->json(['success' => true, 'message' => 'Session created successfully!'],200);
    }

    public function update(Request $request)
    {
        $this->validateRequest($request);

        $session = LiveSession::whereId($request->id)->update($request->all());

        return response()->json(['success' => true, 'message' => 'Session updated successfully!'],200);

    }

    public function getAllGroups()
    {
        $groups = Group::all();

        return response()->json(['success' => true, 'message' => 'Session created successfully!',
            'data' => $groups
        ],200);

    }

    public function validateRequest($request)
    {
        $this->validate($request,[
            'topic' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|string',
            'group_id' => 'required|numeric',
            'link' => 'required|string',
        ]);
    }

    public function destroy($id)
    {

        $user = LiveSession::findOrFail($id);
        $user->delete();
        return response()->json(['success'=> true,'message' => 'data deleted']);
    }
}
