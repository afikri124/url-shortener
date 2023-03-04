<?php

namespace App\Http\Controllers;

use App\Exports\RekapJamKerja;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\WhUser;
use App\Models\WhAttendance;
use App\Models\WhUserGroup;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Mail\WeeklyAttendanceReportMail;
use App\Models\DocDepartment;
use Mail;

class WorkHoursController extends Controller
{
    //
    public function wh(Request $request){
        if ($request->isMethod('post')) {
            if(Auth::user()->hasRole('HR')){
                $this->validate($request, 
                    [ 
                        'range'=> ['required', 'string', 'max:191'],
                        'select_user' => ['required'],
                    ],
                    [
                        'range.required' => 'Periode tanggal wajib ditentukan.',
                        'select_user.required' => 'Akun wajib dipilih.',
                    ]
                );
                $username = $request->select_user;
            } else {
                $this->validate($request, 
                    [ 
                        'range'=> ['required', 'string', 'max:191']
                    ],
                    [
                        'range.required' => 'Periode tanggal wajib ditentukan.'
                    ]
                );
                $username = Auth::user()->username;
            }
            return $this->whr_view($username, $request);
        } else {
            $user = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
            $lastData = WhAttendance::orderByDesc('timestamp')->first();
            return view('wh.index', compact('user', 'lastData')); 
        }
    }

    public function whr(Request $request){
        if ($request->isMethod('post')) {
            $start = Carbon::now()->subMonth(1)->startOfDay()->day(20);
            $end = Carbon::now();
                if($request->range != "" && $request->range != null && $request->range != "Invalid date - Invalid date"){
                    $x = explode(" - ",$request->range);
                    $start = date('Y-m-d 00:00',strtotime($x[0]));
                    $end = date('Y-m-d 23:59',strtotime($x[1]));
                }
            $query = ($request->grup == null ? "":" && w.group_id = '".$request->grup."'");
            // dd($query)
            $data = DB::select( DB::raw("SELECT u.name AS name2, w.name, w.username, count(jam) as hari, CONCAT(FLOOR(SUM( TIME_TO_SEC( `jam` ))/3600),':',FLOOR(SUM( TIME_TO_SEC( `jam` ))/60)%60,':',SUM( TIME_TO_SEC( `jam` ))%60) AS total, u.id AS usrid
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end'
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                RIGHT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                LEFT JOIN users u ON u.username = a.username
                WHERE w.status = 1 ".$query."
                GROUP BY w.username, w.name, u.name, u.id, w.group_id
                ORDER BY w.name
                ") );
            $periode = Carbon::parse($start)->translatedFormat("d F Y")." - ".Carbon::parse($end)->translatedFormat("d F Y");
            $group_name = WhUserGroup::where('uid',$request->grup)->first();
            $gg = "";
            if($request->grup != null){
                $gg = $group_name->title." ".$group_name->desc."_";
            }
            return Excel::download(new RekapJamKerja($data,$periode,$group_name), 'Rekap Jam Kerja_'.$gg.$periode.'.xlsx');
        }
        $user = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
        $lastData = WhAttendance::orderByDesc('timestamp')->first();
        $group          = WhUserGroup::get();
        return view('whr.index', compact('user', 'lastData','group')); 
    }

    public function user_by_id(Request $request)
    {
        $data = WhUser::where('status',1)->where("group_id",$request->id)->with('user')->select('*')->orderBy('name')->get();
        if($request->id == null || $request->id == ""){
            $data = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
        }
        return response()->json($data);
    }

    public function wh_data(Request $request)
    {
        $old_user = WhUser::where('username',Auth::user()->username)->first();
        $old = ($old_user == null ? Auth::user()->username: $old_user->username_old);
        if(Auth::user()->hasRole('HR')){
            $data = WhAttendance::
                leftjoin('wh_users', function($join){
                    $join->on('wh_users.username','=','wh_attendances.username');
                    $join->orOn('wh_users.username_old','=','wh_attendances.username');
                })
                ->with(['user' => function ($query) {
                    $query->select('id','username','name');
                }])
                ->select('wh_attendances.username','name',
                    DB::raw('DATE(`timestamp`) as tanggal'),
                    DB::raw('MIN(`timestamp`) as masuk'),
                    DB::raw('MAX(`timestamp`) as keluar'),
                    DB::raw('TIMEDIFF(MAX(`timestamp`),MIN(`timestamp`)) as total_jam'),                 
                )
                ->groupBy( DB::raw('DATE(`timestamp`)'),'wh_attendances.username','wh_users.name')
                ->orderByDesc('masuk');
        } else {
            $data = WhAttendance::
            leftjoin('wh_users', function($join){
                $join->on('wh_users.username','=','wh_attendances.username');
                $join->orOn('wh_users.username_old','=','wh_attendances.username');
            })
            ->with(['user' => function ($query) {
                $query->select('id','username','name');
            }])
            ->select('wh_attendances.username','name',
                DB::raw('DATE(`timestamp`) as tanggal'),
                DB::raw('MIN(`timestamp`) as masuk'),
                DB::raw('MAX(`timestamp`) as keluar'),
                DB::raw('TIMEDIFF(MAX(`timestamp`),MIN(`timestamp`)) as total_jam'),                 
            )
            ->where(function ($query) use ($request,$old) {
                $query->where('wh_attendances.username', Auth::user()->username)
                      ->orWhere('wh_attendances.username',$old);
            })->where('wh_users.status', 1)
            ->groupBy( DB::raw('DATE(`timestamp`)'),'wh_attendances.username','wh_users.name')
            ->orderByDesc('masuk');
        }
        return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('select_user'))) {
                        $old_user2 = WhUser::where('username',$request->get('select_user'))->first();
                        $old2 = ($old_user2 == null ? $request->get('select_user'): $old_user2->username_old);
                        $instance->where(function ($query) use ($request,$old2) {
                            $query->where('wh_attendances.username', $request->get('select_user'))
                                  ->orWhere('wh_attendances.username',$old2);
                        })->where('wh_users.status', 1);
                    }
                    if (!empty($request->get('select_range'))) {
                        if($request->get('select_range') != "" && $request->get('select_range') != null 
                            && $request->get('select_range') != "Invalid date - Invalid date"){
                            $x = explode(" - ",$request->get('select_range'));
                            $instance->whereDate('timestamp', '<=', date('Y-m-d 23:59',strtotime($x[1])));
                            $instance->whereDate('timestamp', '>=', date('Y-m-d 00:00',strtotime($x[0])));
                        }
                    } else {
                        $instance->whereDate('timestamp', '<=', Carbon::now());
                        $instance->whereDate('timestamp', '>=', Carbon::now()->subMonth(1)->startOfDay()->day(20));
                    }
                    if (!empty($request->get('search'))) {
                         $instance->where(function($w) use($request){
                            $search = $request->get('search');
                                $w->orWhere('wh_attendances.username', 'LIKE', "%$search%")
                                ->orWhere('name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addColumn('telat', function($x){
                    if(new Carbon($x->masuk) > new Carbon($x->tanggal." 08:00:59")){
                        return (new Carbon($x->masuk))->diff(new Carbon($x->tanggal." 08:00:00"))->format('%h:%I');
                    } else {
                        return null;
                    }
                  })
                ->addColumn('cepat', function($x){
                    if((new Carbon($x->tanggal))->dayOfWeek == Carbon::SATURDAY){
                        if((new Carbon($x->keluar) < new Carbon($x->tanggal." 14:00:00")) && $x->total_jam != '00:00:00'){
                            return (new Carbon($x->keluar))->diff(new Carbon($x->tanggal." 14:00:00"))->format('-%h:%I');
                        } else {
                            return null;
                        }
                    } elseif ((new Carbon($x->tanggal))->dayOfWeek == Carbon::SUNDAY){
                        return null;
                    } else {
                        if((new Carbon($x->keluar) < new Carbon($x->tanggal." 16:00:00")) && $x->total_jam != '00:00:00'){
                            return (new Carbon($x->keluar))->diff(new Carbon($x->tanggal." 16:00:00"))->format('-%h:%I');
                        } else {
                            return null;
                        }
                    }
                  })
                ->addColumn('lembur', function($x){
                    if(new Carbon($x->total_jam) > new Carbon("10:00:00")){
                        return (new Carbon($x->total_jam))->diff(new Carbon("10:00:00"))->format('%h:%I');
                    } else {
                        return null;
                    }
                  })
                ->addColumn('userid', function($x){
                    if($x->user != null){
                        return Crypt::encrypt($x->user->id);
                    } else {
                        return null;
                    }
                  })
                ->rawColumns(['telat','cepat','lembur','userid'])
                ->make(true);
    }

    public function whr_data(Request $request)
    {
        $start = Carbon::now()->subMonth(1)->startOfDay()->day(20);
        $end = Carbon::now();
        if (!empty($request->get('select_range'))) {
            if($request->get('select_range') != "" && $request->get('select_range') != null 
                && $request->get('select_range') != "Invalid date - Invalid date"){
                $x = explode(" - ",$request->get('select_range'));
                $start = date('Y-m-d 00:00',strtotime($x[0]));
                $end = date('Y-m-d 23:59',strtotime($x[1]));
            }
        }

        if (!empty($request->get('select_user'))) {
            $user_id = $request->get('select_user');
            $old_user = WhUser::where('username',$user_id)->first();
            $old = ($old_user == null ? $user_id: $old_user->username_old);
            $query = (empty($request->get('select_group')) ? "":" && w.group_id = '".$request->get('select_group')."'");
            $data = DB::select( DB::raw("SELECT u.name AS name2, w.name, w.username, count(jam) as hari, CONCAT(FLOOR(SUM( TIME_TO_SEC( `jam` ))/3600),':',FLOOR(SUM( TIME_TO_SEC( `jam` ))/60)%60,':',SUM( TIME_TO_SEC( `jam` ))%60) AS total, u.id AS usrid, w.group_id
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end' 
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                RIGHT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                LEFT JOIN users u ON u.username = a.username 
                WHERE w.status = 1 ".$query." && (w.`username` = '".$user_id."' or w.`username` = '".$old."')
                GROUP BY w.username, w.name, u.name, u.id, w.group_id
                ORDER BY w.name
                ") );
        } else {
            $query = (empty($request->get('select_group')) ? "":" && w.group_id = '".$request->get('select_group')."'");
            $data = DB::select( DB::raw("SELECT u.name AS name2, w.name, w.username, count(jam) as hari, CONCAT(FLOOR(SUM( TIME_TO_SEC( `jam` ))/3600),':',FLOOR(SUM( TIME_TO_SEC( `jam` ))/60)%60,':',SUM( TIME_TO_SEC( `jam` ))%60) AS total, u.id AS usrid, w.group_id
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end'
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                RIGHT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                LEFT JOIN users u ON u.username = a.username
                WHERE w.status = 1 ".$query."
                GROUP BY w.username, w.name, u.name, u.id, w.group_id
                ORDER BY w.name
                ") );
        }
        return Datatables::of($data)
        ->addColumn('userid', function($x){
            if($x->usrid != null){
                return Crypt::encrypt($x->usrid);
            } else {
                return null;
            }
          })
        ->rawColumns(['userid'])
        ->make(true);
    }

    public function wh_total_h(Request $request)
    {
        $start = Carbon::now()->subMonth(1)->startOfDay()->day(20);
        $end = Carbon::now();
        if (!empty($request->get('select_range'))) {
            if($request->get('select_range') != "" && $request->get('select_range') != null 
                && $request->get('select_range') != "Invalid date - Invalid date"){
                $x = explode(" - ",$request->get('select_range'));
                $start = date('Y-m-d 00:00',strtotime($x[0]));
                $end = date('Y-m-d 23:59',strtotime($x[1]));
            }
        }

        if (!empty($request->get('select_user')) || !Auth::user()->hasRole('HR') ) {
            $user_id = (Auth::user()->hasRole('HR') ? $request->get('select_user') : Auth::user()->username);
            $old_user = WhUser::where('username',$user_id)->first();
            $old = ($old_user == null ? $user_id: $old_user->username_old);
            $data = DB::select( DB::raw("SELECT CONCAT(FLOOR(SUM( TIME_TO_SEC( `jam` ))/3600),':',FLOOR(SUM( TIME_TO_SEC( `jam` ))/60)%60,':',SUM( TIME_TO_SEC( `jam` ))%60) AS total
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end' && (`username` = '$user_id' or `username` = '$old')
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                LEFT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                WHERE w.status = 1
                ") );
        } else {
            $data = DB::select( DB::raw("SELECT CONCAT(FLOOR(SUM( TIME_TO_SEC( `jam` ))/3600),':',FLOOR(SUM( TIME_TO_SEC( `jam` ))/60)%60,':',SUM( TIME_TO_SEC( `jam` ))%60) AS total
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end'
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                LEFT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                WHERE w.status = 1
                ") );
        }
        $total = 0;
        foreach($data as $d){
            $total = $d->total;
        }
        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    }

    public function whr_view($username, Request $request){
        $username = str_replace("�","",$username);
        $start = Carbon::now()->subMonth(1)->startOfDay()->day(20)->translatedFormat("Y-m-d H:i");
        $end = Carbon::now()->translatedFormat("Y-m-d H:i");
        if(isset($request->range)){
            $x = explode(" - ",$request->range);
            $end = Carbon::parse($x[1]." 23:59")->translatedFormat("Y-m-d H:i");
            $start = Carbon::parse($x[0]." 00:00")->translatedFormat("Y-m-d H:i");
        }

        $user = WhUser::where('username',$username)->orWhere('username_old',$username)->with('user')->first();

        if($user != null){
            try {
                $endX = Carbon::parse($end)->subDay(1)->translatedFormat("Y-m-d H:i");
                $data = DB::select("WITH recursive all_dates(dt) AS (
                        SELECT '$start' dt
                        UNION ALL 
                        SELECT dt + INTERVAL 1 DAY FROM all_dates WHERE dt <= '$endX'
                    )
                    SELECT DATE(d.dt) AS tanggal, username, masuk, keluar, total_jam
                    FROM all_dates d
                    LEFT JOIN (
                        SELECT DATE(`timestamp`) AS tanggal, username, MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS keluar, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS total_jam 
                        FROM wh_attendances
                        WHERE (`username` = '$user->username' OR username = '$user->username_old')
                        GROUP BY tanggal, username
                        ORDER BY tanggal
                    ) a
                    ON d.dt = a.`tanggal`
                    GROUP BY d.dt, username, masuk, keluar, total_jam
                    ORDER BY d.dt
                ") ;
               
            } catch (\Exception $e) {
                $startX = Carbon::parse($start)->translatedFormat("Y-m-d");
                $data = DB::select("SELECT v.tanggal, a.username, a.masuk, a.keluar, a.total_jam FROM 
                    (SELECT ADDDATE('$startX',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) tanggal FROM
                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) 
                    v
                    LEFT JOIN (
                        SELECT DATE(`timestamp`) AS tanggal, username, MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS keluar, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS total_jam 
                        FROM wh_attendances
                        WHERE (`username` = '$user->username' OR username = '$user->username_old')
                        GROUP BY tanggal, username
                        ORDER BY tanggal
                    ) a
                    ON v.tanggal = a.`tanggal`
                    WHERE v.tanggal BETWEEN '$start' AND '$end'
                    GROUP BY v.tanggal, username, masuk, keluar, total_jam
                    ORDER BY v.tanggal;
                ") ;
            }
            // dd($data);
            $periode = Carbon::parse($start)->translatedFormat("d F Y")." - ".Carbon::parse($end)->translatedFormat("d F Y");
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return view('whr.view', compact('user', 'data', 'periode','link')); 
        } else {
            abort(403, "User tidak ditemukan!");
        }
    }

     //sync data from machine
    public function whr_sync()
    {
        $data = null;
        $i_total = 0;
        $info = (Auth::check() ? Auth::user()->username." : ".Auth::user()->name : "CronJob");

        //mesin lantai 1
        try {
            $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
            $idmesin = 1; //mesin lt 1
            $i = 0;
            if ($zk->connect()){
                $data = array_reverse(app('App\Http\Controllers\ZKTecoController')->getAttendance($zk), true);
                $zk->disconnect(); 
                $breakId = null;
                $user = WhAttendance::where('idmesin',$idmesin)->orderByDesc('timestamp')->first();
                if($user){
                    $breakId = $user->uid;
                }
                if($data != null){
                        foreach ($data as $att) {
                            if($att['uid'] == $breakId){
                                break;
                            } else {
                                $check = WhAttendance::where('uid',$att['uid'])->where('idmesin',$idmesin)->first(); //mesin 1
                                if(!$check){
                                    $new_att = false;
                                    $new_att=WhAttendance::insert([
                                            'uid' => $att['uid'],
                                            'username' => $att['userid'],
                                            'state' => $att['state'],
                                            'timestamp' => $att['timestamp'],
                                            'type' => $att['type'],
                                            'idmesin' => $idmesin //lantai 1
                                    ]);
                                    if($new_att){
                                        $i++;
                                    }  
                                }
                            }
                        }
                        if(Auth::check()){
                            Log::info($info." sync data att from machine ".$idmesin.", breakid : ".$breakId.", total new : ".$i);
                        }
                        $i_total += $i;
                } else {
                    Log::info($info." failed sync data att from machine ".$idmesin.", breakid : ".$breakId.", total new: ".$i);
                    return response()->json([
                        'success' => false,
                        'total' => $i,
                    ]);
                }
            } else {
                Log::info($info." machine ".$idmesin." Not Connect!");
            } 
        } catch (DecryptException $e) {
            Log::info($info." failed sync from machine 1");
            return response()->json([
                'success' => false,
                'total' => $i,
            ]);
        }
        
        //mesin lantai 2
        if(env('IP_ATTENDANCE_MACHINE_2')){
            try {
                $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE_2'));
                $idmesin = 2; //mesin lantai 2
                $i = 0;
                if ($zk->connect()){
                    $data = array_reverse(app('App\Http\Controllers\ZKTecoController')->getAttendance($zk), true);
                    $zk->disconnect();  
                    $breakId = null;
                    $user = WhAttendance::where('idmesin',$idmesin)->orderByDesc('timestamp')->first();
                    if($user){
                        $breakId = $user->uid;
                    }
                    if($data != null){
                            foreach ($data as $att) {
                                if($att['uid'] == $breakId){
                                    break;
                                } else {
                                    $check = WhAttendance::where('uid',$att['uid'])->where('idmesin',$idmesin)->first(); //mesin
                                    if(!$check){
                                        $new_att = false;
                                        $new_att=WhAttendance::insert([
                                                'uid' => $att['uid'],
                                                'username' => $att['userid'],
                                                'state' => $att['state'],
                                                'timestamp' => $att['timestamp'],
                                                'type' => $att['type'],
                                                'idmesin' => $idmesin //lantai
                                        ]);
                                        if($new_att){
                                            $i++;
                                        }  
                                    }
                                }
                            }
                            if(Auth::check()){
                                Log::info($info." sync data att from machine ".$idmesin.", breakid : ".$breakId.", total new : ".$i);
                            }
                        $i_total += $i;
                    } else {
                        Log::info($info." failed sync data att from machine ".$idmesin.", breakid : ".$breakId);
                        return response()->json([
                            'success' => false,
                            'total' => $i,
                        ]);
                    }
                } else {
                    Log::info($info." machine ".$idmesin." Not Connect!");
                } 
            } catch (DecryptException $e) {
                Log::info($info." failed sync from machine 2");
                return response()->json([
                    'success' => false,
                    'total' => $i,
                ]);
            }
        } 
        
        

        //mesin lantai 5
        if(env('IP_ATTENDANCE_MACHINE_5')){
            try {
                $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE_5'));
                $idmesin = 5; //mesin lantai 5
                $i = 0;
                if ($zk->connect()){
                    $data = array_reverse(app('App\Http\Controllers\ZKTecoController')->getAttendance($zk), true);
                    $zk->disconnect();   
                    $breakId = null;
                    $user = WhAttendance::where('idmesin',$idmesin)->orderByDesc('timestamp')->first();
                    if($user){
                        $breakId = $user->uid;
                    }
                    if($data != null){
                            foreach ($data as $att) {
                                if($att['uid'] == $breakId){
                                    break;
                                } else {
                                    $check = WhAttendance::where('uid',$att['uid'])->where('idmesin',$idmesin)->first(); //mesin
                                    if(!$check){
                                        $new_att = false;
                                        $new_att=WhAttendance::insert([
                                                'uid' => $att['uid'],
                                                'username' => $att['userid'],
                                                'state' => $att['state'],
                                                'timestamp' => $att['timestamp'],
                                                'type' => $att['type'],
                                                'idmesin' => $idmesin //lantai
                                        ]);
                                        if($new_att){
                                            $i++;
                                        }  
                                    }
                                }
                            }
                            if(Auth::check()){
                                Log::info($info." sync data att from machine ".$idmesin.", breakid : ".$breakId.", total new : ".$i);
                            }
                        $i_total += $i;
                    } else {
                        Log::info($info." failed sync data att from machine ".$idmesin.", breakid : ".$breakId.", total new: ".$i);
                        return response()->json([
                            'success' => false,
                            'total' => $i,
                        ]);
                    }
                } else {
                    Log::info($info." machine ".$idmesin." Not Connect!");
                }  
            } catch (DecryptException $e) {
                Log::info($info." failed sync from machine 5");
                return response()->json([
                    'success' => false,
                    'total' => $i,
                ]);
            }
        }
        return response()->json([
            'success' => true,
            'total' => $i_total,
        ]);
    }

    public function weeklyAttendanceReport(){
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
        $date_end =  Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)->endOfDay();
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
          ORDER BY u.group_id DESC, hari") );
        foreach($x as $d){
            $x = [$d->name,(5-$d->hari),$d->ID];
            if($d->group_id == 'JF'){
                array_push($data['item1'],$x);
            } else {
                array_push($data['item2'],$x);
            }
        }
        $data['catatan'] = "<br>Untuk melihat data karyawan secara keseluruhan dapat diakses melalui tautan berikut ini:<br>"
        ."<br><button><b><a target='_blank' href='".url('/WHR')."'>s.jgu.ac.id/WHR</a></b></button>";
        Mail::to($data['email'])->queue(new WeeklyAttendanceReportMail($data));
        // return new WeeklyAttendanceReportMail($data);
    }

    public function zk(){
            $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
            if ($zk->connect()){

                // $role = 0; //14= super admin, 0=User :: according to ZKtecho Machine
                // $users = $zk->getUser();
                // $total = end($users);
                // $lastId=$total[3]+1;

                // 1 = uid
                // 2 = userid
                // 3 = nama (max 24 char)
                // 4 = password
                // 5 = role (14 : admin, 0 : user)
                // $x = app('App\Http\Controllers\ZKTecoController')->setUser($zk, 241, 'ERROR19010001', 'ERROR S.Kom', '', 0);
                    // $uid = 96;
                    // $cardno = 0;
                    // $role = 14;
                    // $password = "";
                    // $name = "Ali Fikri";
                    // $userid = "S092021100001";

                // SELECT userid,MIN(att_date) AS masuk, MAX(att_date) AS pulang, TIMEDIFF(MAX(att_date), MIN(att_date))AS jam FROM attendance
                // WHERE userid = 2
                // GROUP BY DATE(att_date),userid
                // ORDER BY masuk;


                // $zk->removeUser(219); 
                // return "Add user success";

                $data = app('App\Http\Controllers\ZKTecoController')->getUser($zk);
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);

                //             $data = json_decode(json_encode(app('App\Http\Controllers\ZKTecoController')->getUser($zk)));

                //             dd($data);
                $zk->disconnect();   
            }
    }


}
