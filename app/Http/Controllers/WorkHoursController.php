<?php

namespace App\Http\Controllers;
use Rats\Zkteco\Lib\ZKTeco;
use Rats\Zkteco\Lib\Helper\Util;
// use Laradevsbd\Zkteco\Http\Library\ZktecoLib;

use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    //
    public function index(){
            $zk = new ZKTeco('172.18.83.2');
            // $zk = new ZktecoLib('172.18.83.2');
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


                // $zk->removeUser(219); 
                // return "Add user success";

                $this->setUser($zk, 217, 'S092021100001', 'ALI FIKRI BARU', '', 14);

                // $data = $this->getUser($zk);
                // return response()->json([
                //     'success' => true,
                //     'data' => $data
                // ]);
                
            }
    }

    public function getUser(ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_USER_TEMP_RRQ;
        $command_string = chr(Util::FCT_USER);

        $session = $self->_command($command, $command_string, Util::COMMAND_TYPE_DATA);
        if ($session === false) {
            return [];
        }

        $userData = Util::recData($self);

        $users = [];
        if (!empty($userData)) {
            $userData = substr($userData, 11);

            while (strlen($userData) > 72) {
                $u = unpack('H144', substr($userData, 0, 72));

                $u1 = hexdec(substr($u[1], 2, 2));
                $u2 = hexdec(substr($u[1], 4, 2));
                $uid = $u1 + ($u2 * 256);
                $cardno = hexdec(substr($u[1], 78, 2) . substr($u[1], 76, 2) . substr($u[1], 74, 2) . substr($u[1], 72, 2)) . ' ';
                $role = hexdec(substr($u[1], 6, 2)) . ' ';
                $password = hex2bin(substr($u[1], 8, 16)) . ' ';
                $name = hex2bin(substr($u[1], 24, 74)) . ' ';
                $userid = hex2bin(substr($u[1], 98, 72)) . ' ';

                //Clean up some messy characters from the user name
                $password = explode(chr(0), $password, 2);
                $password = $password[0];
                $userid = explode(chr(0), $userid, 2);
                $userid = $userid[0];
                $name = explode(chr(0), $name, 3);
                $name = utf8_encode($name[0]);
                $cardno = str_pad($cardno, 11, '0', STR_PAD_LEFT);

                if ($name == '') {
                    $name = $userid;
                }

                $users[$userid] = [
                    'uid' => $uid,
                    'userid' => $userid,
                    'name' => $name,
                    'role' => intval($role),
                    'password' => $password,
                    'cardno' => $cardno,
                ];

                $userData = substr($userData, 72);
            }
        }

        return $users;
    }

    static public function setUser(ZKTeco $self, $uid, $userid, $name, $password, $role = Util::LEVEL_USER, $cardno = 0)
    {
        $self->_section = __METHOD__;

        // if (
        //     (int)$uid === 0 ||
        //     (int)$uid > Util::USHRT_MAX ||
        //     strlen($userid) > 9 ||
        //     strlen($name) > 24 ||
        //     strlen($password) > 8 ||
        //     strlen($cardno) > 10
        // ) {
        //     return false;
        // }

        $command = Util::CMD_SET_USER;
        $byte1 = chr((int)($uid % 256));
        $byte2 = chr((int)($uid >> 8));
        $cardno = hex2bin(Util::reverseHex(dechex($cardno)));

        $command_string = implode('', [
            $byte1,
            $byte2,
            chr($role),
            str_pad($password, 8, chr(0)),
            str_pad($name, 24, chr(0)),
            str_pad($cardno, 4, chr(0)),
            str_pad(chr(1), 9, chr(0)),
            str_pad($userid, 24, chr(0)),
            // str_repeat(chr(0), 15)
        ]);
//        die($command_string);
        // return $self->_command($command, $command_string);
        dd($command_string);
    }

    static public function getAttendance(ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_ATT_LOG_RRQ;
        $command_string = '';

        $session = $self->_command($command, $command_string, Util::COMMAND_TYPE_DATA);
        if ($session === false) {
            return [];
        }

        $attData = Util::recData($self);

        $attendance = [];
        if (!empty($attData)) {
            $attData = substr($attData, 10);

            while (strlen($attData) > 40) {
                $u = unpack('H78', substr($attData, 0, 39));

                $u1 = hexdec(substr($u[1], 4, 2));
                $u2 = hexdec(substr($u[1], 6, 2));
                $uid = $u1 + ($u2 * 256);
                $id = hex2bin(substr($u[1], 8, 18));
                $id = str_replace(chr(0), '', $id);
                $userid = hex2bin(substr($u[1], 8, 26));
                $state = hexdec(substr($u[1], 56, 2));
                $timestamp = Util::decodeTime(hexdec(Util::reverseHex(substr($u[1], 58, 8))));
                $type = hexdec(Util::reverseHex(substr($u[1], 66, 2 )));
				
                $attendance[] = [
                    'uid' => $uid,
                    'id' => $id,
                    'userid' => $userid,
                    'state' => $state,
                    'timestamp' => $timestamp,
                    'type' => $type
                ];

                $attData = substr($attData, 40);
            }

        }

        return $attendance;
    }
}
