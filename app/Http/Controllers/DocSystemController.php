<?php

namespace App\Http\Controllers;
use Auth;
use PDF;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\DocActivity;
use App\Models\DocCategory;
use App\Models\DocDepartment;
use App\Models\DocPIC;
use App\Models\DocStatus;
use App\Models\DocSystem;
use Illuminate\Http\Request;
use App\Mail\MailNotification;
use Mail;
use DB;

class DocSystemController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, 
                [ 
                    'nama'=> ['required', 'string', 'max:191'],
                    'link'=> ['required', 'url', 'max:191'],
                    'kategori'=> ['required'],
                ],
            );
            $hst = "- ".Carbon::now()->format('d/m/Y, H:i ')."<b>".Auth::user()->name."</b> membuat kriteria dokumen yg dibutuhkan.<br>";
            if($request->catatan != null){
                $hst = $hst."<blockquote>".$request->catatan."</blockquote><br>";
            }
            $data = DocSystem::create([
                'name' => $request->nama,
                'deadline' => ($request->batas_waktu != null ? date('Y-m-d H:i:s', strtotime($request->batas_waktu)) : null),
                'doc_path' => $request->link,
                'status_id' => "S1",
                'category_id' => $request->kategori,
                'remark' => ($request->catatan == null ? "-":$request->catatan),
                'created_id' => Auth::user()->id,
                'histories' => $hst,
            ]);
            if($data){
                return redirect()->route('DOC.edit', Crypt::encrypt($data->id))->with('msg','Kriteria Dokumen berhasil ditambahkan');
            }else{
                return redirect()->route('DOC.index')->with('msg','Gagal ditambahkan!');
            }
        }
        $activity = DocActivity::select('*')->get();
        $category = DocCategory::select('*')->get();
        $status = DocStatus::select('*')->get();
        $user = User::whereHas('roles', function($q){
            $q->where('role_id', "ST");
        })->where('id', '!=', 1)
        ->select('id','name')->orderBy('name')->get();
        return view('doc.index', compact('activity', 'category', 'status', 'user'));
    }

    public function index_data(Request $request)
    {
        $data = DocSystem::join('doc_categories','doc_categories.id','=','doc_systems.category_id')
                    ->with('status')
                    ->with('PIC')
                    ->with(['PIC.user' => function ($query) {
                        $query->select('id', 'email', 'name');
                    }])
                    ->with('PIC.department')
                    ->select('doc_systems.*', 'doc_categories.activity_id')->orderBy("category_id");
        return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('activity_id'))) {
                        $instance->where('activity_id', $request->get('activity_id'));
                    }
                    if (!empty($request->get('category_id'))) {
                        $instance->where('category_id', $request->get('category_id'));
                    }
                    if (!empty($request->get('status_id'))) {
                        $instance->where('status_id', $request->get('status_id'));
                    }
                    if (!empty($request->get('pic_id'))) {
                        $instance->whereHas('PIC', function($q) use($request){
                            $q->where('pic_id', $request->get('pic_id'));
                        });
                    }
                    if (!empty($request->get('search'))) {
                        $search = $request->get('search');
                        $instance->where('doc_systems.name', 'LIKE', "%$search%");
                    }
                })
                ->addColumn('idd', function($x){
                    return Crypt::encrypt($x['id']);
                })
                ->rawColumns(['idd'])
                ->make(true);
    }

    public function index_delete(Request $request) {
        $data = DocSystem::find($request->id);
        if($data){
            Log::warning(Auth::user()->username." deleted DocSystem #".$data->id.", name : ".$data->name);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal dihapus!'
            ]);
        }
    }

    public function index_view($idd, Request $request)
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            abort(404);
        }
        if ($request->isMethod('post')) {
            $data = DocSystem::findOrFail($id);
            if($request->action == "unggah"){
                $this->validate($request, 
                    [ 
                        'action'=> ['required']
                    ],
                );
                $hst = "- ".Carbon::now()->format('d/m/Y, H:i ')."<b>".Auth::user()->name."</b> sudah mengunggah Bukti.<br>";
                if($request->catatan != null){
                    $hst = $hst."--- <i>".$request->catatan."</i><br>";
                }
                $hst = $data->histories." ".$hst;
                $d = $data->update([ 
                    'remark' => ($request->catatan == null ? "-":$request->catatan),
                    'status_id' => "S2",
                    'histories' => $hst
                ]);
            } elseif($request->action == "batalkan"){
                $this->validate($request, 
                    [ 
                        'action'=> ['required'],
                        'catatan'=> ['required'],
                    ],
                );
                $hst = "- ".Carbon::now()->format('d/m/Y, H:i ')."<b>".Auth::user()->name."</b> membatalkan Bukti.<br>";
                if($request->catatan != null){
                    $hst = $hst."--- <i>".$request->catatan."</i><br>";
                }
                $hst = $data->histories." ".$hst;
                $d = $data->update([ 
                    'remark' => ($request->catatan == null ? "-":$request->catatan),
                    'status_id' => "S1",
                    'histories' => $hst
                ]);
            }
        }
        $data = DocSystem::with('status')->with('category')
                ->with('PIC')
                ->with('PIC.user')
                ->with('PIC.department')->find($id);
        return view('doc.index_view', compact('data'));
    }

    public function index_edit($idd, Request $request)
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            abort(404);
        }
        if ($request->isMethod('post')) {
            $data = DocSystem::findOrFail($id);
            if($request->action == "validasi"){
                $this->validate($request, 
                    [ 
                        'action'=> ['required']
                    ],
                );
                $hst = "- ".Carbon::now()->format('d/m/Y, H:i ')."<b>".Auth::user()->name."</b> sudah melakukan validasi Bukti.<br>";
                if($request->catatan != null){
                    $hst = $hst."--- <i>".$request->catatan."</i><br>";
                }
                $hst = $data->histories." ".$hst;
                $d = $data->update([ 
                    'remark' => ($request->catatan == null ? "-":$request->catatan),
                    'status_id' => "S4",
                    'histories' => $hst,
                    'updated_id' => Auth::user()->id
                ]);
            } elseif($request->action == "revisi"){
                $this->validate($request, 
                    [ 
                        'action'=> ['required'],
                        'catatan'=> ['required'],
                    ],
                );
                $hst = "- ".Carbon::now()->format('d/m/Y, H:i ')."<b>".Auth::user()->name."</b> meminta agar dilakukan revisi Bukti.<br>";
                if($request->catatan != null){
                    $hst = $hst."--- <i>".$request->catatan."</i><br>";
                }
                $hst = $data->histories." ".$hst;
                $d = $data->update([ 
                    'remark' => ($request->catatan == null ? "-":$request->catatan),
                    'status_id' => "S3",
                    'histories' => $hst
                ]);
                if($d){
                    //TODO : Kirim email revisi
                    $dataEmail = DocPIC::with('department')->with('user')->where('doc_id', $id)->get();
                    if($dataEmail){
                        $sendTo = array();
                        $ccTo = array();
                        $name = array();
                        foreach($dataEmail as $x){
                            array_push($sendTo, $x->department->email);
                            array_push($ccTo, $x->user->email);
                            array_push($name, $x->user->name_with_title);
                        } 
                        $s = array();
                        $s['name'] = implode(", ",$name);
                        $s['subject'] = "Revisi Dokumen bukti yang dibutuhkan";
                        $s['messages'] = "Mohon maaf, Dokumen yang anda unggah harus direvisi";
                        $s['item'] = [$data->name];
                        $s['catatan'] = "Catatan : <br><i>".$request->catatan."</i>";
                        \Mail::to($sendTo)->cc($ccTo)->queue(new \App\Mail\MailNotification($s));
                    }
                }
            } elseif($request->action == "tambah"){
                $this->validate($request, 
                    [ 
                        'action'=> ['required'],
                        'penanggung_jawab'=> ['required'],
                        'departemen'=> ['required'],
                    ],
                );

                $data = DocPIC::create([
                    'doc_id' => $id,
                    'department_id' => $request->departemen,
                    'pic_id' => $request->penanggung_jawab,
                ]);
                if($data){
                    return redirect()->route('DOC.edit', $idd)->with('msg','Penanggung Jawab berhasil ditambahkan');
                }
            }
        }
        $data = DocSystem::with('status')->with('category')
                ->with('PIC')
                ->with(['PIC.user' => function ($query) {
                    $query->select('id', 'email', 'name');
                }])
                ->with('PIC.department')->find($id);

        $department = DocDepartment::select('*')->get();
        $user = User::whereHas('roles', function($q){
            $q->where('role_id', "ST");
        })->select('id','name')->get();
        return view('doc.index_edit', compact('data','user','department'));
    }

    public function index_edit_data($idd, Request $request)
    {
        $data = DocPIC::where('doc_id', $idd)
                ->with(['user' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['department' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->select('*')->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            // $instance->where('name', 'LIKE', "%$search%");
                        }
                    })
                    ->addColumn('idd', function($x){
                        return Crypt::encrypt($x['id']);
                      })
                    ->rawColumns(['idd'])
                    ->make(true);
    }

    public function index_edit_delete(Request $request) {
        $data = DocPIC::find($request->id);
        if($data){
            Log::warning(Auth::user()->username." deleted DocPIC #".$data->id.", dept : ".$data->department_id);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Penanggung Jawab berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal dihapus!'
            ]);
        }
    }


    public function activity(Request $request)
    {
        $user = User::whereHas('roles', function($q){
                    $q->where('role_id', "ST");
                })->select('id AS user_id','name')->get();
        if ($request->isMethod('post')) {
            $this->validate($request, 
                [ 
                    'aktivitas'=> ['required', 'string', 'max:191'],
                ],
            );
            $data = DocActivity::create([
                'name' => $request->aktivitas,
                'user_id' => $request->penanggung_jawab
            ]);
            if($data){
                return redirect()->route('DOC.activity')->with('msg','Activitas berhasil ditambahkan');
            }else{
                return redirect()->route('DOC.activity')->with('msg','Activitas gagal ditambahkan!');
            }
        }else{
            return view('doc.activity', compact('user'));
        }
    }

    public function activity_data(Request $request)
    {
        $data = DocActivity::with(['user' => function ($query) {
                    $query->select('id', 'name');
                }])->select('*')->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('name', 'LIKE', "%$search%");
                        }
                    })
                    ->addColumn('idd', function($x){
                        return Crypt::encrypt($x['id']);
                      })
                    ->rawColumns(['idd'])
                    ->make(true);
    }

    public function activity_delete(Request $request) {
        $data = DocActivity::find($request->id);
        if($data){
            Log::warning(Auth::user()->username." deleted DocActivity #".$data->id.", name : ".$data->name);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas gagal dihapus!'
            ]);
        }
    }

    public function activity_id(Request $request) {
        $data   = DocActivity::find($request->id);
        if($data){
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ditemukan!'
            ]);
        }
    }

    public function activity_edit(Request $request) 
    {
        if($request->_token==csrf_token() && $request->id != null){
            $data = DocActivity::findOrFail($request->id);
            Log::warning(Auth::user()->username." updated DocActivity #".$data->id.", name : ".$data->name);
            $d = $data->update([ 
                'name' => $request->name,
                'user_id' => $request->user_id
            ]);
            if($d){
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

    public function dept(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, 
                [ 
                    'nama'=> ['required', 'string', 'max:191'],
                    'email'=> ['required', 'string', 'max:191'],
                ],
            );
            $data = DocDepartment::create([
                'name' => $request->nama,
                'email' => $request->email
            ]);
            if($data){
                return redirect()->route('DOC.dept')->with('msg','Departemen berhasil ditambahkan');
            }else{
                return redirect()->route('DOC.dept')->with('msg','Departemen gagal ditambahkan!');
            }
        }else{
            return view('doc.dept');
        }
    }

    public function dept_data(Request $request)
    {
        $data = DocDepartment::select('*')->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('name', 'LIKE', "%$search%");
                            $instance->orWhere('email', 'LIKE', "%$search%");
                        }
                    })
                    ->addColumn('idd', function($x){
                        return Crypt::encrypt($x['id']);
                      })
                    ->rawColumns(['idd'])
                    ->make(true);
    }

    public function dept_delete(Request $request) {
        $data = DocDepartment::find($request->id);
        if($data){
            Log::warning(Auth::user()->username." deleted Doc Department #".$data->email.", name : ".$data->name);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas gagal dihapus!'
            ]);
        }
    }

    public function dept_id(Request $request) {
        $data   = DocDepartment::find($request->id);
        if($data){
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ditemukan!'
            ]);
        }
    }

    public function dept_edit(Request $request) 
    {
        if($request->_token==csrf_token() && $request->id != null){
            $data = DocDepartment::findOrFail($request->id);
            Log::warning(Auth::user()->username." updated Doc Department #".$data->email.", name : ".$data->name);
            $d = $data->update([ 
                'name' => $request->name,
                'email' => $request->email
            ]);
            if($d){
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

    public function category(Request $request)
    {
        $user = DocActivity::select('id','name')->get();
        if(!Auth::user()->hasRole('AD')){
            $user->where('user_id',Auth::user()->id);
        }
        if ($request->isMethod('post')) {
            $this->validate($request, 
                [ 
                    'nama'=> ['required', 'string', 'max:191'],
                    'aktivitas'=> ['required'],
                ],
            );
            $data = DocCategory::create([
                'name' => $request->nama,
                'activity_id' => $request->aktivitas
            ]);
            if($data){
                return redirect()->route('DOC.category')->with('msg','Kategori berhasil ditambahkan');
            }else{
                return redirect()->route('DOC.category')->with('msg','Kategori gagal ditambahkan!');
            }
        }else{
            return view('doc.category', compact('user'));
        }
    }

    public function category_data(Request $request)
    {
        $data = DocCategory::join('doc_activities','doc_activities.id','=','doc_categories.activity_id')
            ->with('activity')->select('doc_categories.*', 'doc_activities.user_id')->orderByDesc("id");
            if(!Auth::user()->hasRole('AD')){
                $data->where('doc_activities.user_id', Auth::user()->id);
            }
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('name', 'LIKE', "%$search%");
                        }
                    })
                    ->addColumn('idd', function($x){
                        return Crypt::encrypt($x['id']);
                      })
                    ->rawColumns(['idd'])
                    ->make(true);
    }

    public function category_delete(Request $request) {
        $data = DocCategory::find($request->id);
        if($data){
            Log::warning(Auth::user()->username." deleted DocCategory #".$data->id.", name : ".$data->name);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori gagal dihapus!'
            ]);
        }
    }

    public function category_id(Request $request) {
        $data   = DocCategory::find($request->id);
        if($data){
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ditemukan!'
            ]);
        }
    }

    public function category_edit(Request $request) 
    {
        if($request->_token==csrf_token() && $request->id != null){
            $data = DocCategory::findOrFail($request->id);
            Log::warning(Auth::user()->username." updated DocCategory #".$data->id.", name : ".$data->name);
            $d = $data->update([ 
                'name' => $request->name,
                'activity_id' => $request->activity_id
            ]);
            if($d){
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

    public function BroadCastNotification(){
        $dataPIC = DocPIC::with('department')->with('user')
        ->select('doc_p_i_c_s.department_id','doc_p_i_c_s.pic_id', DB::raw("COUNT('doc_systems.status_id') as total"))
        ->join('doc_systems','doc_systems.id','=','doc_p_i_c_s.doc_id')
        ->where('status_id','=','S1')
        ->orWhere('status_id','=','S3')
        ->groupBy('department_id','pic_id')
        ->get();

        foreach($dataPIC as $d){
           $item = DocSystem::select('doc_systems.id','doc_systems.name', 'doc_systems.status_id', 'doc_statuses.name as status', 'doc_p_i_c_s.department_id', 'doc_p_i_c_s.pic_id')
            ->join('doc_p_i_c_s','doc_systems.id','=','doc_p_i_c_s.doc_id')
            ->join('doc_statuses','doc_systems.status_id','=','doc_statuses.id')
            ->where(function ($query) {
                $query->where('status_id','=','S1')
                    ->orWhere('status_id','=','S3');
            })
            ->where('department_id','=', $d->department_id)
            ->where('pic_id','=', $d->pic_id)
            ->groupBy('doc_systems.id','department_id','pic_id','doc_systems.name','doc_systems.status_id', 'doc_statuses.name')
            ->get();

            if($d->total > 0){
                $sendTo = $d->department->email;
                $ccTo = $d->user->email;
                $itemEmail = array();
                $actEmail = array();
                foreach($item as $x){
                    array_push($itemEmail, $x->name." <i><b>(".$x->status.")</b></i>");
                    $actDOC = DocSystem::select('doc_activities.name')
                    ->join('doc_categories','doc_systems.category_id','=','doc_categories.id')
                    ->join('doc_activities','doc_activities.id','=','doc_categories.activity_id')
                    ->find($x->id);
                    if(!in_array($actDOC->name, $actEmail, true)){
                        array_push($actEmail, $actDOC->name);
                    }
                }

                $aktivitas = implode(", ",$actEmail);
                $s = array();
                $s['name'] = $d->user->name_with_title;
                $s['subject'] = "Pengumpulan Dokumen ".$aktivitas;
                $s['messages'] = "Dalam rangka <b>".$aktivitas."</b>, Anda ditugaskan untuk mengunggah dokumen berikut:";
                $s['item'] = $itemEmail;
                $s['catatan'] = "Langkah mengunggah dokumen:<br><ol>"
                ."<li>Akses halaman <b><a href='".url('/DOC')."'>https://s.jgu.ac.id</a></b></li>"
                ."<li>Masuk menggunakan email penerima pemberitahuan ini / Akun SSO penanggung jawab.</li>"
                ."<li>Tekan menu <b>Dokumen > Unggah Bukti</b></li>"
                ."<li>Pilih dan tekan nama Dokumen yang diperlukan</li> "
                ."<li>Lalu tekan <b>Unggah Bukti Disini</b></li>"
                ."<li>Jika sudah, maka segera tekan <b>Sudah Unggah</b>.</li>"
                ."</ol>";
                Mail::to($sendTo)->cc($ccTo)->queue(new MailNotification($s));
            }
        }
        Log::info("DocSystem mengirim email broadcast!");
        // return response()->json($dataPIC);
    }
}
