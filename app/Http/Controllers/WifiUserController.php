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
use Illuminate\Support\Str;


class WifiUserController extends Controller
{
    //
    function wifi (){
        if(Auth::user()->username == null || Auth::user()->phone == null){
            return redirect()->route('update_profile')->with('msg','LENGKAPI DATA ANDA TERLEBIH DAHULU!');
        }

        $group = "BELUM TERDAFTAR!";
        $username = Auth::user()->username;
        $password = strtoupper(Str::random(5)).rand(100,999);

        if(Auth::user()->hasRole('ST')){
            $groupRadius = "Dosen-Staff";
        } else if (Auth::user()->hasRole('SD')){
            $groupRadius = "Mahasiswa";
        } else {
            $groupRadius = "Tamu";
        }
        $wu = WifiUser::where('username', Auth::user()->username)->first();
        if($wu == null){
            $data = WifiUser::insert([
                'username'=> $username,
                'password'=> $password,
                'first_name'=> Auth::user()->name,
                'last_name'=> null,
                'email'=> Auth::user()->email,
                'wifi_group' => $groupRadius,
                'is_seen' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $password = $wu->password;
            $updateWU = WifiUser::where('username', Auth::user()->username)->update([
                'first_name'=> Auth::user()->name,
                'last_name'=> null,
                'email'=> Auth::user()->email,
                'wifi_group' => $groupRadius,
                'is_seen' => true,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        try {
        $radius = DB::connection('mysql2')->table('radcheck')->where('username',Auth::user()->username)->first();
            if($radius == null){                
                DB::beginTransaction();
                try {
                    $radiusAdd = DB::connection('mysql2')->table('radcheck')->insert([
                        'username'=> $username,
                        'value'=> $password,
                        'attribute'=> "Cleartext-Password",
                        'op'=> ":="
                    ]);
                    if($radiusAdd){
                        $radiusgroupAdd = DB::connection('mysql2')->table('radusergroup')->insert([
                            'username'=> $username,
                            'groupname'=> $groupRadius,
                            'priority'=> 0
                        ]);
                        $radiusUserInfoAdd = DB::connection('mysql2')->table('userinfo')->insert([
                            'username'=> $username,
                            'firstname'=> Auth::user()->name,
                            'lastname'=> Auth::user()->job,
                            'email'=> Auth::user()->email,
                            'creationdate' => date("Y-m-d H:i:s"),
                            'updatedate' => date("Y-m-d H:i:s"),
                            'creationby' => 'administrator'
                        ]);
                        $radiusUserBillInfoAdd = DB::connection('mysql2')->table('userbillinfo')->insert([
                            'username'=> $username,
                            'lastbill' => date("Y-m-d"),
                            'nextbill' => date("Y-m-d"),
                            'creationdate' => date("Y-m-d H:i:s"),
                            'updatedate' => date("Y-m-d H:i:s"),
                            'creationby' => 'administrator'
                        ]);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::info("Wifi Radius error : ".$e);
                }
            } else {
                $radiusUpdate = DB::connection('mysql2')->table('radcheck')->where('username',Auth::user()->username)->update([
                    'username'=> $username,
                    'value'=> $password
                ]);
                $radiusgroupUpdate = DB::connection('mysql2')->table('radusergroup')->where('username',Auth::user()->username)->update([
                    'username'=> $username,
                    'groupname'=> $groupRadius,
                    'priority'=> 0
                ]);
            }
            $radiusGroup = DB::connection('mysql2')->table('radusergroup')->where('username',Auth::user()->username)->first();
            $group = $radiusGroup->groupname;
        } catch (\Exception $e) {
            Log::info("Wifi Radius error : ".$e);
            $group = "ERROR : SERVER RADIUS SEDANG DOWN!";
        }
        return view('user.wifi', compact('username','password','group'));
    }

    public function wifi_edit (Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'password_baru' => ['required', 'string', 'max:16'],
            ]);
            WifiUser::where('username', Auth::user()->username)->update([
                'password'=> $request->password_baru,
                'updated_at' => Carbon::now()
            ]);
            return redirect()->route('wifi')->with('msg','Password berhasil diperbarui!');
        }
        $data = WifiUser::where('username', Auth::user()->username)->first();
        if($data == null){
            abort(403, "Access not allowed!");
        }
        return view('user.wifi_edit', compact('data'));
    }

    function update_profile(Request $request){
        $roles   = Role::get();
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'email'=> ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::user()->id, 'id')],
                'username'=> ['required', 'string', 'max:255', Rule::unique('users')->ignore(Auth::user()->id, 'id')],
                'job' => ['required', 'string'],
                'name' => ['required', 'string'],
                'gender' => ['required']
            ]);
            User::where('id', Auth::user()->id)->update([
                'name'=> $request->name,
                'username' => $request->username,
                'job' => $request->job,
                'email'=> $request->email,
                'phone'=> $request->phone,
                'gender'=> $request->gender,
            ]);
            return redirect()->route('wifi')->with('msg','Profil telah diperbarui!');
        }
        $gender = [['id' => 'M', 'title' => "Pria"], ['id' => 'F', 'title' => "Wanita"]];
        return view('user.wifi_update_profile', compact('roles','gender'));
    }

    function index (Request $request){
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'username' => ['required', 'string', Rule::unique('wifi_users')],
                'nama_depan' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);
            $new = Wifiuser::create([
                'username'=> $request->username,
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
        $data = WifiUser::select(DB::raw("CONCAT(first_name,' ',IFNULL(last_name,'')) AS name, username, password, wifi_group, is_seen, updated_at, id"))->orderByDesc("updated_at")->orderBy("id");
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
