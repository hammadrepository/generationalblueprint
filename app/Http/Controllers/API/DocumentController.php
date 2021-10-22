<?php

namespace App\Http\Controllers\API;

use App\Group;
use App\GroupDocument;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $groupDocs = GroupDocument::all();

        $groupDocs->map(function($val){
            return $val->new = '<a target="blank" href="' . $val->link.'">'.$val->name. '</a>';
        });
        $groupDocs->load('group');
        return $this->sendResponse($groupDocs,'Success');
    }
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'group_id'  => 'required|numeric|exists:groups,id',
            'file'  => 'required|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ],[
            'group_id.required' => 'Please select group'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors(),422);
        }
        try{
            $group = Group::find($request->group_id);
            if($request->file()) {
                $file_extension = $request->file->getClientOriginalExtension();
                $file_original_name = $request->file->getClientOriginalName();
                $file_name = Str::uuid() .'.'. $file_extension;
                $file_path = $request->file('file')->storeAs('uploads/documents/' . $group->name . '/', $file_name, 'public');
            }
            $data = [
                'group_id' => $request->group_id,
                'file_name' => $file_original_name,
                'file_link' => $file_path,
                'file_type' => $file_extension,
            ];
            $groupDocument = new GroupDocument();
            $groupDocument->storeDocument($data);
            $message = 'File Uploaded Successfully!';
            return $this->sendResponse($result = [],$message );

        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),$e->getTraceAsString(),400);
        }

    }

    public function destroy($id)
    {
        $document = GroupDocument::findOrFail($id);
        $document->delete();
        return ['message' => 'data deleted'];
    }
}
