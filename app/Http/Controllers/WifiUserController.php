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
use Illuminate\Support\Facades\Log;

class WifiUserController extends Controller
{
    //
    function wifi (){
        $username = "USERNAME ANDA BELUM TERDAFTAR";
        $password = "SILAHKAN HUBUNGI ITIC JGU";
        if(Auth::user()->hasRole('ST')){
            $data = WifiUser::where("username", Auth::user()->username)->first();
            if($data != null){
                $username = $data->username;
                $password = $data->password;
                $update = WifiUser::where("username", Auth::user()->username)->update(['updated_at' => date('Y-m-d H:i:s'),'is_seen' => true]);
            }
        } else if (Auth::user()->hasRole('SD')){
            $data = WifiUser::where("username", "STUDENT")->first();
            if($data != null){
                $username = $data->username;
                $password = $data->password;
                $update = WifiUser::where("username", "STUDENT")->update(['updated_at' => date('Y-m-d H:i:s'),'is_seen' => true]);
            }
        } else if (Auth::user()->hasRole('GS')){
                $username = "GUEST";
                $password = "GUEST";
        }

        return view('user.wifi', compact('username','password'));
    }

    function index (Request $request){
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'nik' => ['required', 'string'],
                'nama_depan' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);
            $new = Wifiuser::create([
                'username'=> $request->nik,
                'password'=> $request->password,
                'first_name'=> strtoupper($request->nama_depan),
                'last_name'=> strtoupper($request->nama_belakang),
                'email'=> $request->email
            ]);
            if($new){
                return redirect()->route('setting_account_wifi')->with('msg','Pengguna '.$request->nik.', Kata sandi '.$request->password.' BERHASIL dibuat!');
            }
        }
        return view('setting.account_wifi');
    }

    public function data(Request $request)
    {
        $data = WifiUser::select(DB::raw("CONCAT(first_name,' ',IFNULL(last_name,'')) AS name, username, password, is_seen, updated_at, id"))->orderByDesc("updated_at")->orderBy("id");
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

    public function wifi_delete(Request $request) {
        $user = WifiUser::find($request->id);
        if($user){
            Log::warning(Auth::user()->username." deleted wifi user #".$user->id.", username : ".$user->username.", password : ".$user->password);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Akun gagal dihapus!'
            ]);
        }
    }
}
