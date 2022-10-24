<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Role;
use Auth;
use Yajra\DataTables\DataTables;

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

    function edit(Request $request){
        $roles   = Role::get();
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'email'=> ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::user()->id, 'id')],
                'username'=> ['nullable', 'string', 'max:255', Rule::unique('users')->ignore(Auth::user()->id, 'id')],
                'job' => ['required', 'string'],
                'name' => ['required', 'string'],
            ]);
            User::where('id', Auth::user()->id)->update([
                'name'=> $request->name,
                'username' => $request->username,
                'front_title' => $request->front_title,
                'back_title' => $request->back_title,
                'job' => $request->job,
                'email'=> $request->email,
                'gender'=> $request->gender,
            ]);
            return redirect()->route('user.edit')->with('msg','Profile has been updated!');
        }
        $gender = ['M', 'F'];
        return view('user.edit', compact('roles','gender'));
    }

}
