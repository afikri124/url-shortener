<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\WhUser;
use App\Models\WhUserGroup;
use App\Models\WhUserUnit;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use Auth;
use Rats\Zkteco\Lib\ZKTeco;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;


class SettingController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update_birth_date($data){
         foreach ($data as $d) {
            $nip_or_nim = $d->attributes->nip ?? $d->attributes->nim;
            echo $nip_or_nim."<br>";
            $id_status = $d->attributes->id_status_aktif ?? $d->attributes->id_status_mahasiswa;
            $status = false;
            if($id_status == "A" || $id_status == "AA" ){
                $status = true;
            }
            $check_user = User::where('username', $nip_or_nim)->exists();
            if ($status && !$check_user){
                $check_email = User::where('email', $d->attributes->email)->exists();
                if(!$check_email){
                    $new_user = User::insert([
                        'name' => $d->attributes->nama,
                        'email' => $d->attributes->email,
                        'username' => $nip_or_nim,
                        'password'=> Hash::make($nip_or_nim),
                        'email_verified_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'birth_date' => $d->attributes->tanggal_lahir,
                        'status' => $status,
                    ]);
                    echo $nip_or_nim." berhasil dibuat otomatis dengan email ".$d->attributes->email."<br>";

                    $user = User::where('username', $nip_or_nim)->first();
                    if($id_status == "A" && !$user->hasRole('SD')){
                        $user->roles()->attach(Role::where('id', 'SD')->first());
                    } else if($id_status == "AA" && !$user->hasRole('ST')){
                        $user->roles()->attach(Role::where('id', 'ST')->first());
                    } else {
                        if(!$user->hasRole('GS')){
                            $user->roles()->attach(Role::where('id', 'GS')->first());
                        }
                    }
                }
            } else {
                $user_update = User::where('username',$nip_or_nim)->update([
                    'birth_date' => $d->attributes->tanggal_lahir,
                    'name' => $d->attributes->nama,
                    'status' => $status,
                ]);
                if($user_update){
                    $user = User::where('username', $nip_or_nim)->first();
                    if($id_status == "A" && !$user->hasRole('SD')){
                        $user->roles()->attach(Role::where('id', 'SD')->first());
                    } else if($id_status == "AA" && !$user->hasRole('ST')){
                        $user->roles()->attach(Role::where('id', 'ST')->first());
                    }
                    if($user->roles->isEmpty()){
                        $user->roles()->attach(Role::where('id', 'GS')->first());
                    } else {
                        if($user->hasRole('GS')){
                            $user->roles()->detach(
                                Role::where('id', 'GS')->first()
                            );
                        }
                    }
                    echo $nip_or_nim." berhasil update TL ".$d->attributes->tanggal_lahir."<br>";
                }
            }
        }
    }

    public function callSevimaAPI($url){
        $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-App-Key' => env('SevimaAPI_key'),
                    'X-Secret-Key' => env('SevimaAPI_secret'),
                ])->get($url);
        if ($response->successful()) {
            $data = json_decode(json_encode($response->json()));
            if(is_object($data)){ 
                $update = $this->update_birth_date($data->data);
                if($data->meta->last_page != $data->meta->current_page){
                    echo "next----------------------------".$data->urls->next."<br>";
                    self::callSevimaAPI($data->urls->next);
                }
            }
        }
    }
    public function sync_birth_date(Request $request)
    {
        try {
            $url = env('SevimaAPI_url').'/siakadcloud/v1/pegawai';
            $update = $this->callSevimaAPI($url);
            $url = env('SevimaAPI_url').'/siakadcloud/v1/dosen';
            $update = $this->callSevimaAPI($url);
            $url = env('SevimaAPI_url').'/siakadcloud/v1/mahasiswa';
            $update = $this->callSevimaAPI($url);
            
        } catch (\Exception $e) {
            Log::warning($e);
        }
    }

    public function user_login_us($id) {       
        $data = User::find($id);
        if($id == 1 || $data == null){
            abort(403, "Cannot access to restricted page");
        } else {
            Log::warning(Auth::user()->username." (".Auth::user()->name.")"." Login us #".$data->username." = ".$data->name);
            Auth::loginUsingId($data->id);
            return redirect()->route('home');
        }
    }

    public function account(Request $request)
    {
            $roles          = Role::get();
            $job            = User::select('job')->groupBy('job')->get();
            return view('setting.account', compact('roles','job'));      
    }

    public function account_data(Request $request)
    {
        $data = User::select('*');
        return Datatables::of($data)
                ->addColumn('roles',function(User $admin){
                    return $admin->roles->toArray();
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('select_role'))) {
                        $instance->whereHas('roles', function($q) use($request){
                            $q->where('role_id', $request->get('select_role'));
                        });
                    }
                    if (!empty($request->get('select_job'))) {
                        $instance->where('job', $request->get('select_job'));
                    }if (!empty($request->get('month'))) {
                        $instance->whereMonth('birth_date', $request->get('month'));
                    }
                    if (!empty($request->get('search'))) {
                         $instance->where(function($w) use($request){
                            $search = $request->get('search');
                                $w->orWhere('username', 'LIKE', "%$search%")
                                ->orWhere('name', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%")
                                ->orWhere('job', 'LIKE', "%$search%")
                                ->orWhere('birth_date', 'LIKE', "%$search%")
                                ->orWhere('phone', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addColumn('idd', function($x){
                    return Crypt::encrypt($x['id']);
                  })
                ->rawColumns(['idd'])
                ->make(true);
    }

    public function account_edit ($idd, Request $request)
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('setting_account');
        }
        $roles   = Role::get();
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'name' => ['required', 'string'],
                'username'=> ['required', 'string', 'max:255', Rule::unique('users')->ignore($id, 'id')],
                'email'=> ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id, 'id')],
                'job' => ['required', 'string'],
                'gender' => ['required', 'string'],
            ]);
            User::where('id', $id)->update([
                'name'=> $request->name,
                'username'=> $request->username,
                'email'=> $request->email,
                'job' => $request->job,
                'gender' => $request->gender,
                'front_title' => $request->front_title,
                'back_title' => $request->back_title,
                'updated_at' => Carbon::now()
            ]);
            $detach = User::find($id)->roles()->detach();
            $attach = User::find($id)->roles()->attach($request->roles);
            Log::info(Auth::user()->name." update user profile #".$id.", ".$request->name);
            return redirect()->route('setting_account_edit', ['id'=>$idd])->with('msg','Profil '.$request->name.' diperbarui!');
        }
        $data = User::find($id);
        if($id == 1 || $data == null){
            abort(403, "Access not allowed!");
        }
        return view('setting.account_edit', compact('data','roles'));
    }

    public function account_delete(Request $request) {
        $user = User::find($request->id);
        if($user){
            Log::warning(Auth::user()->username." deleted user #".$user->id.", username : ".$user->username.", name : ".$user->name);
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

    public function account_att(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'nama' => ['required', 'string','max:24'],
                'nik' => ['required'],
                'grup' => ['required']
            ]);
            $latest = WhUser::orderByDesc('uid')->first();
            $id = ($latest->uid + 1);
            try {
                $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
                if ($zk->connect()){
                    $x = app('App\Http\Controllers\ZKTecoController')
                    ->setUser($zk, $id, $request->nik, $request->nama, '', 0); 
                    $zk->disconnect();   
                    $new = WhUser::create([
                        'uid'=> $id,
                        'name'=> $request->nama,
                        'username'=> $request->nik,
                        'status'=> 1,
                        'role'=> 0,
                        'group_id' => $request->grup,
                        'unit_id' => $request->unit
                    ]);
                    if($new){
                        return redirect()->route('setting_account_att')->with('msg','Pengguna '.$request->nama.' BERHASIL dibuat!');
                    }
                }
            } catch (DecryptException $e) {
                Log::info(Auth::user()->name." Failed created data user att in machine uid = ".$id);
                return redirect()->route('setting_account_att')->with('msg','Pengguna '.$request->nama.' GAGAL dibuat!');
            }
        }
        $status          = json_decode(json_encode(array(['id' => "1", 'title' => "Aktif"], ['id' => "0", 'title' => "Tidak Aktif"])));
        $group          = WhUserGroup::get();
        $unit          = WhUserUnit::get();
        return view('setting.account_att', compact('status', 'group', 'unit'));      
    }

    public function account_att_data(Request $request)
    {
        $data = WhUser::with('user')->with('group')->with('unit')->select('*')->orderBy('uid');
        return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    if (!is_null($request->get('select_status'))) {
                        $instance->where('status', $request->get('select_status'));
                    }
                    if (!is_null($request->get('select_group'))) {
                        $instance->where('group_id', $request->get('select_group'));
                    }
                    if (!is_null($request->get('select_unit'))) {
                        $instance->where('unit_id', $request->get('select_unit'));
                    }
                    if (!empty($request->get('search'))) {
                         $instance->where(function($w) use($request){
                            $search = $request->get('search');
                                $w->orWhere('username', 'LIKE', "%$search%")
                                ->orWhere('name', 'LIKE', "%$search%")
                                ->orWhere('username_old', 'LIKE', "%$search%")
                                ->orWhere('cardno', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addColumn('idd', function($x){
                    return Crypt::encrypt($x['uid']);
                  })
                ->addColumn('userid', function($x){
                    if($x->user != null){
                        return Crypt::encrypt($x->user->id);
                    } else {
                        return null;
                    }
                  })
                ->rawColumns(['idd','userid'])
                ->make(true);
    }

     //sync data from machine
    public function account_att_sync()
    {
        $data = null;
        $i = 0;
        $info = (Auth::check() ? Auth::user()->username." : ".Auth::user()->name : "CronJob");
        $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
        if ($zk->connect()){
            $data = json_decode(json_encode(app('App\Http\Controllers\ZKTecoController')->getUser($zk)));
            $zk->disconnect();   
        } else {
            return response()->json([
                'success' => false
            ]);
        }       
        $NewUser = array();
        $UpdatedUser = array();
        $FailedUser = array();
        foreach ($data as $u) {
            $user = WhUser::where('uid',$u->uid)->first();
            if($user == null){
                $new_user = false;
                $new_user=WhUser::insert([
                        'uid' => $u->uid,
                        'username' => (strlen($u->userid) <  13 ? null : $u->userid),
                        'username_old' => (strlen($u->userid) <  13 ? $u->userid : null),
                        'name' => $u->name,
                        'role' => $u->role,
                        'password'=> $u->password,
                        'cardno' => $u->cardno,
                        'status' => true,
                        'created_at' => Carbon::now()
                ]);
                if($new_user){
                    array_push($NewUser,$u->name);
                    $i++;
                } else {
                    array_push($FailedUser,$u->name);
                }  
            } else {
                $old_user = WhUser::where('uid',$u->uid)->update([
                    'username' => (strlen($u->userid) <  13 ? null : $u->userid),
                    'name' => $u->name,
                    'role' => $u->role,
                    'password'=> $u->password,
                    'cardno' => $u->cardno,
                    'updated_at' => Carbon::now()
                ]);
                
                if($old_user){
                    array_push($UpdatedUser,$u->name);
                    $i++;
                } else {
                    array_push($FailedUser,$u->name);
                }  
            }
        }
        if(Auth::check()){
            Log::info($info." sync user att from machine, total user ".$i);
        }
        return response()->json([
            'success' => true,
            'total' => $i,
            'new' => $NewUser,
            'updated' => $UpdatedUser,
            'failed' => $FailedUser
        ]);
    }

    public function account_att_edit ($idd, Request $request)
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('setting_account_att');
        }
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'name' => ['required', 'string','max:24'],
                'status' => ['required', 'string','max:1'],
                'role' => ['required'],
                'grup' => ['required']
            ]);
            WhUser::where('uid', $id)->update([
                'name'=> $request->name,
                'status'=> $request->status,
                'role'=> $request->role,
                'username_old' => $request->old,
                'group_id' => $request->grup,
                'unit_id' => $request->unit,
                'updated_at' => Carbon::now()
            ]);
            try {
                $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
                if ($zk->connect()){
                    $userid = ($request->username == null ? $request->old : $request->username);
                    $x = app('App\Http\Controllers\ZKTecoController')->setUser($zk, $id, $userid, $request->name, '', $request->role); 
                    $zk->disconnect();   
                }
            } catch (DecryptException $e) {
                Log::info(Auth::user()->name." Failed update data user att in macchine uid = ".$id);
            }
            Log::info(Auth::user()->name." update user att #".$id.", ".$request->name);
            return redirect()->route('setting_account_att', ['id'=>$idd])->with('msg','Profil '.$request->name.' diperbarui!');
        }
        $status   = json_decode(json_encode(array(['id' => "1", 'title' => "Aktif"], ['id' => "0", 'title' => "Tidak Aktif"])));
        $data = WhUser::where('uid',$id)->first();
        $group          = WhUserGroup::get();
        $unit          = WhUserUnit::get();
        if($data == null){
            abort(403, "Access not allowed!");
        }
        return view('setting.account_att_edit', compact('data','status','group','unit'));
    }

    public function account_att_delete(Request $request) {
        $user = WhUser::where('uid', $request->uid)->first();
        if($user){
            Log::warning(Auth::user()->username." deleted WH-USER ATT #".$user->uid.", username : ".$user->username.", name : ".$user->name);
            WhUser::where('uid', $request->uid)->delete();
            try {
                $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
                if ($zk->connect()){
                    $x = app('App\Http\Controllers\ZKTecoController')->removeUser($zk,$request->uid); 
                    $zk->disconnect();   
                }
            } catch (DecryptException $e) {
                Log::info(Auth::user()->name." Failed deleted data user att in macchine uid = ".$request->uid);
            }
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
