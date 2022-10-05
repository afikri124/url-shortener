<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Data;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class DataController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            echo "Dalam Development";
        }else{
            $data = "";
            return view('data.index', compact('data'));
        }
            
    }

    public function data(Request $request)
    {
        $data = Data::where("user_id", Auth::user()->id)
            ->select('*')->orderByDesc("id");
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
                    ->rawColumns(['idd','uid'])
                    ->make(true);
    }

    public function hapus(Request $request) {
        $data = Data::find($request->id);
        if($data){
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan menghapus data ini!'
            ]);
        }
    }

}
