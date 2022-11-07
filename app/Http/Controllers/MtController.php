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
                'judul'             => ['required'],
                'tanggal'           => ['required', 'date'],
                'tenggat_absensi'       => ['required', 'date'],
                'lokasi'            => ['required'],
                'pimpinan_rapat'    => ['required'],
                'peserta'           => ['required'],
            ]);
            $data = AttendanceActivity::create([
                'type'               => 'M',
                'title'              => $request->judul,
                'date'               => date('Y-m-d', strtotime($request->tanggal)),
                'expired'       => date('Y-m-d H:i:s', strtotime($request->tenggat_absensi)),
                'location'           => $request->lokasi,
                'host'               => $request->pimpinan_rapat,
                'participant'        => $request->peserta,
                'notulen_username'   => $request->notulen,
                'user_id'            => Auth::user()->id
            ]);
            if($data){
                return redirect()->route('mt.index')->with('msg','Absensi berhasil dibuat!');
            }else{
                return redirect()->route('mt.index')->with('msg','Absensi gagal dibuat!');
            }
        }else{
            $user            = User::select('username','name')
                                ->whereHas('roles', function($q){
                                    $q->where('role_id', "ST");
                                })
                                ->where('username','!=', 'admin')
                                ->orderBy('name')
                                ->get();
            return view('mt.index', compact('user'));
        }
            
    }

    public function data(Request $request)
    {
        $data = AttendanceActivity::leftJoin('users', 'users.username', '=', 'attendance_activities.notulen_username')
        ->where('type','M')
        ->where('user_id', Auth::user()->id)->select('attendance_activities.*', "users.name as notulen")->orderByDesc("id");
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
                'judul'             => ['required'],
                'tanggal'           => ['required', 'date'],
                'tenggat_absensi'       => ['required', 'date'],
                'lokasi'            => ['required'],
                'pimpinan_rapat'    => ['required'],
                'peserta'           => ['required'],
            ]);
            $data = AttendanceActivity::findOrFail($id);
            $d = $data->update([ 
                'type'               => 'M',
                'title'              => $request->judul,
                'date'               => date('Y-m-d', strtotime($request->tanggal)),
                'expired'       => date('Y-m-d H:i:s', strtotime($request->tenggat_absensi)),
                'location'           => $request->lokasi,
                'host'               => $request->pimpinan_rapat,
                'participant'        => $request->peserta,
                'notulen_username'   => $request->notulen,
                'user_id'            => Auth::user()->id
            ]);
            if($d){
                return redirect()->route('mt.index')->with('msg','Data berhasil diubah!');
            }else{
                return redirect()->route('mt.index')->with('msg','Data gagal diubah!');
            }
        }
        $user            = User::select('username','name')->get();
        $data            = AttendanceActivity::findOrFail($id);
        if($data->user_id != Auth::user()->id){
            abort(403);
        } else {
            return view('mt.edit', compact('data','user'));
        }
    }

    public function delete(Request $request) 
    {
        $data = AttendanceActivity::find($request->id);
        $att = Attendance::where("activity_id", $request->id)->count();
        if($att != 0){
            return response()->json([
                'success' => false,
                'message' => 'Absensi ini sudah terpakai, mohon hapus dulu daftar pesertanya!'
            ]);
        } else {
            if($data && $data->user_id == Auth::user()->id){
                Log::warning(Auth::user()->name." delete AttendanceActivity #".$data->id.", ".$data->title);
                $data->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil dihapus!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak diizinkan untuk menghapus data ini!'
                ]);
            }
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
            $pdf = PDF::loadview('attendance.qr', compact('qr','data','link','tok'));
            return $pdf->stream("Attendance #".$data->id."-".$tok." - ".Carbon::parse($data->date)->translatedFormat('j F Y').".pdf");
            // return view('attendance.qr', compact('qr','data','link'));
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
            $al = Attendance::where('activity_id', $id)->with('user')->select('*')->get();
            $pdf = PDF::loadview('attendance.print', compact('qr','data','link','tok', 'al'));
            return $pdf->stream("Attendance #".$data->id."-".$tok." - ".Carbon::parse($data->date)->translatedFormat('j F Y').".pdf");
            // return view('attendance.print', compact('qr','data','link', 'tok', 'al'));
        }else{
            try {
                $id = Crypt::decrypt($idd);
            } catch (DecryptException $e) {
                return redirect()->route('mt.index');
            }
            $data = AttendanceActivity::findOrFail($id);
            return view('mt.list', compact('id','data'));
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

    public function list_delete(Request $request) 
    {
        $data = Attendance::find($request->id);
        if($data){
            Log::warning(Auth::user()->name." delete Attendance #".$data->id.", ".$data->username);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan untuk menghapus data ini!'
            ]);
        }
    }
}
