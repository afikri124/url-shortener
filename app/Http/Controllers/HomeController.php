<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function sso_klas2(Request $request)
    {
        try {
            if(Session::get('klas2_api_key') != $request->api_key){
                return redirect()->route('login')->withErrors(['msg' => 'API key expired, please try again!']);
            }
            Session::put('klas2_api_key', null);
            if($request->token == md5($request->api_key.$request->id) && "JGU".gmdate('Y/m/d') == Crypt::decrypt($request->api_key)){
                $user = User::where('username', $request->id)->first();
                if ($user != null) { //login
                    Auth::loginUsingId($user->id);
                    if(Auth::user()->password == null || Auth::user()->email == null){
                        return redirect()->route('update_account');
                    } else {
                        return redirect()->route('home');
                    }
                } else { //register
                    $user = User::where('email',$request->email)->first();
                    if($request->email == null){
                        $user = User::where('username',$request->id)->first();
                    }
                    if($user == null){
                        $new_user = false;
                        if($request->email == null || $request->email == ""){
                            $request->email = null;
                        }
                        $new_user=User::insert([
                                'name' => $request->name,
                                'email' => $request->email,
                                'username' => $request->id,
                                'password'=> Hash::make($request->id),
                                'email_verified_at' => Carbon::now(),
                                'created_at' => Carbon::now()
                        ]);
                        
                        if($new_user){
                            $user = User::where('username', $request->id)->first();
                            // if($request->dept_id == "ACAD"){
                                $user->roles()->attach(Role::where('id', 'ST')->first());
                            // }
                        }  
                    } else {
                        $old_user = $user->update([
                            'name' => $request->name,
                            'email' => $request->email,
                            'username' => $request->id,
                            'updated_at' => Carbon::now()
                        ]);
                        
                        if($old_user){
                            $user = User::where('username', $request->id)->first();
                            if(
                                // $request->dept_id == "ACAD" && 
                                !$user->hasRole('ST')){
                                $user->roles()->attach(Role::where('id', 'ST')->first());
                            }
                        }  
                    }
                    Auth::loginUsingId($user->id);
                    if(Auth::user()->username == null){
                        return redirect()->route('update_account');
                    } else {
                        return redirect()->route('my_profile');
                    }
                }
            } else {
                abort(403, "Cannot access to restricted page!");
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return redirect()->route('login')->withErrors(['msg' => $msg]);
        }
    }
}
