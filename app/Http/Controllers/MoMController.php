<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use PDF;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceActivity;
use App\Models\MomDoc;
use App\Models\MomList;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class MoMController extends Controller
{
    //
    public function notetaker(Request $request)
    {
        $user = AttendanceActivity::select('attendance_activities.user_id', 'users.name')
                ->join('users','attendance_activities.user_id','=','users.id')
                ->groupBy('user_id', 'name')
                ->get();
        return view('mom.notetaker', compact('user'));
    }

    public function notetaker_data(Request $request)
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

    public function notetaker_id($idd, Request $request) {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            abort(403, "Data tidak ditemukan!");
        }
        $activity   = AttendanceActivity::findOrFail($id);
        $users      = User::whereHas('roles', function($q){
                                $q->where('role_id','ST');
                            })->where('username','!=', 'admin')
                            ->get();
        return view('mom.notetaker_id', compact('activity','users'));
    }

    public function notetaker_id_data($id, Request $request)
    {
        $data = MomList::join('attendance_activities','attendance_activities.id','=','mom_lists.activity_id')
        ->where('attendance_activities.id', $id)
        ->where('attendance_activities.notulen_username', Auth::user()->username)
        ->select('mom_lists.*', 'attendance_activities.notulen_username')
        ->with(['pics' => function ($query) {
            $query->select('name');
        }])
        ->orderBy("mom_lists.id");
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

    public function notetaker_add(Request $request) 
    {
        return response()->json([
            'success' => true,
            'message' => 'Tidak diizinkan untuk menghapus data ini!'
        ]);
        // $data = AttendanceActivity::find($request->id);
        // $att = Attendance::where("activity_id", $request->id)->count();
        // if($att != 0){
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Absensi ini sudah terpakai, mohon hapus dulu daftar pesertanya!'
        //     ]);
        // } else {
        //     if($data && $data->user_id == Auth::user()->id){
        //         Log::warning(Auth::user()->name." delete AttendanceActivity #".$data->id.", ".$data->title);
        //         $data->delete();
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Data berhasil dihapus!'
        //         ]);
        //     } else {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Tidak diizinkan untuk menghapus data ini!'
        //         ]);
        //     }
        // }
    }


    public function PIC(Request $request)
    {
        $user = AttendanceActivity::select('attendance_activities.user_id', 'users.name')
                ->join('users','attendance_activities.user_id','=','users.id')
                ->groupBy('user_id', 'name')
                ->get();
        return view('mom.PIC', compact('user'));
    }

    public function PIC_data(Request $request)
    {
        $data = MomList::
        whereHas('pics', function($q) use($request){
            $q->where('username', Auth::user()->username);
        })
        ->with('activity')
        ->with(['pics' => function ($query) {
            $query->select('name');
        }])
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

    public function PIC_id($idd, Request $request) {
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
                ->with(['pics' => function ($query) {
                    $query->select('name');
                }])
                ->with('docs')
                ->findOrFail($id);
        return view('mom.PIC_id', compact('data'));
    }

    public function meeting(Request $request)
    {
        return view('mom.meeting');
    }

    public function meeting_data(Request $request)
    {
        $data = AttendanceActivity::
        join('attendances', 'attendances.activity_id', '=', 'attendance_activities.id')
        ->join('mom_lists', 'mom_lists.activity_id', '=', 'attendance_activities.id')
        ->where('attendances.username', Auth::user()->username)
        ->selectRaw('attendance_activities.id, attendance_activities.title, attendance_activities.date, attendance_activities.location, attendance_activities.host, attendance_activities.participant, count(mom_lists.id) as MoM')
        ->groupBy('attendance_activities.id', 'attendance_activities.title', 'attendance_activities.date', 'attendance_activities.location', 'attendance_activities.host', 'attendance_activities.participant')
        ->orderByDesc("attendance_activities.date");

            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
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

    public function meeting_id($idd, Request $request) {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            abort(403, "Data tidak ditemukan!");
        }
        $activity =  AttendanceActivity::findOrFail($id);
        $lists =  MomList::where('activity_id',$id)
            ->with(['pics' => function ($query) {
                $query->select('name');
            }])->get();
        $docs =  MomDoc::where('activity_id',$id)->get();
        return view('mom.meeting_id', compact('activity','lists','docs'));
    }

}
