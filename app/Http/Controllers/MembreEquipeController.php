<?php

namespace App\Http\Controllers;

use App\Membre_equipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MembreEquipeController extends Controller
{
    public function index()
    {
        $membreequipes=DB::select("SELECT *,m.id AS 'idm' FROM `membre_equipes` m INNER JOIN contenirs c ON c.membre_equipe_id=m.id INNER JOIN unite_vaccinations u ON u.id=c.unite_vaccination_id INNER JOIN fonctions f ON f.id=m.`fonction`");
        $unitevac=DB::select("SELECT * FROM `unite_vaccinations`");
        $fonction=DB::select("SELECT * FROM `fonctions`");
        $station=DB::select("SELECT * FROM `unite_vaccinations` WHERE uniteParent IS null or uniteParent=''");
        return view('admin.membreEquipe')->with([
            'membreequipes'=>$membreequipes,
            'station'=>$station,
            'fonction'=>$fonction,
            'unitevac'=>$unitevac
        ]);
    }
    public function index2aa()
    {
        $id=$_GET["aa"];
        $req=DB::select("SELECT * FROM `unite_vaccinations` WHERE uniteParent=".$id);
        return $req;
    }
    public function store(Request $request)
    {
        if($request->input('unite')=='')
        {
            $id=time();
            if($request->input('newFunction')!=null){
                $ide=time();
                $function=new Fonction;
                $function->id=$ide;
                $function->libelleFonction=$request->input('newFunction');
                $function->save();
                $insertMembre=DB::insert("INSERT INTO `membre_equipes`(`id`,`nom`, `prenom`, `fonction`, `cin`, `tel`, `email`, `created_at`, `updated_at`) VALUES ('".$id."','".$request->input('nom')."','".$request->input('prenom')."','".$ide."','".$request->input('cin')."','".$request->input('tel')."','".$request->input('email')."','".now()."','".now()."')");
                $contenire=DB::insert("INSERT INTO `contenirs`(`unite_vaccination_id`, `membre_equipe_id`, `created_at`, `updated_at`) VALUES ('".$request->input('staion')."','".$id."','".now()."','".now()."')");

            }
            else{
                $insertMembre=DB::insert("INSERT INTO `membre_equipes`(`id`,`nom`, `prenom`, `fonction`, `cin`, `tel`, `email`, `created_at`, `updated_at`) VALUES ('".$id."','".$request->input('nom')."','".$request->input('prenom')."','".$request->input('fonction')."','".$request->input('cin')."','".$request->input('tel')."','".$request->input('email')."','".now()."','".now()."')");
                $contenire=DB::insert("INSERT INTO `contenirs`(`unite_vaccination_id`, `membre_equipe_id`, `created_at`, `updated_at`) VALUES ('".$request->input('staion')."','".$id."','".now()."','".now()."')");
            }
        }
        else
        {
            $id=time();
            if($request->input('newFunction')!=null){
                $ide=time();
                $function=new Fonction;
                $function->id=$ide;
                $function->libelleFonction=$request->input('newFunction');
                $function->save();

                //$insertMembre=DB::insert("INSERT INTO `membre_equipes`(`id`,`nom`, `prenom`, `fonction`, `cin`, `tel`, `email`, `created_at`, `updated_at`) VALUES ('".$id."','".$request->input('nom')."','".$request->input('prenom')."','".$ide."','".$request->input('cin')."','".$request->input('tel')."','".$request->input('email')."','".now()."','".now()."')");
                //$contenire=DB::insert("INSERT INTO `contenirs`(`unite_vaccination_id`, `membre_equipe_id`, `created_at`, `updated_at`) VALUES ('".$request->input('staion')."','".$id."','".now()."','".now()."')");
                $insertMembre=DB::insert("INSERT INTO `membre_equipes`(`id`,`nom`, `prenom`, `fonction`, `cin`, `tel`, `email`, `created_at`, `updated_at`) VALUES ('".$id."','".$request->input('nom')."','".$request->input('prenom')."','".$ide."','".$request->input('cin')."','".$request->input('tel')."','".$request->input('email')."','".now()."','".now()."')");
                $contenire=DB::insert("INSERT INTO `contenirs`(`unite_vaccination_id`, `membre_equipe_id`, `created_at`, `updated_at`) VALUES ('".$request->input('unite')."','".$id."','".now()."','".now()."')");

            }
            else{
                $insertMembre=DB::insert("INSERT INTO `membre_equipes`(`id`,`nom`, `prenom`, `fonction`, `cin`, `tel`, `email`, `created_at`, `updated_at`) VALUES ('".$id."','".$request->input('nom')."','".$request->input('prenom')."','".$request->input('fonction')."','".$request->input('cin')."','".$request->input('tel')."','".$request->input('email')."','".now()."','".now()."')");
                $contenire=DB::insert("INSERT INTO `contenirs`(`unite_vaccination_id`, `membre_equipe_id`, `created_at`, `updated_at`) VALUES ('".$request->input('unite')."','".$id."','".now()."','".now()."')");
            }






        }
        //$insertMembre=DB::insert("INSERT INTO `membre_equipes`(`nom`, `prenom`, `fonction`, `cin`, `tel`, `email`, `created_at`, `updated_at`) VALUES ('".$request->input('nom')."','".$request->input('prenom')."','".$request->input('fonction')."','".$request->input('cin')."','".$request->input('tel')."','".$request->input('email')."','".now()."','".now()."')");
        return redirect('membreEquipe');
    }
       public function show($id){
        return DB::select('select ne.*,f.libelleFonction from membre_equipes ne join contenirs co on co.membre_equipe_id=ne.id join fonctions f on f.id=ne.fonction where co.unite_vaccination_id='.$id);
    }
}
