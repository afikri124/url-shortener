<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Role;
use App\Models\WifiUser;
use Auth;
use Yajra\DataTables\DataTables;
use DB;
use Carbon\Carbon;

class WifiUserController extends Controller
{
    //
    function wifi (){
        $username = "AKUN ANDA BELUM TERDAFTAR";
        $password = "PASTIKAN USERNAME ANDA BENAR";
        if(Auth::user()->hasRole('ST')){
            $data = WifiUser::where("username", Auth::user()->username)->first();
            if($data != null){
                $username = $data->username;
                $password = $data->password;
            }
            WifiUser::where("username", Auth::user()->username)->update(['updated_at' => date('Y-m-d H:i:s')]);
        } else if (Auth::user()->hasRole('SD')){
            $data = WifiUser::where("username", "STUDENT")->first();
            if($data != null){
                $username = $data->username;
                $password = $data->password;
            }
        }

        return view('user.wifi', compact('username','password'));
    }

    function index (){
        return view('setting.account_wifi');
    }

    public function data(Request $request)
    {
        $data = WifiUser::select(DB::raw("CONCAT(first_name,' ',IFNULL(last_name,'')) AS name, username, password, is_seen, updated_at"))->orderBy("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                               $search = $request->get('search');
                                   $w->orWhere(DB::raw("CONCAT(first_name,' ',IFNULL(last_name,''))"), 'LIKE', "%$search%")
                                   ->orWhere('username', 'LIKE', "%$search%");
                           });
                       }
                    })
                    ->make(true);
    }
}
