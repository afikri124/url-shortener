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
                ->with('pics')
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
        $lists =  MomList::where('activity_id',$id)->with('pics')->get();
        $docs =  MomDoc::where('activity_id',$id)->get();
        return view('mom.meeting_id', compact('activity','lists','docs'));
    }

}
