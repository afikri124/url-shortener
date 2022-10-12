<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
                'shortlink'=> ['required', 'string', 'max:191', Rule::unique('data')],
                'url' => ['required'],
            ]);

            $data = Data::create([
                'shortlink' => $request->shortlink,
                'url' => $request->url,
                'user_id' => Auth::user()->id
            ]);
            if($data){
                return redirect()->route('att.index')->with('msg','Data added successfully');
            }else{
                return redirect()->route('att.index')->with('msg','Data failed to add!');
            }
        }else{
            $data = "";
            return view('att.index', compact('data'));
        }
            
    }

}
