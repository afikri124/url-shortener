<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    //
    function profile (){
        return view('user.profile');
    }

    public function data(Request $request)
    {
        $data = Attendance::where("username", Auth::user()->username)
                ->with('activity')
                ->select('*')->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->whereHas('activity', function($q) use($request){
                                $search = $request->get('search');
                                $q->where('title', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->make(true);
    }

    function profile_by_id ($idd){
        try {
            $id = Crypt::decrypt($idd);
            $data = User::find($id);
        } catch (DecryptException $e) {
            return redirect()->route('user.profile');
        }
        if($data){
            return view('user.profile_by_id', compact('data'));
        } else {
            return redirect()->route('user.profile');
        }
    }

    public function data_by_id($username, Request $request)
    {
        $data = Attendance::where("username", $username)
                ->with('activity')
                ->select('*')->orderByDesc("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->whereHas('activity', function($q) use($request){
                                $search = $request->get('search');
                                $q->where('title', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->make(true);
    }

}
