<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Attendance;
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

    public function attendance($idd, Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'email'=> ['required'],
                'username' => ['required'],
                'location' => ['required'],
            ]);

            if(Auth::user()->username == null){
                User::where('id', Auth::user()->id)->update([
                    'username'=> $request->username,
                    'updated_at' => Carbon::now()
                ]);
            }

            $data = Attendance::create([
                'username' => $request->username,
                'location' => $request->location,
                'created_at' => Carbon::now()
            ]);
            return redirect()->route('attendance', ['id' => $idd])->with('msg','Absensi berhasil!');
        }
        $location = $idd;
        return view('attendance.index', compact('location'));
    }

    public function sso_klas2(Request $request)
    {
        try {
            if(Session::get('klas2_api_key') != $request->api_key){
                return redirect()->route('login')->withErrors(['msg' => 'Token API kedaluwarsa, silakan ulangi lagi!']);
            }
            Session::put('klas2_api_key', null);
            if($request->token == md5($request->api_key.$request->id) && "S.JGU".gmdate('Y/m/d') == Crypt::decrypt($request->api_key)){
                $user = User::where('username', $request->id)->first();
                if ($user != null) { //login
                    Auth::loginUsingId($user->id);
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
                                'name' => strtoupper($request->name),
                                'email' => $request->email,
                                'username' => $request->id,
                                'password'=> Hash::make($request->id),
                                'job'=> $request->job,
                                'gender'=> $request->gender,
                                'email_verified_at' => Carbon::now(),
                                'created_at' => Carbon::now()
                        ]);
                        
                        if($new_user){
                            $user = User::where('username', $request->id)->first();
                            $user->roles()->attach(Role::where('id', 'ST')->first());
                        }  
                    } else {
                        $old_user = $user->update([
                            'name' => $request->name,
                            'email' => $request->email,
                            'username' => $request->id,
                            'job'=> $request->job,
                            'gender'=> $request->gender,
                            'updated_at' => Carbon::now()
                        ]);
                        
                        if($old_user){
                            $user = User::where('username', $request->id)->first();
                            if(!$user->hasRole('ST')){
                                $user->roles()->attach(Role::where('id', 'ST')->first());
                            }
                        }  
                    }
                    Auth::loginUsingId($user->id);
                }
                if( session()->has('url.intended')){
                    $link = session('url.intended');
                    session(['url.intended' => null]);
                    return redirect($link);
                } else {
                    return redirect()->route('home');
                }
            } else {
                abort(403, "Tidak dapat mengakses halaman terbatas!");
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return redirect()->route('login')->withErrors(['msg' => $msg]);
        }
    }
}
