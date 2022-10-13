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

class MtController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'title'         => ['required'],
                'sub_title'     => ['required'],
                'date'          => ['required', 'date'],
                'location'      => ['required'],
                'sub_title'     => ['required'],
                'host'          => ['required'],
                'participant'   => ['required'],
            ]);
            $data = AttendanceActivity::create([
                'type'               => 'M',
                'title'              => $request->title,
                'sub_title'          => $request->sub_title,
                'date'               => date('Y-m-d', strtotime($request->date)),
                'location'           => $request->location,
                'host'               => $request->host,
                'participant'        => $request->participant,
                'notulen_username'   => $request->notulen,
                'user_id'            => Auth::user()->id
            ]);
            if($data){
                return redirect()->route('mt.index')->with('msg','Activity added successfully');
            }else{
                return redirect()->route('mt.index')->with('msg','Activity failed to add!');
            }
        }else{
            $data = "";
            return view('mt.index', compact('data'));
        }
            
    }

    public function data(Request $request)
    {
        $data = AttendanceActivity::where('type','M')->where('user_id', Auth::user()->id)->select('*')->orderByDesc("id");
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
                    ->addColumn('uid', function($x){
                        return Crypt::encrypt($x['user_id']);
                      })
                    ->addColumn('token', function($x){
                        return $x['type'].$x['user_id'].$x['id'];
                      }) 
                    ->rawColumns(['idd','uid', 'token'])
                    ->make(true);
    }

    public function edit($idd, Request $request) 
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('mt.index');
        }
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'title'         => ['required'],
                'sub_title'     => ['required'],
                'date'          => ['required', 'date'],
                'location'      => ['required'],
                'sub_title'     => ['required'],
                'host'          => ['required'],
                'participant'   => ['required'],
            ]);
            $data = AttendanceActivity::findOrFail($id);
            $d = $data->update([ 
                'type'               => 'M',
                'title'              => $request->title,
                'sub_title'          => $request->sub_title,
                'date'               => date('Y-m-d', strtotime($request->date)),
                'location'           => $request->location,
                'host'               => $request->host,
                'participant'        => $request->participant,
                'notulen_username'   => $request->notulen,
                'user_id'            => Auth::user()->id
            ]);
            if($d){
                return redirect()->route('mt.index')->with('msg','Data changed successfully!');
            }else{
                return redirect()->route('mt.index')->with('msg','Data failed to change!');
            }
        }
        
        $data = AttendanceActivity::findOrFail($id);
        if($data->user_id != Auth::user()->id){
            abort(403);
        } else {
            return view('mt.edit', compact('data'));
        }
    }

    public function delete(Request $request) 
    {
        $data = AttendanceActivity::find($request->id);
        if($data && $data->user_id == Auth::user()->id){
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data deleted successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not allowed to delete this data!'
            ]);
        }
    }

    public function print($idd) {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('mt.index');
        }
        $x = AttendanceActivity::findOrFail($id);
        $tok = $x->type."".$x->user_id."".($x->id+3);
            $data = AttendanceActivity::with('user')->findOrFail($id);
            $link = route('attendance', ['id' => $id, 'token' => $tok] );
            $qr = "https://s.jgu.ac.id/qrcode?data=".$link;
            $pdf = PDF::loadview('mt.pdf', compact('qr','data','link','tok'));
            return $pdf->stream("Attendance #".$data->id."-".$tok." - ".Carbon::now()->format('j F Y').".pdf");
            // return view('mt.pdf', compact('qr','data','link'));
    }

    public function list($idd, Request $request)
    {
        if ($request->isMethod('post')) {
           //TO DO PRINT
            try {
                $id = Crypt::decrypt($idd);
            } catch (DecryptException $e) {
                return redirect()->route('mt.index');
            }
            $x = AttendanceActivity::findOrFail($id);
            $tok = $x->type."".$x->user_id."".($x->id+3);
            $data = AttendanceActivity::with('user')->findOrFail($id);
            $link = route('attendance', ['id' => $id, 'token' => $tok] );
            $qr = "https://s.jgu.ac.id/qrcode?data=".$link;
            $al = Attendance::where('activity_id', $id)->with('user')->select('*')->orderBy("id")->get();
            $pdf = PDF::loadview('mt.print', compact('qr','data','link','tok', 'al'));
            return $pdf->stream("Attendance #".$data->id."-".$tok." - ".Carbon::now()->format('j F Y').".pdf");
            // return view('mt.print', compact('qr','data','link', 'tok', 'al'));
        }else{
            try {
                $id = Crypt::decrypt($idd);
            } catch (DecryptException $e) {
                return redirect()->route('mt.index');
            }
            return view('mt.list', compact('id'));
        }     
    }

    public function list_data($id, Request $request)
    {
        $data = Attendance::where('activity_id', $id)->with('user')->select('*')->orderBy("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $s = $request->get('search');
                            $instance->whereHas('user', function($q) use($s){
                                $q->where('name', 'LIKE', "%$s%");
                            });
                        }
                    })
                    ->addColumn('date', function($x){
                        return date('d M Y H:i', strtotime($x['created_at']));
                      }) 
                    ->rawColumns(['date'])
                    ->make(true);
    }
}
