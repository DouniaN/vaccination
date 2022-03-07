<?php

namespace App\Http\Controllers;

use App\Citoyen_non_verified;

use Illuminate\Support\Facades\Validator;
use App\Exports\PostsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class CitoyenNonVerifiedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cit = Citoyen_non_verified::all();
        return view('admin.export')->with(compact("cit"));
    }
    public function export_exel(){
        return Excel::download(new PostsExport, 'posts.xlsx');

        // $cit = DB::table('citoyen_non_verifieds')->get()->toArray();
        // $cit_array[] = array('CIN', 'Nom', 'Prenom');
        // foreach($cit as $item)
        // {
        // $cit_array[] = array(
        // 'CIN'  => $item->cin,
        // 'Nom'   => $item->nom,
        // 'Prenom'   => $item->prenom
        // );
        // }
        // Excel::create('Cit Data', function($excel) use ($cit_array){
        // $excel->setTitle('Cit Data');
        // $excel->sheet('Cit Data', function($sheet) use ($cit_array){
        // $sheet->fromArray($cit_array, null, 'A1', false, false);
        // });
        // })->download('xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $communs=DB::select("SELECT * FROM `communs` ");
        return view('admin.Add_cit_no')->with(compact('communs'));
    }
    public function indexaa()
    {
        $id=$_GET["a"];
        $req = DB::select("SELECT * FROM `quartiers` where commun_id=".$id);
        return $req;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

        // public function validateRequest(){
        //     $cit= Validator::make($request->all(),[
        //         'cin' =>"required",
        //         "nom" =>"required",
        //         'prenom'=>'required',
        //         "nomAr" =>"required",
        //         'prenomAr'=>'required',
        //         'dateNaissance'=>'required',
        //         'sexe'=>'required',
        //         'adresse'=>'required',
        //         'tel'=>'required',
        //         'email'=>'required',
        //         'profession'=>'required',
        //         ]);
        //         return $cit;
        // }
    public function store(Request $request)
    {
        $cit= request()->validate([
            'cin' =>'required',
            'nom' =>'required',
             'prenom'=>'required',
             'nomAr' =>'required',
             'prenomAr'=>'required',
             'dateNaissance'=>'required',
             'sexe'=>'required',
             'adresse'=>'required',
             'tel'=>'required',
             'email'=>'required | email',
             'profession'=>'required',
             'quartier_id'=>'sometimes',
             'lieu_travail'=>'required',
             'maladie_chronique'=>'sometimes',
             'maladie_foie'=>'sometimes',
             'hypertention'=>'sometimes',
             'cancer'=>'sometimes',
             'enceinte'=>'sometimes',
             'sufisance_renal'=>'sometimes',
             'maladie_resperatoire'=>'sometimes',
             'sys_uminitaire'=>'sometimes',
             'vaccin_grippal'=>'required',
             'date_vaccGripe'=>'sometimes'
            ]);


       $Citoyen_non_verified=Citoyen_non_verified::create($cit);

        return redirect('add')->with("success","test");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Citoyen_non_verified  $citoyen_non_verified
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Citoyen_non_verified  $citoyen_non_verified
     * @return \Illuminate\Http\Response
     */
    public function edit(Citoyen_non_verified $citoyen_non_verified)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Citoyen_non_verified  $citoyen_non_verified
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Citoyen_non_verified $citoyen_non_verified)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Citoyen_non_verified  $citoyen_non_verified
     * @return \Illuminate\Http\Response
     */
    public function destroy(Citoyen_non_verified $citoyen_non_verified)
    {
        //
    }
}
