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
use Twilio\Rest\Client;

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

        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required',
            'fcm_token' => 'nullable',
            'device_name' => 'required',
        ]);

        try{
            $user = \App\User::where('email', $request->email)->first();
            if(isset($user) && $user->tokens()->count() >= 2 ){
                if($user->otp_verified != 1){
                    $user->tokens()->delete();
                }else{
                    return $this->sendError('You cannot log in more than 2 devices!',[],200);
                }

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
                'group' => $user->groups()->first() ?? null,
                'questions_completed'=> $user->checkCategory(),
                'user' => $user
            ];

            $message = 'User logged in successfully!';
            return $this->sendResponse($response,$message);
        }catch (ValidationException $exception){
            return $this->sendError($exception->getMessage(),$exception,422);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(),$e,400);
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
            'age_group' => 'nullable',
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

            // Create token
            $token = $createdUser->createToken('auth_token')->plainTextToken;

            // Send Otp
            $createdUser->otpMethod = $request->otpMethod ?? '';
            $otp = $user->sendOtp($createdUser);

            $response = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'otp' => $createdUser->otp,
                'user' => $createdUser
            ];
            $message = 'User created successfully!';
            return $this->sendResponse($response,$message);

        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),[],200);
        }
    }

    public function logout(Request $request)
    {
        try{
            if($user = auth('sanctum')->user()){
                $user->currentAccessToken()->delete();
                $message = 'User logged out successfully!';
                return $this->sendResponse([],$message);
            }
            return $this->sendError('User is not logged in!',[],400);
        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),[],400);
        }
    }

    public function verifyOtp(Request $request)
    {
        $this->validate($request,
        [
            'otp_verified' => 'required|boolean'
        ]);
        try{
            $user = User::find(auth('sanctum')->id());
            $user->update([ 'otp_verified'=> 1 ]);
            $message = 'OTP verified successfully';
            return $this->sendResponse($response = [],$message);
        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),[],400);
        }

    }

    public function resendOtp(Request $request)
    {
        try{
            $user = auth('sanctum')->user();
            $userObj = new User();
            $userObj->createOtp($user);
            $user->otpMethod = $request->otpMethod ?? '';
            $user->sendOtp($user);

            return $this->sendResponse(['otp' => $user->otp], $message = 'Otp sent successfully');
        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),$e,200);
        }
        }


    public function profile_update(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return ['message' => 'updated'];
    }
    public function banUser(Request $request) {
        $user = User::find($request->id);
        $user->update(['status'=> $request->status]);
        event(new \App\Events\UserBan($user));
        return response()->json(['success' => true],200);

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
            EmailSignature::updateOrCreate([
                'user_id' => $request->user_id
            ],[
                'user_id' => $request->user_id,
                'name' => time().'_'.$request->file->getClientOriginalName(),
                'image' => '/storage/' . $file_path,
                'imageLink' => url('/') . '/imagefile/'.$request->user_id
            ]);
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
