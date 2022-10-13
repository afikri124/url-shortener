<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
                'type'          => 'M',
                'title'         => $request->title,
                'sub_title'     => $request->sub_title,
                'date'          => date('Y-m-d', strtotime($request->date)),
                'location'      => $request->location,
                'host'          => $request->host,
                'participant'   => $request->participant,
                'user_id'       => Auth::user()->id
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
}
