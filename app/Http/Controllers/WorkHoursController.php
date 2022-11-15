<?php

namespace App\Http\Controllers;
use Rats\Zkteco\Lib\ZKTeco;
use App\Http\Controllers\ZkTecoController;

use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    //
    protected $ZkTecoController;
    public function __construct(ZkTecoController $ZkTecoController)
    {
        $this->ZkTecoController = $ZkTecoController;
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

                // $data = app('App\Http\Controllers\ZkTecoController')->getUser($zk);
                // return response()->json([
                //     'success' => true,
                //     'data' => $data
                // ]);

                $data = json_decode(json_encode($this->ZkTecoController->getUser($zk)));

                dd($data);
                $zk->disconnect();   
            }
    }

}
