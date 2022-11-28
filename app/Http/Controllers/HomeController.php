<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

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

    public function attendance($idd, $token, Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'email'=> ['required'],
                'username' => ['required'],
                'jabatan' => ['required'],
                'paraf' => ['required'],
            ]);

            if(Auth::user()->username == null){
                $user = User::where('username', $request->username)->first();
                if($user){
                    User::where('username', Auth::user()->username)->update([
                        'updated_at' => Carbon::now()
                    ]);
                } else {
                    User::where('id', Auth::user()->id)->update([
                        'username'=> $request->username,
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
            
            if(Auth::user()->email == null){
                User::where('id', Auth::user()->id)->update([
                    'email'=> $request->email,
                    'updated_at' => Carbon::now()
                ]);
            }
            
            if(Auth::user()->job != $request->jabatan){
                User::where('id', Auth::user()->id)->update([
                    'job'=> $request->jabatan,
                    'updated_at' => Carbon::now()
                ]);
            }

            $data = Attendance::create([
                'username' => (Auth::user()->username == null ? $request->username : Auth::user()->username),
                'activity_id' => $idd,
                'signature_img' => $request->paraf,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude
            ]);
            return redirect()->route('attendance', ['id' => $idd, 'token' => $token])->with('msg','Absensi berhasil!');
        }
        $data = AttendanceActivity::findOrFail($idd);
        $tok = $data->type."".$data->user_id."".($data->id+3);
        $check = Attendance::where('username',Auth::user()->username)->where('activity_id', $idd)->first();
        if(strtolower($tok) != strtolower($token)){
            abort(403, "Token tidak valid!");
        } else {
            return view('attendance.index', compact('data','check'));
        }
    }

    public function sso_klas2(Request $request)
    {
        try {
            if(Session::get('klas2_api_key') != $request->api_key){
                return redirect()->route('login')->withErrors(['msg' => 'Token API kedaluwarsa, silakan ulangi lagi!']);
            }
            Session::put('klas2_api_key', null);
            if($request->token == md5($request->api_key.$request->id) && env('APP_KEY').gmdate('Y/m/d') == Crypt::decrypt($request->api_key)){
                $user = User::where('username', $request->id)->first();
                if ($user != null) { //login
                    if(($request->dept_id  != "STUDENT" && $user->back_title == null) || $user->job == null){
                        User::where('id',$user->id)->update([
                            'front_title' => $request->front_title,
                            'back_title' => $request->back_title,
                            'job'=> $request->job,
                            'gender'=> $request->gender,
                            'updated_at' => Carbon::now()
                        ]);
                        // dd($request);
                    }
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
                                'front_title' => $request->front_title,
                                'back_title' => $request->back_title,
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
                            if($request->dept_id == "ACAD" || $request->dept_id == "NACAD"){
                                $user->roles()->attach(Role::where('id', 'ST')->first());
                            } elseif ($request->dept_id == "STUDENT") {
                                $user->roles()->attach(Role::where('id', 'SD')->first());
                            }
                        }  
                    } else {
                        $old_user = $user->update([
                            'name' => $request->name,
                            'front_title' => $request->front_title,
                            'back_title' => $request->back_title,
                            'email' => $request->email,
                            'username' => $request->id,
                            'job'=> $request->job,
                            'gender'=> $request->gender,
                            'updated_at' => Carbon::now()
                        ]);
                        
                        if($old_user){
                            $user = User::where('username', $request->id)->first();
                            if(($request->dept_id == "ACAD" || $request->dept_id == "NACAD") && !$user->hasRole('ST')){
                                $user->roles()->attach(Role::where('id', 'ST')->first());
                            } else if($request->dept_id == "STUDENT" && !$user->hasRole('SD')){
                                $user->roles()->attach(Role::where('id', 'SD')->first());
                            }
                        }  
                    }
                    Auth::loginUsingId($user->id);
                }
                if( session()->has('url.intended')){
                    $link = session('url.intended');
                    session(['url.intended' => null]);
                    Session::forget('url.intended');
                    return redirect($link);
                } else {
                    return redirect()->route('home');
                }
            } else {
                abort(403, "Unable to access restricted pages!");
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return redirect()->route('login')->withErrors(['msg' => $msg]);
        }
    }

}
