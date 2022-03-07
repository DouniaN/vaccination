<?php

namespace App\Http\Controllers;

use App\Unite_vaccination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniteVaccinationController extends Controller
{
    public function index()
    {
        $unite_vaccinations=DB::select("SELECT q.nomQuartier,u.* FROM `unite_vaccinations` u LEFT JOIN quartiers q on q.id=u.`quartier_id` WHERE u.`type_unite`='Centre de santé'");
        //  WHERE p.id=".$idd
        $province=DB::select("SELECT * FROM `provinces`");
        $communs=DB::select("SELECT * FROM `communs` c INNER JOIN provinces p ON p.id=c.`province_id`");
        return view('admin.uniteVaccination')->with([
            'unite_vaccinations'=>$unite_vaccinations,
            'communs'=>$communs,
            'province'=>$province
        ]);
    }
    public function indexaaa()
    {
        $id=$_GET["a"];
        $req = DB::select("SELECT * FROM communs where province_id=".$id);
        return $req;
    }
    public function indexquartier()
    {
        $idP=$_GET['a'];
        //dd($idP);
        $req1=DB::select("SELECT * FROM `quartiers` where commun_id=".$idP);
        //dd($req1);
       return view('admin.quartierr')->with([
           'req1'=>$req1
       ]);
    }
    public function indexaa()
    {
        $id=$_GET["a"];
        $req = DB::select("SELECT * FROM `quartiers` where commun_id=".$id);
        return $req;
    }
    
    public function index2aa()
    {
        $id=$_GET["a"];
        $req = DB::select("SELECT * FROM `quartiers` where commun_id=".$id);
        return $req;
    }
    public function store(Request $request)
    {
        $id=time();
        $requete=DB::insert("INSERT INTO `unite_vaccinations`(`id`,`nomUnite`, `adresse`, `categorie`, `capacite`, `type_unite`,`type_construction`,`capacite_refrigerateur`,`quartier_id`,`X`,`Y`, `created_at`, `updated_at`) VALUES ('".$id."','".$request->input('nomUnite')."','".$request->input('adresse')."','".$request->input('categorie')."','".$request->input('capacite')."','Centre de santé','".$request->input('type_construction')."','".$request->input('capacite_refrigerateur')."','".$request->input('id_quartier')."','".$request->input('x')."','".$request->input('y')."','".now()."','".now()."')");
        return redirect('uniteVaccination');
    }
    public function destroy($id)
    {
        $delete=DB::delete("DELETE FROM `unite_vaccinations` WHERE id='".$id."'");
        $unite_vaccinations=DB::select("SELECT * FROM `unite_vaccinations`");
        return view('admin.uniteVaccination')->with([
            'delete'=>$delete,
            'unite_vaccinations'=>$unite_vaccinations
        ]);
    }
    public function index2()
    {
        $UniteProximite=DB::select("SELECT q.nomQuartier,u.* FROM `unite_vaccinations` u left JOIN quartiers q on q.id=u.`quartier_id` WHERE u.`type_unite`='UniteProximite'");
        $station=DB::select("SELECT * FROM `unite_vaccinations` WHERE `type_unite`='Centre de santé'");
        //  WHERE p.id=".$idd
        $province=DB::select("SELECT * FROM `provinces`");
        $communs=DB::select("SELECT * FROM `communs` c INNER JOIN provinces p ON p.id=c.`province_id`");
        return view('admin.UniteProximite')->with([
            'UniteProximite'=>$UniteProximite,
            'station'=>$station,
            'communs'=>$communs,
            'province'=>$province
        ]);
    }
    public function store2(Request $request)
    {
        $id=time();
        $requete=DB::insert("INSERT INTO `unite_vaccinations`(`id`,`nomUnite`, `adresse`, `categorie`, `capacite`, `type_unite`,`type_construction`,`capacite_refrigerateur`,`quartier_id`,`X`,`Y`,`uniteParent`, `created_at`, `updated_at`) VALUES ('".$id."','".$request->input('nomUnite')."','".$request->input('adresse')."','".$request->input('categorie')."','".$request->input('capacite')."','UniteProximite','".$request->input('type_construction')."','".$request->input('capacite_refrigerateur')."','".$request->input('id_quartier')."','".$request->input('x')."','".$request->input('y')."','".$request->input('staion')."','".now()."','".now()."')");
        return redirect('UniteProximite');
    }
    public function find($id) {
        $unites=DB::select('select u.* from unite_vaccinations u join quartiers q on q.id=u.quartier_id join communs co on co.id=q.commun_id join provinces p on p.id=co.province_id where co.province_id="'.$id.'"');
        return $unites;
    }
}
