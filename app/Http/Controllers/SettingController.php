<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\WhUser;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use Auth;
use Rats\Zkteco\Lib\ZKTeco;


use Carbon\Carbon;


class SettingController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
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
                    }
                    if (!empty($request->get('search'))) {
                         $instance->where(function($w) use($request){
                            $search = $request->get('search');
                                $w->orWhere('username', 'LIKE', "%$search%")
                                ->orWhere('name', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%")
                                ->orWhere('job', 'LIKE', "%$search%");
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
            $status          = json_decode(json_encode(array(['id' => "1", 'title' => "Aktif"], ['id' => "0", 'title' => "Tidak Aktif"])));
            return view('setting.account_att', compact('status'));      
    }

    public function account_att_data(Request $request)
    {
        $data = WhUser::with('user')->select('*')->orderBy('name');
        return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    if (!is_null($request->get('select_status'))) {
                        $instance->where('status', $request->get('select_status'));
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
                    return Crypt::encrypt($x['id']);
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

    public function account_att_sync(Request $request)
    {
        $data = null;
        $zk = new ZKTeco(env('IP_ATTENDANCE_MACHINE'));
        if ($zk->connect()){
            $data = json_decode(json_encode(app('App\Http\Controllers\ZkTecoController')->getUser($zk)));
            $zk->disconnect();   
        } else {

        }       
        $i = 0;
        $NewUser = array();
        $UpdatedUser = array();
        $FailedUser = array();
        foreach ($data as $u) {
            $prodi = null;
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
                        'status' => 1,
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
        return response()->json([
            'total' => $i,
            'new' => $NewUser,
            'updated' => $UpdatedUser,
            'failed' => $FailedUser
        ]);
    }

}
