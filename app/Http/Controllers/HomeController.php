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
use App\Mail\MailBirthday;
use App\Models\DocDepartment;
use App\Models\DocPIC;
use App\Jobs\JobNotificationWA;
use Illuminate\Support\Facades\Log;
use Mail;
use Illuminate\Support\Facades\Http;

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

    public function sso_siap(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, 
                [ 
                    'email'=> ['required', 'email'],
                    'password' => ['required'],
                ]
            );
            try {
                if($request->urlintended != null){
                    session(['url.intended' => $request->urlintended]); //link redirect
                }
                $url = env('SevimaAPI_url').'/siakadcloud/v1/user/login';
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-App-Key' => env('SevimaAPI_key'),
                    'X-Secret-Key' => env('SevimaAPI_secret'),
                ])->post($url, [
                    'email' => $request->email,
                    'password' => $request->password,
                ]);

                if ($response->successful()) {
                    $data = json_decode(json_encode($response->json())); // atau redirect/simpan token
                    if(is_object($data) && isset($data->attributes) && $data->attributes->status_aktif){ //mengecek akun apakah masih aktif
                        // dd($data->attributes);//test
                        $username = null;
                        $jobName = null;

                        foreach ($data->attributes->role as $role) {
                            if (is_object($role) && isset($role->nama_role)) {
                                $jobName = $role->nama_role;
                            }

                            if (is_object($role) && isset($role->nip)) {
                                $username = $role->nip;
                                break; // Ambil hanya nip yang pertama
                            } else if (is_object($role) && isset($role->nim)) {
                                $username = $role->nim;
                                break; // Ambil hanya nim yang pertama
                            }
                        }
                        $roles = collect($data->attributes->role); // Ambil bagian role saja
                        $hasMhs = $roles->contains(function ($role) {
                            return is_object($role) && $role->id_role === 'mhs';
                        });
                        $hasPeg = $roles->contains(function ($role) {
                            return is_object($role) && $role->id_role === 'peg';
                        });
                        $hasDosen = $roles->contains(function ($role) {
                            return is_object($role) && $role->id_role === 'dosen';
                        });

                        if ($username != null){ // nim/nip tidak null
                            $user = User::where('username', $username)->first(); //cari user by username
                            if ($user != null) { //login
                                if($data->attributes->email != $user->email){
                                    $email = explode("@",$data->attributes->email);
                                    if($email[1] == "jgu.ac.id" || $email[1] == "student.jgu.ac.id"){
                                        $emailcheck = User::where('email',$data->attributes->email)->first();
                                        if($emailcheck != null){ //update username base on email
                                            User::where('id',$user->id)->update([
                                                'username' => $username."x", //jika bentrok username diganti
                                            ]);
                                            User::where('email',$data->attributes->emai)->update([
                                                'username' => $username,
                                            ]);
                                        } else { //update email
                                            User::where('id',$user->id)->update([
                                                'email' => $data->attributes->email,
                                            ]);
                                        }
                                    }
                                }
                                Auth::loginUsingId($user->id);
                            } else { //register jika username blm terdaftar
                                $user = User::where('email',$data->attributes->email)->first(); //cari user by email
                                if($user == null){ //jika user tdk ada
                                    $new_user = User::insert([
                                            'name' => $data->attributes->nama,
                                            'email' => $data->attributes->email,
                                            'username' => $username,
                                            'password'=> Hash::make($username),
                                            'job'=> $jobName,
                                            'email_verified_at' => Carbon::now(),
                                            'created_at' => Carbon::now()
                                    ]);
                                    
                                    if($new_user){
                                        $user = User::where('username', $username)->first();
                                        if($hasDosen || $hasPeg){
                                            $user->roles()->attach(Role::where('id', 'ST')->first());
                                        } else {
                                            if ($hasMhs) {
                                                $user->roles()->attach(Role::where('id', 'SD')->first());
                                            } else {
                                                $user->roles()->attach(Role::where('id', 'GS')->first());
                                            }
                                        }
                                    }  
                                } else { //jika user ada
                                    $old_user = $user->update([
                                        'name' => $data->attributes->nama,
                                        'username' => $username,
                                        'password'=> Hash::make($username),
                                        'updated_at' => Carbon::now()
                                    ]);
                                    
                                    if($old_user){
                                        $user = User::where('username', $username)->first();
                                        if(($hasDosen || $hasPeg) && !$user->hasRole('ST')){
                                            $user->roles()->attach(Role::where('id', 'ST')->first());
                                        } 
                                        if($hasMhs && !$user->hasRole('SD')){
                                            $user->roles()->attach(Role::where('id', 'SD')->first());
                                        }
                                    }  
                                }
                                Auth::loginUsingId($user->id);
                            }
                            if(session()->has('url.intended')){
                                $link = session('url.intended');
                                session(['url.intended' => null]);
                                Session::forget('url.intended');
                                return redirect($link);
                            } else {
                                return redirect()->route('home');
                            }
                        } else {
                            $msg = "Akun anda tidak ditemukan!";
                            return redirect()->route('sso_siap')->withErrors(['msg' => $msg]);
                        }
                    } else {
                        $msg = "Akun anda sudah tidak aktif !";
                        return redirect()->route('sso_siap')->withErrors(['msg' => $msg]);
                    }
                } else {
                    $responseBody = $response->json();
                    if($responseBody['errors']['code'] == 500){
                        $msg = "<b>".$responseBody['errors']['detail']." (".$responseBody['errors']['code']."):</b><br>Koneksi API ke Sevima gagal, silahkan coba lagi..";
                    } else {
                        $msg = "<b>Login gagal (".$responseBody['errors']['code']."):</b><br>".$responseBody['errors']['detail'];
                    }
                    return redirect()->route('sso_siap')->withErrors(['msg' => $msg]);
                }
            } catch (\Exception $e) {
                Log::warning($e);
                return redirect()->route('sso_siap')->withErrors(['msg' => $e->getMessage()]);
            }
        } else {
            return view('auth.login-siap');
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
        (
				SELECT username, COUNT(hari) as days,
                    CONCAT(FLOOR(SUM( TIME_TO_SEC( `total_jam` ))/3600),':',FLOOR(SUM( TIME_TO_SEC( `total_jam` ))/60)%60,':',SUM( TIME_TO_SEC( `total_jam` ))%60) AS total 
                    from (
                        SELECT tanggal, username, COUNT(total_jam) as hari, max(total_jam) as total_jam
                        FROM (
                            SELECT DATE(`timestamp`) AS tanggal, username, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`)) AS total_jam 
                            FROM wh_attendances
                            GROUP BY tanggal, username
                            UNION ALL
                            SELECT
                                ph.date as tanggal,
                                IFNULL(username,username_old) as username,
                                TIME('08:00:00') as total_jam
                            FROM
                                wh_users u
                                JOIN wh_public_holidays ph ON u.username != ph.id
                            GROUP BY IFNULL(username,username_old), id, ph.date, total_jam
                            ORDER BY tanggal, total_jam desc
                        ) x
                        WHERE tanggal >= '".$date_start."' && tanggal <= '".$date_end."'
                        GROUP BY tanggal, username
                    ) x2
                    GROUP BY username
				) AS tt
        RIGHT JOIN wh_users u ON tt.username = u.username
        WHERE u.`status` = 1 && IFNULL(tt.days,0) <= ".$diff." && (u.group_id = 'JF' OR u.group_id = 'JE')
        ORDER BY u.group_id DESC, hari, u.name
        ") );
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

    public function wa(){
        echo "tes wa $ email ke fikri";
                            //----------------WA-------------------------------
                            $wa_to = "6281233933313";
                            if($wa_to != null){
                                $WA_DATA = array();
                                $WA_DATA['wa_to'] = $wa_to;
                                $WA_DATA['wa_text'] = "SJGU TES WA\nbreak\nline\ntest lagi\n_ini miring_\n*ini tebal*";
                                dispatch(new JobNotificationWA($WA_DATA));
                            }
                            // ------------------end send to WA-----------------
                            Mail::raw('Hello World!', function($msg) {$msg->to('fikri@jgu.ac.id')->subject('Test Email'); });
    }

    public function ultah(){
        // ambil tanggal & bulan hari ini
        $today = Carbon::now();

        // filter user yang tanggal lahir (bulan & hari) sama dengan hari ini
        $users = User::with('roles')
                    ->whereMonth('birth_date', $today->month)
                    ->whereDay('birth_date', $today->day)
                    ->where('status', true)
                    ->get();
        // dd($users);
        if($users){
            foreach ($users as $u){
                echo "kirim wa dan email ulang tahun ke ".$u->name."<br>";
                $umur = Carbon::parse($u->birth_date)->age;
                $data['email'] = $u->email;
                $data['name'] = $u->name;
                $data['subject'] = "🎉Birthday Greetings from JGU!🎉";
                $data['is_mhs'] = $u->roles->contains('id', 'SD') ?? false;
                $data['is_staf'] = $u->roles->contains('id', 'ST') ?? false;
                if($data['is_staf']){
                    // Mail::to($data['email'])->send(new MailBirthday($data));
                    Mail::to($data['email'])->queue(new MailBirthday($data));
                    Log::info("berhasil mengirim ucapan HBD ke ".$u->name);
                    //----------------WA-------------------------------
                    if($u->phone){
                        $WA_DATA = array();
                        $WA_DATA['wa_to'] = $u->phone;
                        $WA_DATA['wa_text'] = "\n🎂 HBD ".$u->name." 🎉\n
Keluarga besar _Jakarta Global University_ mengucapkan selamat ulang tahun ke-".$umur.". 
Semoga panjang umur, sehat selalu, dan sukses dalam setiap langkah. 
Terima kasih atas dedikasi dan kontribusi yang telah diberikan. 🌟\n\nSalam hangat,\n*JGU*";
                        dispatch(new JobNotificationWA($WA_DATA));
                    }
                    // ------------------end send to WA-----------------
                } else if ($data['is_mhs']){
                    // Mail::to($data['email'])->send(new MailBirthday($data));
                    Mail::to($data['email'])->queue(new MailBirthday($data));
                    Log::info("berhasil mengirim ucapan HBD ke ".$u->name);
                    //----------------WA-------------------------------
                    if($u->phone){
                        $WA_DATA = array();
                        $WA_DATA['wa_to'] = $u->phone;
                        $WA_DATA['wa_text'] = "\n🎂 HBD ".$u->name."🎉 \n
Seluruh civitas akademika _Jakarta Global University_ mengucapkan selamat ulang tahun ke-".$umur.". 
Semoga panjang umur, sehat selalu, dan semakin berprestasi dalam perjalanan studi serta kehidupanmu. 
Teruslah bersemangat dalam meraih mimpi dan cita-citamu. 🌟\n\nSalam hangat,\n*JGU*";
                        dispatch(new JobNotificationWA($WA_DATA));
                    }
                    // ------------------end send to WA-----------------
                }
            }
        }
    }

}
