<?php

namespace App\Http\Controllers;

use App\Exports\RekapJamKerja;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\WhUser;
use App\Models\WhAttendance;
use App\Models\WhUserGroup;
use App\Models\WhUserUnit;
use App\Models\WhPublicHoliday;
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
use App\Jobs\JobNotificationWA;

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
            $query = ($request->grup == null ? "":" && u.group_id = '".$request->grup."'");
            $query2 = ($request->unit == null ? "":" && u.unit_id = '".$request->unit."'");
            // dd($query)
            $data = DB::select( DB::raw("SELECT users.name AS name2, u.name, IFNULL(u.username,u.username_old) as username, 
                uu.title as unit, a.hari, a.total, users.id AS usrid, u.group_id
                from wh_users u
                LEFT JOIN wh_user_groups g on g.uid = u.group_id
                LEFT JOIN wh_user_units uu on uu.uid = u.unit_id
                LEFT JOIN users ON users.username = u.username
                LEFT JOIN (
                    SELECT username, COUNT(hari) as hari,
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
                        WHERE tanggal >= '$start' && tanggal <= '$end'
                        GROUP BY tanggal, username
                    ) x2
                    GROUP BY username
                ) a on a.username = IFNULL(u.username,u.username_old)
                WHERE u.status = 1 ".$query.$query2."
                GROUP BY IFNULL(u.username,u.username_old), u.name, users.name, users.id, u.group_id, u.unit_id, uu.title, a.hari, a.total
                ORDER BY hari desc, total desc
                ") );
            $periode = Carbon::parse($start)->translatedFormat("d F Y")." - ".Carbon::parse($end)->translatedFormat("d F Y");
            $group_name = WhUserGroup::where('uid',$request->grup)->first();
            $gg = "";
            if($request->grup != null){
                $gg = " ".$group_name->title." ".$group_name->desc;
            }
            $unit_name = WhUserUnit::where('uid',$request->unit)->first();
            $uu = "";
            if($request->unit != null){
                $uu = "_".str_replace('/', '-', $unit_name->title);
            }
            return Excel::download(new RekapJamKerja($data,$periode,$group_name,$unit_name), 'Rekap Jam Kerja'.$gg.$uu."_".$periode.'.xlsx');
        }
        $user = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
        $lastData = WhAttendance::orderByDesc('timestamp')->first();
        $group          = WhUserGroup::get();
        $unit          = WhUserUnit::get();
        return view('whr.index', compact('user', 'lastData','group', 'unit')); 
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
                leftjoin('wh_users', function($x){
                    $x->on('wh_users.username','=','wh_attendances.username');
                    $x->orOn('wh_users.username_old','=','wh_attendances.username');
                })
                ->leftjoin('wh_user_units', function($x){
                    $x->on('wh_users.unit_id','=','wh_user_units.uid');
                })
                ->with(['user' => function ($query) {
                    $query->select('id','username','name');
                }])
                ->select('wh_attendances.username','name',
                    DB::raw('DATE(`timestamp`) as tanggal'),
                    DB::raw('MIN(`timestamp`) as masuk'),
                    DB::raw('MAX(`timestamp`) as keluar'),
                    DB::raw('TIMEDIFF(MAX(`timestamp`),MIN(`timestamp`)) as total_jam'),
                    'wh_user_units.time_in', 'wh_user_units.time_out', 'wh_user_units.time_total'       
                )
                // ->where('wh_attendances.username', 'S092021100001')
                ->groupBy( DB::raw('DATE(`timestamp`)'),'wh_attendances.username','wh_users.name','wh_user_units.time_in', 'wh_user_units.time_out', 'wh_user_units.time_total' )
                ->orderByDesc('masuk');
            // echo json_encode($data);
        } else {
            $data = WhAttendance::
            leftjoin('wh_users', function($join){
                $join->on('wh_users.username','=','wh_attendances.username');
                $join->orOn('wh_users.username_old','=','wh_attendances.username');
            })
            ->leftjoin('wh_user_units', function($x){
                $x->on('wh_users.unit_id','=','wh_user_units.uid');
            })
            ->with(['user' => function ($query) {
                $query->select('id','username','name');
            }])
            ->where(function ($query) use ($request,$old) {
                $query->where('wh_attendances.username', Auth::user()->username)
                      ->orWhere('wh_attendances.username',$old);
            })->where('wh_users.status', 1)
            ->select('wh_attendances.username','name',
                DB::raw('DATE(`timestamp`) as tanggal'),
                DB::raw('MIN(`timestamp`) as masuk'),
                DB::raw('MAX(`timestamp`) as keluar'),
                DB::raw('TIMEDIFF(MAX(`timestamp`),MIN(`timestamp`)) as total_jam'),
                'wh_user_units.time_in', 'wh_user_units.time_out', 'wh_user_units.time_total'                 
            )
            ->groupBy( DB::raw('DATE(`timestamp`)'),'wh_attendances.username','wh_users.name','wh_user_units.time_in', 'wh_user_units.time_out', 'wh_user_units.time_total')
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
                    if(new Carbon($x->masuk) > new Carbon($x->tanggal." ".$x->time_in)){
                        return (new Carbon($x->masuk))->diff(new Carbon($x->tanggal." ".$x->time_in))->format('%h:%I');
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
                        if((new Carbon($x->keluar) < new Carbon($x->tanggal." ".$x->time_out)) && $x->total_jam != '00:00:00'){
                            return (new Carbon($x->keluar))->diff(new Carbon($x->tanggal." ".$x->time_out))->format('-%h:%I');
                        } else {
                            return null;
                        }
                    }
                  })
                ->addColumn('lembur', function($x){
                    $lemburSetelah = ((Carbon::parse($x->time_total) < new Carbon("10:00:00")) ? new Carbon("10:00:00") : Carbon::parse($x->time_total));
                    if(new Carbon($x->total_jam) > new Carbon($lemburSetelah)){
                        return (new Carbon($x->total_jam))->diff(new Carbon($lemburSetelah))->format('%h:%I');
                    } else {
                        return null;
                    }
                  })
                ->addColumn('kurang', function($x){
                    if((new Carbon($x->tanggal))->dayOfWeek == Carbon::SATURDAY){
                        if(new Carbon($x->total_jam) < new Carbon("06:00:00")){
                            return (new Carbon($x->total_jam))->diff(new Carbon("06:00:00"))->format('%h:%I');
                        } else {
                            return null;
                        }
                    } elseif ((new Carbon($x->tanggal))->dayOfWeek == Carbon::SUNDAY){
                        return null;
                    } else {
                        if(new Carbon($x->total_jam) < new Carbon($x->time_total)){
                            return (new Carbon($x->total_jam))->diff(new Carbon($x->time_total))->format('%h:%I');
                        } else {
                            return null;
                        }
                    }
                  })
                ->rawColumns(['telat','cepat','lembur'])
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
            $query = (empty($request->get('select_group')) ? "":" && u.group_id = '".$request->get('select_group')."'");
            $query2 = (empty($request->get('select_unit')) ? "":" && u.unit_id = '".$request->get('select_unit')."'");
            $data = DB::select( DB::raw("SELECT users.name AS name2, u.name, IFNULL(u.username,u.username_old) as username, 
                uu.title as unit, a.hari, a.total, users.id AS usrid, u.group_id
                from wh_users u
                LEFT JOIN wh_user_groups g on g.uid = u.group_id
                LEFT JOIN wh_user_units uu on uu.uid = u.unit_id
                LEFT JOIN users ON users.username = u.username
                LEFT JOIN (
                    SELECT username, COUNT(hari) as hari,
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
                        WHERE tanggal >= '$start' && tanggal <= '$end'
                        GROUP BY tanggal, username
                    ) x2
                    GROUP BY username
                ) a on a.username = IFNULL(u.username,u.username_old)
                WHERE u.status = 1 ".$query.$query2." && (u.`username` = '".$user_id."' or u.`username_old` = '".$old."')
                GROUP BY IFNULL(u.username,u.username_old), u.name, users.name, users.id, u.group_id, u.unit_id, uu.title, a.hari, a.total
                ORDER BY hari desc, total desc
                ") );
        } else {
            $query = (empty($request->get('select_group')) ? "":" && u.group_id = '".$request->get('select_group')."'");
            $query2 = (empty($request->get('select_unit')) ? "":" && u.unit_id = '".$request->get('select_unit')."'");
            $data = DB::select( DB::raw("SELECT users.name AS name2, u.name, IFNULL(u.username,u.username_old) as username, 
                uu.title as unit, a.hari, a.total, users.id AS usrid, u.group_id
                from wh_users u
                LEFT JOIN wh_user_groups g on g.uid = u.group_id
                LEFT JOIN wh_user_units uu on uu.uid = u.unit_id
                LEFT JOIN users ON users.username = u.username
                LEFT JOIN (
                    SELECT username, COUNT(hari) as hari,
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
                        WHERE tanggal >= '$start' && tanggal <= '$end'
                        GROUP BY tanggal, username
                    ) x2
                    GROUP BY username
                ) a on a.username = IFNULL(u.username,u.username_old)
                WHERE u.status = 1 ".$query.$query2."
                GROUP BY IFNULL(u.username,u.username_old), u.name, users.name, users.id, u.group_id, u.unit_id, uu.title, a.hari, a.total
                ORDER BY hari desc, total desc"
                ) );
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

        $user = WhUser::with('unit')->where('username',$username)->orWhere('username_old',$username)->with('user')->first();
        $time_in = ($user->unit_id == null ? "08:00:00" : $user->unit->time_in);
        $time_out = ($user->unit_id == null ? "16:00:00" : $user->unit->time_out);
        $time_total = ($user->unit_id == null ? "08:00:00" : $user->unit->time_total);
        if($user != null){
            try {
                $endX = Carbon::parse($end)->subDay(1)->translatedFormat("Y-m-d H:i");
                $data = DB::select("WITH recursive all_dates(dt) AS (
                        SELECT '$start' dt
                        UNION ALL 
                        SELECT dt + INTERVAL 1 DAY FROM all_dates WHERE dt <= '$endX'
                    )
                    SELECT DATE(d.dt) AS tanggal, IF(a.username IS NULL && h.detail IS NOT NULL,'$user->username',a.username) as username, 
                    IF(h.detail IS NULL, a.masuk, IF(a.masuk IS NULL,TIMESTAMP(tanggal,'".$time_in."'), a.masuk)) as masuk, 
					IF(h.detail IS NULL, a.keluar, IF(a.keluar IS NULL,TIMESTAMP(tanggal,'".$time_out."'), a.keluar)) as keluar,  
					IF(h.detail IS NULL, a.total_jam, IF(a.total_jam > TIME('".$time_total."'),a.total_jam,TIME('".$time_total."'))) as total_jam, 
					h.detail as libur
                    FROM all_dates d
                    LEFT JOIN (
                        SELECT DATE(`timestamp`) AS tanggal, username, MIN(`timestamp`) AS masuk, MAX(`timestamp`) AS keluar, TIMEDIFF(MAX(`timestamp`), MIN(`timestamp`))AS total_jam 
                        FROM wh_attendances
                        WHERE (`username` = '$user->username' OR username = '$user->username_old')
                        GROUP BY tanggal, username
                        ORDER BY tanggal
                    ) a
                    ON d.dt = a.`tanggal`
                    LEFT JOIN wh_public_holidays h on d.dt = h.date
                    GROUP BY d.dt, username, masuk, keluar, total_jam
                    ORDER BY d.dt
                ") ;
               
            } catch (\Exception $e) {
                $startX = Carbon::parse($start)->translatedFormat("Y-m-d");
                $data = DB::select("SELECT v.tanggal, IF(a.username IS NULL && h.detail IS NOT NULL,'$user->username',a.username) as username, 
                IF(h.detail IS NULL, a.masuk, IF(a.masuk IS NULL,TIMESTAMP(v.tanggal,'".$time_in."'), a.masuk)) as masuk, 
                IF(h.detail IS NULL, a.keluar, IF(a.keluar IS NULL,TIMESTAMP(v.tanggal,'".$time_out."'), a.keluar)) as keluar,  
                IF(h.detail IS NULL, a.total_jam, IF(a.total_jam > TIME('".$time_total."'),a.total_jam,TIME('".$time_total."'))) as total_jam, 
                h.detail as libur   
                FROM  
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
                    LEFT JOIN wh_public_holidays h on v.tanggal = h.date
                    WHERE v.tanggal BETWEEN '$startX' AND '$end'
                    GROUP BY v.tanggal, username, masuk, keluar, total_jam, h.detail
                    ORDER BY v.tanggal;
                ") ;
            }
            // dd($data);
            $periode = Carbon::parse($start)->translatedFormat("d F Y")." - ".Carbon::parse($end)->translatedFormat("d F Y");
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $photo = 'assets/img/biophoto/face/'.$username.'.jpg';
            if(file_exists(public_path($photo))){
                $photo = asset($photo);
            } else {
                $photo = null;
            }
            return view('whr.view', compact('user', 'data', 'periode','link', 'photo', 'time_in', 'time_out', 'time_total')); 
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
            $idmesin = intval("1".Carbon::now()->translatedFormat("ymdH")); //mesin lt 1
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
                    if (env('APP_ENV') != 'local' && $zk->connect()){
                        $zk->clearAttendance(); // Remove attendance log only if not empty
                        $zk->disconnect();
                    }
                }
            } else {
                Log::info($info." machine ".$idmesin." Not Connect!");
            } 
        } catch (DecryptException $e) {
            Log::error($info." failed sync from machine 1");
            return response()->json([
                'success' => false,
                'total' => $i,
            ]);
        }
        
        //mesin lantai 2
        if(env('IP_ATTENDANCE_MACHINE_2')){
            try {
                $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE_2'));
                $idmesin = intval("2".Carbon::now()->translatedFormat("ymdH")); //mesin lantai 2
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
                        if (env('APP_ENV') != 'local' && $zk->connect()){
                            $zk->clearAttendance(); // Remove attendance log only if not empty
                            $zk->disconnect();
                        }
                    }
                } else {
                    Log::info($info." machine ".$idmesin." Not Connect!");
                } 
            } catch (DecryptException $e) {
                Log::error($info." failed sync from machine 2");
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
                $idmesin = intval("5".Carbon::now()->translatedFormat("ymdH")); //mesin lantai 5
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
                        if (env('APP_ENV') != 'local' && $zk->connect()){
                            $zk->clearAttendance(); // Remove attendance log only if not empty
                            $zk->disconnect();
                        }
                    }
                } else {
                    Log::info($info." machine ".$idmesin." Not Connect!");
                }  
            } catch (DecryptException $e) {
                Log::error($info." failed sync from machine 5");
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

    //sync data to Siakadcloud (siap.jgu.ac.id)
    public function siap_sync(Request $request){
        $date_start = null;
        $date_end = null;
        if (!empty($request->get('select_range'))) {
            if($request->get('select_range') != "" && $request->get('select_range') != null 
                && $request->get('select_range') != "Invalid date - Invalid date"){
                $x = explode(" - ",$request->get('select_range'));
                $date_start = date('Y-m-d',strtotime($x[0]));
                $date_end = date('Y-m-d',strtotime($x[1]));
            }
        }

        $conf['token_siakad'] = env('token_siakad');
        $conf['action'] = 'updatemany';
        $conf['url_siakad'] = env('url_siakad'); 
        $conf['attend_upload'] = date('Y-m-d'); // set desire date by default it today

        $start = microtime(true);
        $now = date('Y-m-d');
        if (!empty($request->get('date'))) {
            $now  = $request->get('date');
        }

        if($date_start != null){ //manual
            $result = DB::select( DB::raw("SELECT DISTINCT(a.username) AS idfinger, DATE_FORMAT(a.timestamp,'%Y-%m-%d') AS tglabsensi, 
	             concat(DATE_FORMAT(a.timestamp,'%Y-%m-%d'),' ',DATE_FORMAT(MIN(a.timestamp), '%H:%i:%s')) AS waktumasuk, 
				 IF(MIN(a.timestamp) = MAX(a.timestamp), NULL,
                 concat(DATE_FORMAT(a.timestamp,'%Y-%m-%d'),' ',DATE_FORMAT(MAX(a.timestamp), '%H:%i:%s'))) AS waktukeluar
                    FROM wh_attendances a 
                    WHERE DATE_FORMAT(a.timestamp,'%Y-%m-%d') >= '". $date_start ."' AND DATE_FORMAT(a.timestamp,'%Y-%m-%d')  <= '". $date_end ."'
                    GROUP BY a.username, DATE_FORMAT(a.timestamp,'%Y-%m-%d')
                    ORDER BY tglabsensi"
                ) );
        } else { //(-7 hari) - hari ini
            $result = DB::select( DB::raw("SELECT DISTINCT(a.username) AS idfinger, DATE_FORMAT(a.timestamp,'%Y-%m-%d') AS tglabsensi, 
            concat(DATE_FORMAT(a.timestamp,'%Y-%m-%d'),' ',DATE_FORMAT(MIN(a.timestamp), '%H:%i:%s')) AS waktumasuk, 
            IF(MIN(a.timestamp) = MAX(a.timestamp), NULL,
            concat(DATE_FORMAT(a.timestamp,'%Y-%m-%d'),' ',DATE_FORMAT(MAX(a.timestamp), '%H:%i:%s'))) AS waktukeluar
               FROM wh_attendances a 
               WHERE DATE_FORMAT(a.timestamp,'%Y-%m-%d') >= DATE_SUB('". $now ."', INTERVAL 3 DAY) AND DATE_FORMAT(a.timestamp,'%Y-%m-%d')  <= '". $now ."'
               GROUP BY a.username, DATE_FORMAT(a.timestamp,'%Y-%m-%d')
               ORDER BY tglabsensi"
           ) );
        }
        
        $respon_message = null;
        if( (count($result) == 0) ){
            echo json_encode(['message' => 'Tidak ada data absensi.']);
            $respon_message = 'Tidak ada data.';
        } else {
            $request2 = [];
            foreach($result AS $rows) {
                foreach($rows AS $key => $row) {
                    $res[$key] = $row;
                }
                $res['nomesin'] = null;
                $request2[] = $res;
            }

            // var_dump($request2);
            $body['token'] = $conf['token_siakad'];
            $body['action'] = $conf['action'];
            $body['data'] = $request2;

            $header[] = 'Content-type: application/json';

            $response = array();
            $curl = curl_init($conf['url_siakad']);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

            $json_response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            $response = json_decode($json_response, true);
            $end = microtime(true);
            $response['time'] = round($end - $start, 4);

            echo json_encode($response);
            $resp = json_decode(json_encode($response));
            $respon_message = (isset($resp->message) ? $resp->message : $resp );
        }
        if(Auth::check()){
            $info = (Auth::check() ? Auth::user()->username." : ".Auth::user()->name : "CronJob");
            Log::info($info." - sync data att to Siakadcloud (SIAP), response : ".$respon_message);
        } else {
            Log::info("auto sync data att to Siakadcloud (SIAP), response : ".$respon_message);
        }
    }

    public function weekly_attendance_report(){
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
       
        $list_wa = "";
        foreach($x as $d){
            $list_wa = $list_wa."\n".$d->name." (".(($diff+1) - $d->hari)." hari)";
            $x = [$d->name,(($diff+1) - $d->hari),$d->ID];
            if($d->group_id == 'JF'){
                array_push($data['item1'],$x);
            } else {
                array_push($data['item2'],$x);
            }
        }
        $data['catatan'] = "<br>Untuk melihat data karyawan secara keseluruhan dapat diakses melalui tautan berikut ini:<br>"
        ."<br><button><b><a target='_blank' href='".url('/WHR')."'>s.jgu.ac.id/WHR</a></b></button>";
        Mail::to($data['email'])->cc('eddy@jgu.ac.id')->queue(new WeeklyAttendanceReportMail($data));
        Log::info("Weekly report Att sended!");
        //----------------WA-------------------------------
        //$wa_to = "6281284174900"; //bu risma
        $wa_to = "6283802434392"; //revita
        if($wa_to != null){
            $WA_DATA = array();
            $WA_DATA['wa_to'] = $wa_to;
            $WA_DATA['wa_text'] = "Berikut ini merupakan data karyawan yang TIDAK ABSEN dalam minggu ini:\n".$list_wa."\n\nJika terdapat nama karyawan yg sudah resign silahkan laporkan ke tim ITIC.";
            dispatch(new JobNotificationWA($WA_DATA));
        }
        // ------------------end send to WA-----------------
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
                $zk->testVoice();
                // echo $zk->setTime(); 
                // echo $zk->getTime();
                echo intval("1".Carbon::now()->translatedFormat("ymdH"));

                        // $zk->clearAttendance(); // Remove attendance log only if not empty

                // $data = app('App\Http\Controllers\ZKTecoController')->getAttendance($zk);
                // return response()->json([
                //     'success' => true,
                //     'data' => $data
                // ]);

                //             $data = json_decode(json_encode(app('App\Http\Controllers\ZKTecoController')->getUser($zk)));

                //             dd($data);
                $zk->disconnect();   
            }
    }

    public function api_presensi(Request $request)
    {
        $today = Carbon::now();
        $start = $today->copy()->startOfday();
        $end = $today->copy()->endOfday();

        if (!empty($request->get('date_start')) && !empty($request->get('date_end'))) {
            $start = Carbon::createFromFormat('Y-m-d H:i:s',$request->get('date_start'));
            $end = Carbon::createFromFormat('Y-m-d H:i:s',$request->get('date_end'));
        }


        $data = DB::select( DB::raw("SELECT wh_attendances.username as NIP, wh_attendances.username as akun, 
            DATE_FORMAT(`timestamp`, '%d-%m-%Y') as tanggal, 
            TIME_FORMAT(MIN(`timestamp`), '%h%i') as masuk, TIME_FORMAT(MAX(`timestamp`), '%h%i') as keluar

            from wh_attendances
            JOIN wh_users on wh_users.username = wh_attendances.username
            WHERE `timestamp` >= '$start' and `timestamp` <= '$end'
            GROUP BY wh_attendances.username, DATE_FORMAT(`timestamp`, '%d-%m-%Y')
            ") );

        return response()->json([
            'info' => "untuk memfilter tanggal silahkan kirimkan get request date_start dan date_end dengan format Y-m-d H:i:s",
            'date_start' => $start->format('Y-m-d H:i:s'),
            'date_end' => $end->format('Y-m-d H:i:s'),
            'data' => $data
        ]);
    }

}
