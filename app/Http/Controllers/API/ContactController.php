<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function contactUs(Request $request) {

        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone'=>'required|numeric',
            'message' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(),$validator->errors());
        }
        //  Store data in database
        $request->request->add(['user_id' => auth('sanctum')->id()]);
        ContactUs::create($request->all());

        //  Send mail to Application Admin
//        \Mail::send('emails.contactemail', array(
//            'name' => $request->get('name'),
//            'email' => $request->get('email'),
//            'subject' => $request->get('subject'),
//            'bodyMessage' => $request->get('message'),
//        ), function($message) use ($request){
//            $message->from($request->email);
//            $message->to('troposal.com@gmail.com', 'Admin')->subject($request->get('subject'));
//        });
        return $this->sendResponse($response=[],$message = 'Message sent successfully!');
    }

}
