<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Auth;
use Exception;
use App\Models\User;
use Carbon\Carbon;
use DB;

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
                Auth::user()->user_avatar = $user->avatar;
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
                    // echo "User tidak terdaftar";
                    $img = "<img style='max-width: 100px;border-radius: 50%;' src='$user->avatar'>";
                    $msg = "$img<br><br>Sorry, $user->name ($user->email)<br>is not registered.<br>Please contact the administrator!";
                    return view('user.error', compact('msg'));
                }
            }
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => 'Session ended, please try again!']);
        }
    }
}
