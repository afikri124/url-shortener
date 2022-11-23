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
use Illuminate\Support\Facades\File;


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
        $data = AttendanceActivity::where('notulen_username', Auth::user()->username)
        ->whereNotNull('attendance_activities.notulen_username')->with('user')->select('*')->orderByDesc("date");
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
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'dokumen' => ['required', 'mimes:xlsx,xls,doc,docx,ppt,pptx,pdf,jpg,jpeg,png','max:10000'],
            ]);

            $type = "File";
            $dokName = "";
            if(isset($request->dokumen)){
                $ext = $request->dokumen->extension();
                if($ext == "jpg" || $ext == "jpeg" || $ext == "png"){
                    $type = "Image";
                }    
                $name = str_replace(' ', '_', $request->dokumen->getClientOriginalName());
                $dokName = Auth::user()->id.'_'.$name; 
                $folderName =  "MoM/".Carbon::now()->format('Y/m');
                $path = public_path()."/".$folderName;
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true); //create folder
                }
                $upload = $request->dokumen->move($path, $dokName); //upload image to folder
                if($upload){
                    $dokName=$folderName."/".$dokName;
                } else {
                    $dokName = "";
                }
            }
            
            $data = MomDoc::create([
                'activity_id' => $id,
                'type' => $type,
                'doc_path' => $dokName
            ]);
            if($data){
                return redirect()->route('mom.notetaker_id', ['id'=>$idd])->with('msg','Dokumen telah disimpan!');
            } else {
                return redirect()->route('mom.notetaker_id', ['id'=>$idd])->with('msg','Gagal menambahkan dokumen!');
            }
        }
        $activity   = AttendanceActivity::findOrFail($id);
        
        if(Auth::user()->username == 'admin'){
            $users      = User::whereHas('roles', function($q){
                                $q->where('role_id','ST');
                            })
                            ->get();
        } else {
            $users      = User::whereHas('roles', function($q){
                                $q->where('role_id','ST');
                            })->where('username','!=', 'admin')
                            ->get();
        }
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
        ->orderByDesc("mom_lists.id");
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
        if($request->_token==csrf_token() && $request->detail != null){
            $this->validate($request, [ 
                'activity_id'=> ['required'],
                'detail'=> ['required', 'max:65500'],
            ]);
            $data = MomList::create([
                'activity_id' => $request->activity_id,
                'detail' => $request->detail,
                'target' => $request->target
            ]);
            if($data){
                MomList::find($data->id)->pics()->attach($request->users);
                return response()->json([
                    'success' => true,
                   'message' => 'Berhasil ditambahkan!'
                ]);  
            }
        }
        return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan!'
            ]);
    }

    public function notetaker_edit(Request $request) 
    {
        if($request->_token==csrf_token() && $request->detail != null){
            $this->validate($request, [ 
                'detail'=> ['required', 'max:65500'],
            ]);
            $data = MomList::findOrFail($request->id);
            $d = $data->update([ 
                'detail' => $request->detail,
                'target' => $request->target
            ]);

            if($d){
                if($request->users != null){
                    $detach = MomList::find($request->id)->pics()->detach();
                    $attach =  MomList::find($request->id)->pics()->attach($request->users);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil diubah!'
                ]);  
            }
        }
        return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan!'
            ]);
    }

    public function notetaker_delete(Request $request) 
    {
        $data = MomList::find($request->id);
        if($data){
            Log::warning(Auth::user()->name." delete MomList #".$data->id.", activity: ".$data->actyvity_id);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil dihapus!'
            ]);
        }
        return response()->json([
                'success' => false,
                'message' => 'Gagal dihapus!'
            ]);
    }

    public function list_id(Request $request) {
        $data   = MomList::join('attendance_activities','attendance_activities.id','=','mom_lists.activity_id')
        ->where('attendance_activities.notulen_username', Auth::user()->username)
        ->select('mom_lists.*', 'attendance_activities.notulen_username')
        ->find($request->id);
        if($data){
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Diizinkan!'
            ]);
        }
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
        ->orderByDesc("activity_id");
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
        $images =  MomDoc::where('activity_id',$data->activity_id)->where('type', "Image")->inRandomOrder()->first();
        return view('mom.PIC_id', compact('data','images'));
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
        $activity =  AttendanceActivity::with('notulen')->findOrFail($id);
        $lists =  MomList::where('activity_id',$id)
            ->with(['pics' => function ($query) {
                $query->select('name');
            }])->get();
        $docs =  MomDoc::where('activity_id',$id)
        // ->where('type',"!=", "Image")
        ->get();
        $images =  MomDoc::where('activity_id',$id)->where('type', "Image")->inRandomOrder()->get();
        return view('mom.meeting_id', compact('activity','lists','docs', 'images'));
    }

    public function mom_docs(Request $request)
    {
        $data = MomDoc::where('activity_id', $request->activity_id)
                ->select('*')
                ->orderByDesc("type");
        return Datatables::of($data)->make(true);
    }

    public function mom_docs_delete(Request $request) {
        $data = MomDoc::find($request->id);
        if($data){
                if($data->doc_path != null){
                    File::delete(public_path()."/".$data->doc_path);
                }
                Log::warning(Auth::user()->name." menghapus dokumen #".$data->id.", Dok: ".$data->doc_path);
                $data->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil dihapus!'
                ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan menghapus data ini!'
            ]);
        }
    }

    
    public function notetaker_print($idd, Request $request) {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            abort(403, "Data tidak ditemukan!");
        }
        $data =  AttendanceActivity::with('notulen')->findOrFail($id);
        $lists =  MomList::where('activity_id',$id)
            ->with(['pics' => function ($query) {
                $query->select('name','front_title','back_title');
            }])->get();
        $docs =  MomDoc::where('activity_id',$id)
                ->where('type',"!=", "Image")
                ->get();
        $images =  MomDoc::where('activity_id',$id)->where('type', "Image")->inRandomOrder()->get();

        $link = route('mom.note-taker_print', ['id' => $idd] );
        $qr = "https://s.jgu.ac.id/qrcode?data=".$link;

        // return view('mom.print', compact('qr','link', 'data', 'lists', 'docs', 'images'));
        $pdf = PDF::loadView('mom.print', compact('qr','link', 'data', 'lists', 'docs', 'images'))->setPaper('a4', 'landscape')->set_option("enable_php", true);
        return $pdf->stream("MoM #".$data->id." - ".Carbon::parse($data->date)->translatedFormat('j F Y').".pdf");
    }
}
