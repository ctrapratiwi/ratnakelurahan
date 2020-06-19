<?php

namespace App\Http\Controllers;

use DB;
use App\Pasien;
use App\Data;
use App\Kecamatan;
use App\Kelurahan;
use App\Kabupaten;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    private $dateTimeNow;
    private $dateNow;
    private $dateFormatName;
    public function __construct(){

        $this->dateTimeNow = Carbon::now()->addHours(8);
        $this->dateNow = Carbon::now()->format('Y-m-d');
        $this->dateFormatName = Carbon::now()->locale('id')->isoFormat('LL');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tanggalSekarang = $this->dateFormatName;
        $kabupaten = Kabupaten::get();
        $data1 = Data::select('updated_at')->get();

        $kelurahanBelumUpdate = Kelurahan::whereDoesntHave('data', function($query){
            $query->where('tanggal','=',$this->dateNow)->where('status','=',1);
        })->get();


        return view('Pasien.index', compact("kabupaten","kelurahanBelumUpdate","tanggalSekarang"));
    }

    public function getKecamatan(Request $request){
        return Kecamatan::where('id_kabupaten',$request->id_kabupaten)->get();
    }

    public function getKelurahan(Request $request){
        return Kelurahan::where('id_kecamatan',$request->id_kecamatan)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cek = Data::where('id_kelurahan',$request->kelurahan)->where('tanggal',$request->tanggal)->count();
        if($cek == 0){
            $data = new Data();
        }else{
            $data = Data::where('id_kelurahan',$request->kelurahan)->where('tanggal',$request->tanggal)->first();
            $data->status = 1;
        }

        $data->id_kelurahan = $request->kelurahan;
        $data->ppln = $request->ppln;
        $data->ppdn = $request->ppdn;
        $data->tl = $request->tl;
        $data->lainnya = $request->lainnya;

        $data->sembuh = $request->sembuh;
        $data->meninggal = $request->meninggal;
        $data->perawatan = $request->perawatan;
        $data->tanggal = $request->tanggal;
        $data->total = $request->sembuh + $request->perawatan + $request->meninggal;
        if($cek == 0){
            $data->save();
        }else{
            $data->update();
        }
        return redirect('/pasien')->with('alert','Data Berhasil di Update!');
        return $request;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function show(Pasien $pasien)
    {
        $pasien = Pasien::select('tb_pasien.id','kabupaten','positif','rawat','sembuh','meninggal')
                ->join('tb_kabupaten','tb_Pasien.id_kabupaten','=','tb_kabupaten.id')
                ->where('tb_Pasien.id_kabupaten','=',$pasien)
                ->get();

        return view('detail',compact('pasien'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function edit(Pasien $pasien)
    {
        return view('edit',compact('pasien'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pasien $pasien)
    {
        // $pasien->update($request->all());
        // $test = $request->id;
        $rawat= $request->rawat;
        $sembuh= $request->sembuh;
        $meninggal= $request->meninggal;
        $positif = $rawat+$sembuh+$meninggal;

        $pasien->sembuh = $request->sembuh;
        $pasien->rawat= $request->rawat;
        $pasien->positif= $positif;
        $pasien->meninggal= $request->meninggal;
        $pasien->save();

        return redirect('/pasien');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pasien $pasien)
    {
        $pasien->delete();
        return redirect('/pasien')->with('alert-success','Pasien berhasil dihapus!');
    }
}
