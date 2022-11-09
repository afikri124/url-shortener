<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use PDF;
use Carbon\Carbon;
use App\Models\AttendanceActivity;
use App\Models\MomList;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class MoMController extends Controller
{
    //
    public function admin(Request $request)
    {
        $user = AttendanceActivity::select('attendance_activities.user_id', 'users.name')
                ->join('users','attendance_activities.user_id','=','users.id')
                ->groupBy('user_id', 'name')
                ->get();
        return view('mom.admin', compact('user'));
    }

    public function admin_data(Request $request)
    {
        $data = AttendanceActivity::where('notulen_username', Auth::user()->username)->with('user')->select('*')->orderByDesc("date");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        
                        if (!empty($request->get('select_pembuat'))) {
                            $instance->where('user_id', $request->get('select_pembuat'));
                        }
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('title', 'LIKE', "%$search%");
                        }
                    })
                    ->addColumn('idd', function($x){
                        return Crypt::encrypt($x['id']);
                      })
                    ->rawColumns(['idd'])
                    ->make(true);
    }

    public function user(Request $request)
    {
        $user = AttendanceActivity::select('attendance_activities.user_id', 'users.name')
                ->join('users','attendance_activities.user_id','=','users.id')
                ->groupBy('user_id', 'name')
                ->get();
        return view('mom.user', compact('user'));
    }

    public function user_data(Request $request)
    {
        $data = MomList::
        whereHas('pics', function($q) use($request){
            $q->where('username', Auth::user()->username);
        })
        ->with('activity')
        ->with('pics')
        ->select('*')
        ->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('detail', 'LIKE', "%$search%");
                        }
                    })
                    ->addColumn('idd', function($x){
                        return Crypt::encrypt($x['id']);
                    })
                    ->rawColumns(['idd'])
                    ->make(true);
    }

    public function user_list_id($idd, Request $request) {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            abort(403, "Data tidak ditemukan!");
        }
        $data = MomList::
                whereHas('pics', function($q) use($request){
                    $q->where('username', Auth::user()->username);
                })
                ->with('activity')
                ->with('pics')
                ->with('docs')
                ->findOrFail($id);
        return view('mom.user_list_id', compact('data'));
    }
}
