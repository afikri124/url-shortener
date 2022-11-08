<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use PDF;
use Carbon\Carbon;
use App\Models\AttendanceActivity;
use App\Models\Attendance;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class AttendanceController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = AttendanceActivity::select('attendance_activities.user_id', 'users.name')
                ->join('users','attendance_activities.user_id','=','users.id')
                ->groupBy('user_id', 'name')
                ->get();
        return view('attendance.index_recap', compact('user'));
    }

    public function data(Request $request)
    {
        $data = AttendanceActivity::with('user')->select('*')->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        
                        if (!empty($request->get('select_pembuat'))) {
                            $instance->where('user_id', $request->get('select_pembuat'));
                        }
                        if (!empty($request->get('select_tipe'))) {
                            $instance->where('type', $request->get('select_tipe'));
                        }
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('title', 'LIKE', "%$search%");
                        }
                    })
                    ->addColumn('idd', function($x){
                        return Crypt::encrypt($x['id']);
                      })
                    ->addColumn('token', function($x){
                        return $x['type'].$x['user_id'].($x['id']+3);
                      }) 
                    ->rawColumns(['idd', 'token'])
                    ->make(true);
    }

    public function print($idd) {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('attendance.index');
        }
        $x = AttendanceActivity::findOrFail($id);
        $tok = $x->type."".$x->user_id."".($x->id+3);
            $data = AttendanceActivity::with('user')->findOrFail($id);
            $link = route('attendance', ['id' => $id, 'token' => $tok] );
            $qr = "https://s.jgu.ac.id/qrcode?data=".$link;
            $pdf = PDF::loadview('attendance.qr', compact('qr','data','link','tok'));
            return $pdf->stream("Attendance #".$data->id."-".$tok." - ".Carbon::parse($data->date)->translatedFormat('j F Y').".pdf");
            // return view('attendance.qr', compact('qr','data','link'));
    }
}
