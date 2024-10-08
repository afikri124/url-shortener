<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Auth;
use Exception;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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
        return Socialite::driver('google')->with(["prompt" => "select_account"])->redirect();
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
            // dd($user);
            $finduser = User::where('google_id', $user->id)->first();
            if($finduser){
                Auth::loginUsingId($finduser->id);
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
                    } else {
                        abort(403, "Cannot access to restricted page!");
                    }
                } else {
                    $email = explode("@",$user->email);
                    if($email[1] == "jgu.ac.id" || $email[1] == "student.jgu.ac.id" || $email[1] == "itkj.ac.id"){
                        $findUsername = User::where('username', $email[0])->first();
                        if($findUsername){
                            $data = User::where('username', $email[0])->update([
                                'name' => strtoupper($user->name),
                                'email' => $user->email,
                                'username' => $email[0],
                                'google_id' => $user->id,
                                'password'=> Hash::make($user->email),
                                'email_verified_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        } else {
                            $data=User::create([
                                'name' => strtoupper($user->name),
                                'email' => $user->email,
                                'google_id' => $user->id,
                                'username' => null,
                                'password'=> Hash::make($user->email),
                                'email_verified_at' => Carbon::now(),
                                'created_at' => Carbon::now()
                            ]);
                        }
                        if($data){
                            $user = User::where('id', $data->id)->first();
                            if($email[1] == "student.jgu.ac.id"){
                                $user->roles()->attach(Role::where('id', 'SD')->first()); //Student
                            } else {
                                $user->roles()->attach(Role::where('id', 'ST')->first()); //Staff
                            }
                        }  
                        Auth::loginUsingId($data->id);
                    } else {
                        $usrnm = (strlen($user->email) > 36 ? substr($user->email,0,35) : $user->email);
                        $data=User::create([
                            'name' => strtoupper($user->name),
                            'email' => $user->email,
                            'username' => $usrnm,
                            'google_id' => $user->id,
                            'password'=> Hash::make($user->email),
                            'email_verified_at' => Carbon::now(),
                            'created_at' => Carbon::now()
                        ]);
                        if($data){
                            $user = User::where('id', $data->id)->first();
                            $user->roles()->attach(Role::where('id', 'GS')->first()); //Guest
                        }  
                        Auth::loginUsingId($data->id);
                        // $msg = "Sorry, $user->email not registered.<br>Please login using the official JGU email!";
                        // return redirect()->route('login')->withErrors(['msg' => $msg]);
                    }
                }
            }
            if( session()->has('url.intended')){
                $link = session('url.intended');
                session(['url.intended' => null]);
                Session::forget('url.intended');
                return redirect($link);
            } else {
                if(Auth::user()->username == null ){
                    return redirect()->route('user.edit');
                } else {
                    return redirect()->route('home');
                }
            }
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => 'Sesi kedaluwarsa, silakan ulangi lagi!']);
        }
    }
}
