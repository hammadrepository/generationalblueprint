<?php

namespace App\Http\Controllers\API;

use App\EmailSignature;
use App\Http\Controllers\Controller;
use App\Mail\SendEmailSignature;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use  App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
//        $this->middleware('api');
    }

    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
           'name' => 'required',
           'email' => 'required',
           'phone' => 'required',
           'password' => 'required',
        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'password' => Hash::make($request['password']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
//        $user->load('detail');
       $user->isAdmin =  auth()->id() == 1 ? true : false;
        return response()->json(['user' => $user]);
    }



    public function current_user()
    {
        $user = \auth()->user();
        $user = new HomeController();
        dd($user->currentUser());
;       return response()->json(['message'=>$user]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'fcm_token' => 'nullable',
            'device_name' => 'required',
        ]);
        try{
            $user = \App\User::where('email', $request->email)->first();
            if(isset($user) && $user->tokens()->count() >= 2){
                return $this->sendError('You cannot log in more than 2 devices!',[],200);
            }
            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);

            $token = $user->createToken($request->device_name)->plainTextToken;
            $response = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'group' => $user->category()->latest()->first()->category ?? null,
                'questions_completed'=> $user->checkCategory()
            ];

            $message = 'User logged in successfully!';
            return $this->sendResponse($response,$message);
        }catch (\Exception $e){

            return $this->sendError($e->getMessage(),$e->getMessage(),400);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|numeric',
            'password' => 'required',
            'fcm_token' => 'nullable',
            'race' => 'nullable',
            'nationality' => 'nullable',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
//             $path = $request->file('avatar')->storeAs(
//                'avatars', 'user_'.$validatedData['phone'].'.'.$request->avatar->getClientOriginalExtension()
//            );
        try{
            $user = new User;
            DB::beginTransaction();
            $createdUser = $user->createUser($validatedData);
            DB::commit();
            $token = $createdUser->createToken('auth_token')->plainTextToken;

            $response = [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
            $message = 'User created successfully!';
            return $this->sendResponse($response,$message);

        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),[],400);
        }
    }

    public function profile_update(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return ['message' => 'updated'];
    }

    public function returnImage($id) {
        $file =\App\EmailSignature::whereUserId($id)->first();
        $headers = array(
            'Content-Type:image/jpg',
        );
        $response = response()->file(public_path() . $file->image, $headers);
        return $response;
    }

    public function imageUpload(Request $request){

        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $fileUpload = new EmailSignature();

        if($request->file()) {
            $file_name = time().'_'.$request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
//            $fileUpload->user_id = $request->user_id;
//            $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
//            $fileUpload->image = '/storage/' . $file_path;
            EmailSignature::updateOrCreate([
                'user_id' => $request->user_id
            ],[
                'user_id' => $request->user_id,
                'name' => time().'_'.$request->file->getClientOriginalName(),
                'image' => '/storage/' . $file_path,
                'imageLink' => url('/') . '/imagefile/'.$request->user_id
            ]);
//            $fileUpload->save();

            return response()->json(['success'=>'File uploaded successfully.']);
        }
    }

    public function emailToUser($id)
    {
        $user = User::find($id);

        $data['imageLink'] = $user->eSignature->imageLink;
        $data['name'] = $user->name;
        \Mail::to('hammadrashid98@gmail.com')->send(new SendEmailSignature($data));
        return response()->json(['success'=>'Email sent successfully.'],200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return ['message' => 'updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return ['message' => 'data deleted'];
    }



    public function deleteToken($email)
    {
        $user  = User::where('email',$email)->first();
//        dd($user);
        $user->tokens()->delete();
        return response()->json(['success'=> true]);
    }
}
