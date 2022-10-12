<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
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
        $data = AttendanceActivity::where('type','E')->with('user')->select('*')->orderByDesc("id");
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

}
