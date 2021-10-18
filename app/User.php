<?php

namespace App;

use App\Mail\OtpSend;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'otp',
        'fcm_token  ',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function createUser($validatedData)
    {
        $user = $this->create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'fcm_token' => $validatedData['fcm_token'],
            'otp' => mt_rand(1000, 9999),
            'password' => Hash::make($validatedData['password']),
        ]);
        $validatedData['user_id'] = $user->id;
        $this->createDetail($validatedData);
        return $user;
    }

    public function createDetail($validatedData)
    {
        $userDetail = UserDetail::create([
            'user_id' => $validatedData['user_id'],
//            'avatar' => $validatedData['avatar'],
            'race' => $validatedData['race'],
            'age_group' => $validatedData['age_group'],
            'nationality' => $validatedData['nationality']
        ]);
    }

    public function createOtp($user)
    {
        $data['otp'] = mt_rand(1000, 9999);
        $data['otp_valid_till'] = date('Y-m-d H:i:s', strtotime("+3 minutes"));
        return $user->update(['otp' => $data['otp']]);
    }

    public function sendOtp(User $user)
    {
        if($user->otpMethod == 'email'){
            return $this->sendOtpEmail($user->otp,$user->email);
        }else{
//            return $this->sendOtpSms();
        }
    }

    public function sendOtpEmail(string $otp, string $recipient)
    {
        $res = true;
        $data['otp'] = $otp;
        Mail::to($recipient)->send(new OtpSend($otp));
        try {

        } catch (\Exception $e) {

            $res = false;
        }
        return $res;
    }

    public function sendOtpSms(string $otp, string $recipient)
    {

    }

    public function eSignature()
    {
        return $this->hasOne('App\EmailSignature');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    public function checkCategory()
    {
        return (bool)$this->category()->exists();
    }


    public function detail()
    {
        return $this->hasOne('App\UserDetail');
    }

    public function answers()
    {
        return $this->hasMany('App\UserAnswer');
    }
    public function category()
    {
        return $this->hasMany('App\GroupUser');
    }
}
