<?php

namespace App\Http\Controllers;
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

use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    //
    public function wh(){
        $user = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
        $lastData = WhAttendance::orderByDesc('timestamp')->first();
        return view('wh.index', compact('user', 'lastData')); 
    }

    public function whr(){
        $user = WhUser::where('status',1)->with('user')->select('*')->orderBy('name')->get();
        $lastData = WhAttendance::orderByDesc('timestamp')->first();
        return view('whr.index', compact('user', 'lastData')); 
    }

    public function wh_data(Request $request)
    {
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
            ->where('wh_attendances.username',Auth::user()->username)
            ->groupBy( DB::raw('DATE(`timestamp`)'),'wh_attendances.username','wh_users.name')
            ->orderByDesc('masuk');
        }
        return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('select_user'))) {
                        $instance->where('wh_attendances.username', $request->get('select_user'));
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
                    -- WHERE `timestamp` >= '$start' && `timestamp` <= '$end' && `username` = '$user_id'
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
                    Log::info($info." sync data att from machine, breakid : ".$breakId.", total new : ".$i);
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
                // $x = $zk->setUser(220, '048', 'YULIANTO HADIPRAWIRO', '', 0);
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

                // app('App\Http\Controllers\ZkTecoController')->setUser($zk, 217, 'S092021100001', 'ALI FIKRI (baru)', '', 14);

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
