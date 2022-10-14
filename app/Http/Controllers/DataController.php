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
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

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
            $this->validate($request, [ 
                'shortlink'=> ['required', 'string', 'max:191', Rule::unique('data')],
                'url' => ['required'],
            ]);

            $data = Data::create([
                'shortlink' => $request->shortlink,
                'url' => $request->url,
                'user_id' => Auth::user()->id
            ]);
            if($data){
                return redirect()->route('url.index')->with('msg','Data added successfully');
            }else{
                return redirect()->route('url.index')->with('msg','Data failed to add!');
            }
        }else{
            $data = "";
            return view('data.index', compact('data'));
        }
            
    }

    public function data(Request $request)
    {
        $data = Data::where('user_id',Auth::user()->id)->with('user')->select('*')->orderByDesc("id");
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

    public function delete(Request $request) 
    {
        $data = Data::find($request->id);
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

    public function edit($idd, Request $request) 
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('url.index');
        }
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'shortlink'=> ['required', 'string', 'max:191', Rule::unique('data')->ignore($id, 'id')],
                'url' => ['required'],
            ]);
            $data = Data::findOrFail($id);
            $d = $data->update([ 
                'shortlink' => $request->shortlink,
                'url' => $request->url,
            ]);
            if($d){
                return redirect()->route('url.index')->with('msg','Data changed successfully!');
            }else{
                return redirect()->route('url.index')->with('msg','Data failed to change!');
            }
        }
        
        $data = Data::findOrFail($id);
        if($data->user_id != Auth::user()->id){
            abort(403);
        } else {
            return view('data.edit', compact('data'));
        }
    }

}
