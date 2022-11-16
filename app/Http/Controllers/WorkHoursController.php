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
        echo "Sabar, masih di coding.. hehe";
    }

    public function whr(){
        $user = WhUser::with('user')->select('*')->orderBy('name')
                ->whereNotNull('username')
                ->get();
        return view('whr.index', compact('user')); 
    }

    public function whr_data(Request $request)
    {
        $data = WhAttendance::
        leftjoin('wh_users', function($join){
            $join->on('wh_users.username','=','wh_attendances.username'); // i want to join the users table with either of these columns
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
        // ->where('wh_attendances.username','s092021100001')
        ->groupBy( DB::raw('DATE(`timestamp`)'),'wh_attendances.username','wh_users.name')
        ->orderByDesc('masuk');
        return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('select_user'))) {
                        $instance->where('wh_attendances.username', $request->get('select_user'));
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
                    if($x->total_jam != "00:00:00"){
                        return (new Carbon($x->masuk))->diff(new Carbon($x->tanggal." 08:00:00"))->format('%h:%I');
                    } else {
                        return null;
                    }
                  })
                ->addColumn('cepat', function($x){
                    if(new Carbon($x->keluar) < new Carbon($x->tanggal." 16:00:00")){
                        return (new Carbon($x->keluar))->diff(new Carbon($x->tanggal." 16:00:00"))->format('-%h:%I');
                    } else {
                        return null;
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

    public function whr_sync(Request $request)
    {
        $data = null;
        $i = 0;
        $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
        if ($zk->connect()){
            $data = array_reverse(app('App\Http\Controllers\ZKTecoController')->getAttendance($zk), true);
            $zk->disconnect();   
        }      
        $breakId = null;
        $user = WhAttendance::orderByDesc('uid')->first();
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
            Log::info(Auth::user()->username." sync data att from machine, total : ".$i);
            return response()->json([
                'success' => true,
                'total' => $i,
            ]);
        } else {
            Log::info(Auth::user()->username." failed sync data att from machine, breakid".$breakId.", total : ".$i);
            return response()->json([
                'success' => true,
                'total' => $i,
            ]);
        }

    }

    public function zk(){
        $user = WhAttendance::orderByDesc('uid')->first();
        dd($user);
            // $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
            // if ($zk->connect()){

                // $role = 0; //14= super admin, 0=User :: according to ZKtecho Machine
                // $users = $zk->getUser();
                // $total = end($users);
                // $lastId=$total[3]+1;

                // 1 = uid
                // 2 = userid
                // 3 = nama (max 24 char)
                // 4 = password
                // 5 = role (14 : admin, 0 : user)
                // $x = $zk->setUser(217, 'S092021100001', 'ALI FIKRI', '', 14);
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

                // $data = app('App\Http\Controllers\ZKTecoController')->getUser($zk);
                // return response()->json([
                //     'success' => true,
                //     'data' => $data
                // ]);

    //             $data = json_decode(json_encode(app('App\Http\Controllers\ZKTecoController')->getUser($zk)));

    //             dd($data);
    //             $zk->disconnect();   
    //         }
    }

}
