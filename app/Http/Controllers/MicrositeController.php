<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Microsite;
use App\Models\MicrositeLink;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use PDF;

class MicrositeController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, 
                [ 
                    'judul'=> ['required', 'string', 'max:191'],
                    'shortlink'=> ['required', 'string', 'max:191', Rule::unique('microsites')],
                    'bio' => ['required','string'],
                    'avatar' => ['required','mimes:jpg,jpeg,png','max:3000']
                ],
                [
                    'shortlink.unique' => 'Link ini sudah ada yang menggunakan, silahkan gunakan yang lain.',
                ]
            );
            $fileName = "";
            if(isset($request->avatar)){
                $ext = $request->avatar->extension();
                $name = str_replace(' ', '_', $request->avatar->getClientOriginalName());
                $fileName = Auth::user()->id.'_'.$name; 
                $folderName =  "storage/FILE/microsite/".Carbon::now()->format('Y/m');
                $path = public_path()."/".$folderName;
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true); //create folder
                }
                $upload = $request->avatar->move($path, $fileName); //upload image to folder
                if($upload){
                    $fileName=$folderName."/".$fileName;
                } else {
                    $fileName = "";
                }
            }
            $data = Microsite::create([
                'title' => $request->judul,
                'shortlink' => str_replace(" ","",$request->shortlink),
                'bio' => $request->bio,
                'avatar' => $fileName,
                'user_id' => Auth::user()->id
            ]);
            if($data){
                return redirect()->route('MICROSITE.edit',['id' => Crypt::encrypt($data->id)])->with('msg','Situs Mikro BERHASIL dibuat');
            }else{
                return redirect()->route('MICROSITE.index')->with('msg','Situs Mikro GAGAL dibuat!');
            }
        }else{
            $data = "";
            return view('microsite.index', compact('data'));
        }
            
    }

    public function data(Request $request)
    {
        $data = Microsite::where('user_id',Auth::user()->id)->with('user')->select('*')->orderByDesc("id");
        if(Auth::user()->hasRole('AD')){
            $data = Microsite::with('user')->select('*')->orderByDesc("id");
        }
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
                    ->addColumn('uid', function($x){
                        return Crypt::encrypt($x['user_id']);
                      })
                    ->rawColumns(['idd','uid'])
                    ->make(true);
    }

    public function edit($idd, Request $request) 
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('MICROSITE.index');
        }
        if ($request->isMethod('post')) {
            if($request->type_form == 'add'){
                $this->validate($request, 
                    [ 
                        'microsite_id'=> ['required'],
                        'tautan'=> ['required', 'url', 'max:191'],
                        'judul_tautan'=> ['required', 'string', 'max:191'],
                    ]
                );
                $data = MicrositeLink::create([
                    'title' => $request->judul_tautan,
                    'link' => $request->tautan,
                    'microsite_id' => $request->microsite_id,
                ]);
                if($data){
                    return redirect()->route('MICROSITE.edit',['id' => Crypt::encrypt($data->microsite_id)])
                    ->with('msg','Tautan BERHASIL ditambahkan!');
                }else{
                    return redirect()->route('MICROSITE.edit',['id' => Crypt::encrypt($data->microsite_id)])
                    ->with('msg','Tautan GAGAL ditambahkan!');
                }
            } else {
                $this->validate($request, 
                    [ 
                        'shortlink'=> ['required', 'string', 'max:191', Rule::unique('microsites')->ignore($id, 'id')],
                        'judul'=> ['required', 'string', 'max:191'],
                        'bio' => ['required','string'],
                    ],
                    [
                        'shortlink.unique' => 'Link ini sudah ada yang menggunakan, silahkan gunakan yang lain.',
                    ]
                );
                $data = Microsite::findOrFail($id);
                $fileName = $data->avatar;
                if(isset($request->avatar)){
                    $name = str_replace(' ', '_', $request->avatar->getClientOriginalName());
                    $fileName = Auth::user()->id.'_'.$name; 
                    $folderName =  "storage/FILE/microsite/".Carbon::now()->format('Y/m');
                    $path = public_path()."/".$folderName;
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0755, true); //create folder
                    }
                    $upload = $request->avatar->move($path, $fileName); //upload image to folder
                    if($upload){
                        $fileName=$folderName."/".$fileName;
                        if($data->avatar != null){
                            File::delete(public_path()."/".$data->avatar);
                        }
                    } else {
                        $fileName = $data->avatar;
                    }
                }
                $d = $data->update([ 
                    'title' => $request->judul,
                    'shortlink' => str_replace(" ","",$request->shortlink),
                    'avatar' => $fileName,
                    'bio' => $request->bio,
                ]);
                if($d){
                    return redirect()->route('MICROSITE.edit',['id' => Crypt::encrypt($data->id)])
                    ->with('msg','Situs Mikro berhasil diubah!');
                }else{
                    return redirect()->route('MICROSITE.edit',['id' => Crypt::encrypt($data->id)])
                    ->with('msg','Situs Mikro gagal diubah!');
                }
            }

        }
        
        $data = Microsite::findOrFail($id);
        if($data->user_id != Auth::user()->id){
            abort(403);
        } else {
            return view('microsite.edit', compact('data'));
        }
    }

    public function delete(Request $request) 
    {
        $data = Microsite::find($request->id);
        if($data && $data->user_id == Auth::user()->id){
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Situs Mikro berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan untuk menghapus data ini!'
            ]);
        }
    }

    public function links($id, Request $request)
    {
        $data = MicrositeLink::where('microsite_id',$id)->select('*')->orderBy("id");
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('title', 'LIKE', "%$search%");
                        }
                    })
                    ->make(true);
    }

    public function delete_link(Request $request) 
    {
        $data = MicrositeLink::find($request->id);
        if($data && $data->microsite_id == $request->microsite_id){
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Tautan berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan untuk menghapus Tautan ini!'
            ]);
        }
    }

    public function edit_link(Request $request) 
    {
        $data = MicrositeLink::find($request->id);
        if($data && $data->microsite_id == $request->microsite_id){
            $data->update([ 
                'link' => $request->link
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Tautan berhasil diedit!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan untuk mengedit Tautan ini!'
            ]);
        }
    }

    public function edit_title(Request $request) 
    {
        $data = MicrositeLink::find($request->id);
        if($data && $data->microsite_id == $request->microsite_id){
            $data->update([ 
                'title' => $request->title
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Tautan berhasil diedit!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan untuk mengedit Tautan ini!'
            ]);
        }
    }

    public function view($id, Request $request) 
    {
        $data = Microsite::where("shortlink",$id)->first();
        if($data){
            $links = MicrositeLink::where("microsite_id",$data->id)->get();
            return view('microsite.view', compact('data', 'links'));
        } else {
            abort(404);
        }
    }

    public function print($idd) {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('MICROSITE.index');
        }
        $x = Microsite::findOrFail($id);
        $tok = $x->shortlink;
            $data = Microsite::with('user')->findOrFail($id);
            $link = route('MICROSITE.view', ['id' => $tok] );
            $qr = "https://s.jgu.ac.id/qrcode?data=".$link;
            $pdf = PDF::loadview('microsite.qr', compact('qr','data','link','tok'))->setPaper('a4');
            return $pdf->stream("MICROSITE #".$data->id."-".$tok." - ".Carbon::now()->translatedFormat('j F Y').".pdf");
            // return view('microsite.qr', compact('qr','data','link','tok'));
    }
}
