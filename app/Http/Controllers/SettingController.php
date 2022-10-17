<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

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
            $user          = User::where('username','!=', 'admin')->get();
            return view('setting.account', compact('user','roles'));      
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
                    if (!empty($request->get('select_user'))) {
                        $instance->where('id', $request->get('select_user'));
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
}
