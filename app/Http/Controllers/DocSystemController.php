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
use App\Models\DocSystem;
use Illuminate\Http\Request;

class DocSystemController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $activity = DocActivity::select('*')->get();
        $user = User::select('id AS user_id','name')->get();
        return view('doc.index', compact('activity','user'));
    }

    public function index_data(Request $request)
    {

    }

    public function activity(Request $request)
    {
        $user = User::select('id AS user_id','name')->get();
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
        $user = DocActivity::select('id','name')->where('user_id',Auth::user()->id)->get();
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
            ->where('doc_activities.user_id', Auth::user()->id)
            ->with('activity')->select('doc_categories.*', 'doc_activities.user_id')->orderByDesc("id");
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
}
