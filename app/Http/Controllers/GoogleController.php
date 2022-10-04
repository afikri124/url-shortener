<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Auth;
use Exception;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
       
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleCallback()
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => $e->getMessage() ]);
        }
        try {
            $user = Socialite::driver('google')->user();
            $finduser = User::where('google_id', $user->id)->first();
            if($finduser){
                Auth::loginUsingId($finduser->id);
                return redirect()->route('home');
            }else{
                $findemail = User::where('email', $user->email)->first();
                if($findemail){
                    $update_user = User::where('email',$user->email)->update([
                        'google_id' => $user->id,
                        'updated_at' => Carbon::now()
                    ]);
                    if($update_user){
                        Auth::loginUsingId($finduser->id);
                        Auth::user()->user_avatar = $user->avatar;
                        return redirect()->route('home');
                    } else {
                        abort(403, "Cannot access to restricted page!");
                    }
                } else {
                    $email = explode("@",$user->email);
                    if($email[1] == "jgu.ac.id" || $email[1] == "student.jgu.ac.id" ){
                        $data=User::create([
                            'name' => $user->name,
                            'email' => $user->email,
                            'username' => null,
                            'password'=> Hash::make($user->email),
                            'email_verified_at' => Carbon::now(),
                            'created_at' => Carbon::now()
                        ]);
                        Auth::loginUsingId($data->id);
                        return redirect()->route('home');
                    } else {
                        $msg = "Maaf, $user->email tidak terdaftar.<br>Silahkan login menggunakan email resmi JGU!";
                        return redirect()->route('login')->withErrors(['msg' => $msg]);
                    }
                }
            }
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => 'Sesi Kedaluwarsa, silahkan ulangi lagi!']);
        }
    }
}
