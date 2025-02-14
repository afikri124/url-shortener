<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repository;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PDF;

class RepositoryController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, 
                [ 
                    'nama'=> ['required', 'string', 'max:191'],
                    'status_publikasi' => ['required'],
                    'file_repositori' => ['required','mimes:jpg,jpeg,png,pdf','max:5000']
                ]
            );
            $type = "Pdf";
            $dokName = "";
            if(isset($request->file_repositori)){
                $ext = $request->file_repositori->extension();
                if($ext == "jpg" || $ext == "jpeg" || $ext == "png"){
                    $type = "Image";
                }    
                $name = str_replace(' ', '_', $request->file_repositori->getClientOriginalName());
                $dokName = Auth::user()->id.'_'.$name; 
                $folderName =  "storage/repository/".Carbon::now()->format('Y/m');
                $path = public_path()."/".$folderName;
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true); //create folder
                }
                $upload = $request->file_repositori->move($path, $dokName); //upload image to folder
                if($upload){
                    $dokName=$folderName."/".$dokName;
                } else {
                    $dokName = "";
                }
            }
            $data = Repository::create([
                'user_id' => Auth::user()->id,
                'file_path' => $dokName,
                'name' => $request->nama,
                'type' => $type,
                'published' => $request->status_publikasi,
            ]);
            if($data){
                return redirect()->route('REPOSITORY.index')->with('msg','File BERHASIL diunggah');
            }else{
                return redirect()->route('REPOSITORY.index')->with('msg','File GAGAL dibuat!');
            }
        }else{
            $data = "";
            return view('repository.index', compact('data'));
        }
    }

    public function data(Request $request)
    {
        $data = Repository::where('user_id',Auth::user()->id)
        ->orWhere('published', true)
        ->with('user')->select('*')->orderByDesc("created_at");
        if(Auth::user()->id == 1){
            $data = Repository::with('user')->select('*')->orderByDesc("created_at");
        }
            return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $search = $request->get('search');
                            $instance->where('name', 'LIKE', "%$search%")
                            ->orWhere('file_path', 'LIKE', "%$search%")
                            ->orWhereHas('user', function($query) use ($search) {
                                $query->where('name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->make(true);
    }

    public function delete(Request $request) 
    {
        $data = Repository::where('uid',$request->id)->first();
        if($data && $data->user_id == Auth::user()->id){
            if($data->file_path != null){
                File::delete(public_path()."/".$data->file_path);
            }
            Log::warning(Auth::user()->name." menghapus repository #".$data->name.", File: ".$data->file_path);
            Repository::where('uid',$request->id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan untuk menghapus data ini!'
            ]);
        }
    }

    public function repo($id, Request $request) 
    {
        $data = Repository::where("uid",$id)->first();
        if($data){
            if($data->type == "Image"){
                return '<img src="' . url($data->file_path) . '"/>';
            } else if($data->type == "Pdf") {
                return response()->file($data->file_path);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
}
