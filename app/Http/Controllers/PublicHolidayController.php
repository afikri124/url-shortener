<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;use Illuminate\Validation\Rule;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Role;
use App\Models\WhPublicHoliday;
use Auth;
use Yajra\DataTables\DataTables;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PublicHolidayController extends Controller
{
    //
    function index (Request $request){
        Carbon::setLocale('id');
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'nama'              => ['required'],
                'tanggal'           => ['required', 'date'],
            ]);
            $new = WhPublicHoliday::create([
                'date'=> $request->tanggal,
                'detail'=> $request->nama
            ]);
            if($new){
                return redirect()->route('setting_public_holiday')->with('msg','Hari Cuti/Libur ('.$request->nama.') pada tanggal '.$request->tanggal.' BERHASIL ditambahkan!');
            }
        }
        $tahun = WhPublicHoliday::select(DB::raw("DATE_FORMAT(date, '%Y') year"))->groupBy('year')->orderByDesc("year")->get();
        return view('setting.public_holiday.index', compact('tahun'));
    }

    public function data(Request $request)
    {
        $data = WhPublicHoliday::select('*', DB::raw("DATE_FORMAT(date, '%Y') year"))->orderByDesc("year")->orderBy("date");
            return Datatables::of($data)
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('select_tahun'))) {
                        $instance->whereYear("date", $request->get('select_tahun'));
                    }
                    if (!empty($request->get('select_bulan'))) {
                        $instance->whereMonth("date", $request->get('select_bulan'));
                    }
                    if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                               $search = $request->get('search');
                                   $w->orWhere('detail', 'LIKE', "%$search%")
                                   ->orWhere('date', 'LIKE', "%$search%");
                           });
                    }
                })
                ->addColumn('idd', function($x){
                    return Crypt::encrypt($x['id']);
                  })
                ->rawColumns(['idd'])
                ->make(true);
    }

    public function delete(Request $request) {
        $data = WhPublicHoliday::find($request->id);
        if($data){
            Log::warning(Auth::user()->username." deleted PublicHoliday #".$data->id.", detail : ".$data->detail.", date : ".$data->date);
            $data->delete();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil dihapus!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal dihapus!'
            ]);
        }
    }

    public function edit($idd, Request $request)
    {
        try {
            $id = Crypt::decrypt($idd);
        } catch (DecryptException $e) {
            return redirect()->route('setting_public_holiday');
        }
        if ($request->isMethod('post')) {
            $this->validate($request, [ 
                'nama'              => ['required'],
                'tanggal'           => ['required', 'date'],
            ]);
            WhPublicHoliday::where('id', $id)->update([
                'date'=> $request->tanggal,
                'detail'=> $request->nama
            ]);
            Log::info(Auth::user()->name." update public holiday #".$id.", ".$request->nama);
            return redirect()->route('setting_public_holiday', ['id'=>$idd])->with('msg','Cuti/Libur '.$request->tanggal.' diperbarui!');
        }
        $data = WhPublicHoliday::find($id);
        if($data == null){
            abort(403, "Access not allowed!");
        }
        return view('setting.public_holiday.edit', compact('data'));
    }
}


