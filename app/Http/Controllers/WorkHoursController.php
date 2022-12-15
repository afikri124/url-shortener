<?php

namespace App\Http\Controllers;

use App\Exports\RekapJamKerja;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\WhUser;
use App\Models\WhAttendance;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    //
    public function wh(){
        $user = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
        $lastData = WhAttendance::orderByDesc('timestamp')->first();
        return view('wh.index', compact('user', 'lastData')); 
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
  
            $data = DB::select( DB::raw("SELECT u.name AS name2, w.name, a.username, count(jam) as hari, SEC_TO_TIME(SUM(TIME_TO_SEC(jam))) AS total, u.id AS usrid
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end'
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                LEFT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                LEFT JOIN users u ON u.username = a.username
                WHERE w.status = 1
                GROUP BY a.username, w.name, u.name, u.id
                ORDER BY w.name
                ") );
            $periode = Carbon::parse($start)->translatedFormat("d F Y")." - ".Carbon::parse($end)->translatedFormat("d F Y");
            return Excel::download(new RekapJamKerja($data,$periode), 'Rekap Jam Kerja_'.$periode.'.xlsx');
        }

        $user = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
        $lastData = WhAttendance::orderByDesc('timestamp')->first();
        return view('whr.index', compact('user', 'lastData')); 
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
                    if(new Carbon($x->masuk) > new Carbon($x->tanggal." 08:00:00")){
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
            $data = DB::select( DB::raw("SELECT u.name AS name2, w.name, a.username, count(jam) as hari,  SEC_TO_TIME(SUM(TIME_TO_SEC(jam))) AS total, u.id AS usrid
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end' && (`username` = '$user_id' or `username` = '$old')
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                LEFT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                LEFT JOIN users u ON u.username = a.username 
                WHERE w.status = 1
                GROUP BY a.username, w.name, u.name, u.id
                ORDER BY w.name
                ") );
        } else {
            $data = DB::select( DB::raw("SELECT u.name AS name2, w.name, a.username, count(jam) as hari, SEC_TO_TIME(SUM(TIME_TO_SEC(jam))) AS total, u.id AS usrid
                FROM (
                    SELECT username,MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS pulang, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS jam 
                    FROM wh_attendances
                    WHERE `timestamp` >= '$start' && `timestamp` <= '$end'
                    GROUP BY DATE(`timestamp`),username
                    ORDER BY pulang DESC
                ) a 
                LEFT JOIN wh_users w ON w.username_old = a.username or w.username = a.username
                LEFT JOIN users u ON u.username = a.username
                WHERE w.status = 1
                GROUP BY a.username, w.name, u.name, u.id
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
            $data = DB::select( DB::raw("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(jam))) AS total
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
            $data = DB::select( DB::raw("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(jam))) AS total
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
                // $endX = Carbon::parse($end)->subDay(1)->translatedFormat("Y-m-d H:i");
                // $data = DB::select("WITH recursive all_dates(dt) AS (
                //         SELECT '$start' dt
                //         UNION ALL 
                //         SELECT dt + INTERVAL 1 DAY FROM all_dates WHERE dt <= '$endX'
                //     )
                //     SELECT DATE(d.dt) AS tanggal, username, masuk, keluar, total_jam
                //     FROM all_dates d
                //     LEFT JOIN (
                //         SELECT DATE(`timestamp`) AS tanggal, username, MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS keluar, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS total_jam 
                //         FROM wh_attendances
                //         WHERE (`username` = '$user->username' OR username = '$user->username_old')
                //         GROUP BY tanggal, username
                //         ORDER BY tanggal
                //     ) a
                //     ON d.dt = a.`tanggal`
                //     GROUP BY d.dt, username, masuk, keluar, total_jam
                //     ORDER BY d.dt
                // ") ;
                $data = DB::select("SELECT v.tanggal, a.username, a.masuk, a.keluar, a.total_jam FROM 
                    (SELECT ADDDATE('$start',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) tanggal FROM
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
            } catch (\Exception $e) {
                $data = WhAttendance::
                    leftjoin('wh_users', function($join){
                        $join->on('wh_users.username','=','wh_attendances.username');
                        $join->orOn('wh_users.username_old','=','wh_attendances.username');
                    })
                    ->select('wh_attendances.username',
                        DB::raw('DATE(`timestamp`) as tanggal'),
                        DB::raw('MIN(`timestamp`) as masuk'),
                        DB::raw('MAX(`timestamp`) as keluar'),
                        DB::raw('TIMEDIFF(MAX(`timestamp`),MIN(`timestamp`)) as total_jam'),                 
                    )
                    ->where(function ($query) use ($user) {
                        $query->where('wh_attendances.username', $user->username)
                            ->orWhere('wh_attendances.username',$user->username_old);
                    })
                    ->where('wh_users.status', 1)
                    ->whereDate('timestamp', '<=', $end)
                    ->whereDate('timestamp', '>=', $start)
                    ->groupBy( DB::raw('DATE(`timestamp`)'),'wh_attendances.username','wh_users.name')
                    ->orderBy('tanggal')->get();
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
        $i = 0;
        $info = (Auth::check() ? Auth::user()->username." : ".Auth::user()->name : "CronJob");
        try {
            $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
            if ($zk->connect()){
                $data = array_reverse(app('App\Http\Controllers\ZKTecoController')->getAttendance($zk), true);
                $zk->disconnect();   
            }      
            $breakId = null;
            $user = WhAttendance::orderByDesc('timestamp')->first();
            if($user){
                $breakId = $user->uid;
            }
            if($data != null){
                    foreach ($data as $att) {
                        if($att['uid'] == $breakId){
                            break;
                        } else {
                            $check = WhAttendance::where('uid',$att['uid'])->first();
                            if(!$check){
                                $new_att = false;
                                $new_att=WhAttendance::insert([
                                        'uid' => $att['uid'],
                                        'username' => $att['userid'],
                                        'state' => $att['state'],
                                        'timestamp' => $att['timestamp'],
                                        'type' => $att['type'],
                                ]);
                                if($new_att){
                                    $i++;
                                }  
                            }
                        }
                    }
                    if(Auth::check()){
                        Log::info($info." sync data att from machine, breakid : ".$breakId.", total new : ".$i);
                    }
                    return response()->json([
                        'success' => true,
                        'total' => $i,
                    ]);

            } else {
                Log::info($info." failed sync data att from machine, breakid : ".$breakId.", total new: ".$i);
                return response()->json([
                    'success' => false,
                    'total' => $i,
                ]);
            }
        } catch (DecryptException $e) {
            Log::info($info." failed sync data att to database, breakid : ".$breakId.",  total new: ".$i);
            return response()->json([
                'success' => false,
                'total' => $i,
            ]);
        }
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
                // $x = app('App\Http\Controllers\ZKTecoController')->setUser($zk, 217, 'S092021100001', 'ALI FIKRI S.Kom', '', 14);
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
