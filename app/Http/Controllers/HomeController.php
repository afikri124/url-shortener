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
use DB;
use App\Mail\WeeklyAttendanceReportMail;
use App\Models\DocDepartment;
use App\Models\DocPIC;
use App\Jobs\JobNotificationWA;

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

            $check = Attendance::where('activity_id', $idd)->where('username', Auth::user()->username)->first();
            if (!$check) {
                $data = Attendance::create([
                    'username' => (Auth::user()->username == null ? $request->username : Auth::user()->username),
                    'activity_id' => $idd,
                    'signature_img' => $request->paraf,
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude
                ]);
            }
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
                            $request->email = $request->id."@jgu.ac.id";
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

    public function tes(){
        $hr   = DocDepartment::where('name','Wakil Rektor II')->first();
        if($hr){
            $data['email'] = $hr->email;
            $data['name'] = $hr->name;
        } else {
            $data['email'] = "no-reply@jgu.ac.id";
            $data['name'] = "(REPORT ATT ERROR)";
        }
        $data['item1'] = array();
        $data['item2'] = array();
        $date_start = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $today = new Carbon();
        if($today->dayOfWeek <= Carbon::FRIDAY){
            $date_end = $today->endOfDay();
        } else {
            $date_end =  Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)->endOfDay();
        }
        $diff = $date_start->diffInDays($date_end);
        $data['period'] = $date_start->format('Y-m-d')." - ".$date_end->format('Y-m-d');
        $period = $date_start->format('d M Y')." s/d ".$date_end->format('d M Y');
        $data['subject'] = "Absensi Karyawan (".$period.")";
        $data['messages'] = "Berikut ini merupakan data karyawan yang pernah <b>TIDAK MASUK</b> berdasarkan mesin absen dalam minggu ini (<b>".$period."</b>) :";
        $x = DB::select( DB::raw("SELECT IFNULL(u.username,u.`username_old`) AS ID, u.name, IFNULL(tt.days,0) AS hari, u.group_id
          FROM 
          (SELECT u.username, COUNT(DISTINCT(DATE(a.`timestamp`))) AS days
          FROM wh_users u
          JOIN wh_attendances a ON u.`username` = a.`username` 
          WHERE a.`timestamp` >= '".$date_start."' && a.`timestamp` <= '".$date_end."'
          GROUP BY u.`username`) AS tt
          RIGHT JOIN wh_users u ON tt.username = u.username
          WHERE u.`status` = 1 && IFNULL(tt.days,0) <= ".$diff." && (u.group_id = 'JF' OR u.group_id = 'JE')
          ORDER BY u.group_id DESC, hari, u.name") );
        foreach($x as $d){
            $x = [$d->name,(($diff+1) - $d->hari),$d->ID];
            if($d->group_id == 'JF'){
                array_push($data['item1'],$x);
            } else {
                array_push($data['item2'],$x);
            }
        }
        $data['catatan'] = "<br>Untuk melihat data karyawan secara keseluruhan dapat diakses melalui tautan berikut ini:<br>"
        ."<br><button><b><a target='_blank' href='".url('/WHR')."'>s.jgu.ac.id/WHR</a></b></button>";
        return new WeeklyAttendanceReportMail($data);
        // Mail::to($data['email'])->queue(new WeeklyAttendanceReportMail($data));
    }

    public function tes2(){
        echo "tes";
                            //----------------WA-------------------------------
                            $wa_to = "6281233933313";
                            if($wa_to != null){
                                $WA_DATA = array();
                                $WA_DATA['wa_to'] = $wa_to;
                                $WA_DATA['wa_text'] = "SJGU TES\nbreak\nline\ntest lagi\n_ini miring_\n*ini tebal*";
                                dispatch(new JobNotificationWA($WA_DATA));
                            }
                            // ------------------end send to WA-----------------
    }

}
