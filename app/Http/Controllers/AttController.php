<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use PDF;
use Carbon\Carbon;
use App\Models\AttendanceActivity;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AttController extends Controller
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
                'type'          => 'E',
                'title'         => $request->title,
                'sub_title'     => $request->sub_title,
                'date'          => date('Y-m-d', strtotime($request->date)),
                'location'      => $request->location,
                'host'          => $request->host,
                'participant'   => $request->participant,
                'user_id'       => Auth::user()->id
            ]);
            if($data){
                return redirect()->route('att.index')->with('msg','Activity added successfully');
            }else{
                return redirect()->route('att.index')->with('msg','Activity failed to add!');
            }
        }else{
            $data = "";
            return view('att.index', compact('data'));
        }
            
    }

    public function data(Request $request)
    {
        $data = AttendanceActivity::where('type','E')->where('user_id', Auth::user()->id)->select('*')->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('shortlink', 'LIKE', "%$search%");
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
            return redirect()->route('url.index');
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
                'type'          => 'E',
                'title'         => $request->title,
                'sub_title'     => $request->sub_title,
                'date'          => date('Y-m-d', strtotime($request->date)),
                'location'      => $request->location,
                'host'          => $request->host,
                'participant'   => $request->participant,
                'user_id'       => Auth::user()->id
            ]);
            if($d){
                return redirect()->route('att.index')->with('msg','Data changed successfully!');
            }else{
                return redirect()->route('att.index')->with('msg','Data failed to change!');
            }
        }
        
        $data = AttendanceActivity::findOrFail($id);
        if($data->user_id != Auth::user()->id){
            abort(403);
        } else {
            return view('att.edit', compact('data'));
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
            return redirect()->route('att.index');
        }
        $x = AttendanceActivity::findOrFail($id);
        $tok = $x->type."".$x->user_id."".$x->id;
            $data = AttendanceActivity::with('user')->findOrFail($id);
            $link = route('att.att', ['id' => $id, 'token' => $tok] );
            $qr = "https://s.jgu.ac.id/qrcode?data=".$link."&label=".$link;

            // $pdf = PDF::loadview('att.pdf', compact('qr','data','link'));
            // return $pdf->stream("Attendance #".$data->id."-".$tok." - ".Carbon::now()->format('j F Y').".pdf");
            return view('att.pdf', compact('qr','data','link'));
    }

    public function list(Request $request)
    {
        if ($request->isMethod('post')) {
           //TO DO PRINT
        }else{
            $data = "";
            return view('att.list', compact('data'));
        }
            
    }
}
