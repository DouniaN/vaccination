<?php

namespace App\Http\Controllers;

use Gate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

       if (Gate::allows('root')){

            return redirect('/register');
        }
        else if(Gate::allows('superAdmin')){
            $data=0;
            $d=0;
            $req = DB::select("SELECT * FROM `provinces`");
            $req3 = DB::select("SELECT * FROM `types_vaccins`");
            return view('superAdmin.index')->with([
                'req'=>$req,
                'req3'=>$req3,
                'data'=>$data,
                'd'=>$d
            ]);

        }
        else if (Gate::allows('admin')){
            $data=0;
            $d=0;
            $user    = auth()->user();
            $id_user = $user->id;

            $province_id   =DB::table('user_provinces')
                ->select('province_id')
                ->where('user_id',$id_user)
                ->get();

            $req2=DB::select("select * from communs where province_id='".$province_id[0]->province_id."'");
             $req2;
            $req = DB::select("SELECT * FROM `pachaliks` where province_id=".$province_id[0]->province_id);
            $req3 = DB::select("SELECT * FROM `types_vaccins`");
            
            return view('admin.index')->with([
                'req'=>$req,
                'req2'=>$req2,
                'req3'=>$req3,
                'data'=>$data,
                 'd'=>$d
            ]);
        }
        else if (Gate::allows('gouverneur')){
            $data=0;
            $req = DB::select("SELECT * FROM `provinces`");
            return view('admin.index')->with([
                'req'=>$req,
                'data'=>$data
            ]);

        }
        else if (Gate::allows('unite')){
            $data=0;
            $d=0;
            $req3 = DB::select("SELECT * FROM `types_vaccins`");
            return view('unite.index')->with([
                'req3'=>$req3,
                'data'=>$data,
                 'd'=>$d
            ]);

        }

    }
    public function drop_pachalik(){
        $id= $_GET["a"];

        $req= DB::select("select * from pachaliks  where province_id =".$id);
        $req2=DB::select("select * from communs where province_id='".$id."'");
        return ['req'=>$req,'req2'=>$req2];
    }

    public function drop_commandement(){
        $idP=$_GET['a'];
        // dd($idP);
        $req1=DB::select("select c.id,nomCommandement from commandements c inner join pachaliks p on p.id=c.pachalik_id where p.id='".$_GET['a']."'");
       return view('layouts.partials.dropcommandement')->with([
           'req1'=>$req1,
       ]);
    }
public function searcheWilaya1(Request $request){
       // return $request->input('');
        $requete="select DISTINCT(c.id),c.cin,c.nom,c.prenom,c.dateNaissance,c.tel from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs comm on comm.id=q.commun_id inner join commandements coum on coum.id=q.commandement_id inner join provinces p on p.id=comm.province_id inner join regions r on r.id=p.region_id inner join pachaliks pach on pach.id=coum.pachalik_id JOIN affecters aff ON aff.citoyen_id=c.id where c.id!=-1";
        
        if(request('cin')!=""){
            $requete.=" and c.cin='".request('cin')."'";
        }
        if(request('province')!=0){
            $requete.=" and p.id='".request('province')."'";
        }

        if(request('pachalik')!=0){
            $requete.=" and pach.id='".request('pachalik')."'";
        }

        if(request('Communs')!=0){
            $requete.=" and comm.id='".request('Communs')."'";
        }

        if(request('commandement')!=0){
            $requete.=" and coum.id='".request('commandement')."'";
        }

         if(request("trancheage")==1){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=24 and DATEDIFF(now(),dateNaissance)/365>=18";
        }
        if(request("trancheage")==2){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=34 and DATEDIFF(now(),dateNaissance)/365>=25";
        }
        if(request("trancheage")==3){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=44 and DATEDIFF(now(),dateNaissance)/365>=35";
        }
        if(request("trancheage")==4){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=54 and DATEDIFF(now(),dateNaissance)/365>=45";
        }
        if(request("trancheage")==5){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=59 and DATEDIFF(now(),dateNaissance)/365>=55";
        }
        if(request("trancheage")==6){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365>=60";
        }

        if(request("etapVacc")==1){
            $requete.=" and c.id not in (select citoyen_id from vacciners ) and c.id in (select citoyen_id from affecters)";
        } 
        if(request("etapVacc")==2){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='1ere_prise')";
        }
        if(request('etapVacc')==3){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='rappel')";
        }
        if(request('date_vac1')!="" && request('date_vac2')!="" ){
            $requete.=" and c.id in(select citoyen_id from vacciners where  vaccination='ok' and dateVaccination between '".request('date_vac1')."' and '".request('date_vac2')."')";
        }
        if(request('typeV')!=0){
            $requete.=" and c.id in(select citoyen_id from vacciners where type_vaccin_id=".request('typeV').")";
        }
        $req = DB::select("SELECT * FROM `provinces`");
        $req3 = DB::select("SELECT * FROM `types_vaccins`");
  $d=0;
  $data=DB::select($requete);
if(count($data)==0){
    $d=1;
}
        return view('layouts.partials.partialtableres')->with([
                  'data'=>$data,
                     'd'=>$d
               ]);
       /* return view('superAdmin.index')->with([
          //  'req'=>$req,
            //'req3'=>$req3,
            'data'=>$data,
            'd'=>$d
        ]);*/
    }

    public function searcheWilaya(Request $request){
       // return $request->input('');
        $requete="select DISTINCT(c.id),c.cin,c.nom,c.prenom,c.dateNaissance from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs comm on comm.id=q.commun_id inner join commandements coum on coum.id=q.commandement_id inner join provinces p on p.id=comm.province_id inner join regions r on r.id=p.region_id inner join pachaliks pach on pach.id=coum.pachalik_id where c.id!=-1";
        
        if($request->input('cin')!=""){
            $requete.=" and c.cin='".$request->input('cin')."'";
        }
        if($request->input('province')!=0){
            $requete.=" and p.id='".$request->input('province')."'";
        }

        if($request->input('pachalik')!=0){
            $requete.=" and pach.id='".$request->input('pachalik')."'";
        }

        if($request->input('Commun')!=0){
            $requete.=" and comm.id='".$request->input('Commun')."'";
        }

        if($request->input('commandement')!=0){
            $requete.=" and coum.id='".$request->input('commandement')."'";
        }

         if($request->input("trancheage")==1){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=24 and DATEDIFF(now(),dateNaissance)/365>=18";
        }
        if($request->input("trancheage")==2){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=34 and DATEDIFF(now(),dateNaissance)/365>=25";
        }
        if($request->input("trancheage")==3){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=44 and DATEDIFF(now(),dateNaissance)/365>=35";
        }
        if($request->input("trancheage")==4){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=54 and DATEDIFF(now(),dateNaissance)/365>=45";
        }
        if($request->input("trancheage")==5){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=59 and DATEDIFF(now(),dateNaissance)/365>=55";
        }
        if($request->input("trancheage")==6){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365>=60";
        }

        if($request->input("etapVacc")==1){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='1ere_prise')";
        }
        if($request->input('etapVacc')==2){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='rappel')";
        }
        if($request->input('date_vac1')!="" && $request->input('date_vac2')!="" ){
            $requete.=" and c.id in(select citoyen_id from vacciners where  vaccination='ok' and dateVaccination between '".$request->input('date_vac1')."' and '".$request->input('date_vac2')."')";
        }
        if($request->input('typeV')!=0){
            $requete.=" and c.id in(select citoyen_id from vacciners where type_vaccin_id=".$request->input('typeV').")";
        }
        $req = DB::select("SELECT * FROM `provinces`");
        $req3 = DB::select("SELECT * FROM `types_vaccins`");
  $d=0;
  $data=DB::select($requete);
if(count($data)==0){
    $d=1;
}
        
        return view('superAdmin.index')->with([
            'req'=>$req,
            'req3'=>$req3,
            'data'=>$data,
            'd'=>$d
        ]);
    }

    public function searcheProvince1(Request $request){

        $user    = auth()->user();
        $id_user = $user->id;

        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();
        $requete="select DISTINCT(c.id),c.cin,c.nom,c.prenom,c.dateNaissance,c.tel from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs comm on comm.id=q.commun_id inner join commandements coum on coum.id=q.commandement_id inner join provinces p on p.id=comm.province_id inner join regions r on r.id=p.region_id inner join pachaliks pach on pach.id=coum.pachalik_id INNER JOIN affecters aff ON aff.citoyen_id=c.id where c.id!=-1 and p.id=".$province_id[0]->province_id;

        if(request('cin')!=""){
            $requete.=" and c.cin='".request('cin')."'";
        }

        if(request('pachalik')!=0){
            $requete.=" and pach.id='".request('pachalik')."'";
        }

        if(request('Communs')!=0){
            $requete.=" and comm.id='".request('Communs')."'";
        }

        if(request('commandement')!=0){
            $requete.=" and coum.id='".request('commandement')."'";
        }

        if(request("trancheage")==1){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=24 and DATEDIFF(now(),dateNaissance)/365>=18";
        }
        if(request("trancheage")==2){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=34 and DATEDIFF(now(),dateNaissance)/365>=25";
        }
        if(request("trancheage")==3){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=44 and DATEDIFF(now(),dateNaissance)/365>=35";
        }
        if(request("trancheage")==4){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=54 and DATEDIFF(now(),dateNaissance)/365>=45";
        }
        if(request("trancheage")==5){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=59 and DATEDIFF(now(),dateNaissance)/365>=55";
        }
        if(request("trancheage")==6){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365>=60";
        }

         if(request("etapVacc")==1){
            $requete.=" and c.id not in (select citoyen_id from vacciners ) and c.id in (select citoyen_id from affecters)";
        } 
        if(request("etapVacc")==2){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='1ere_prise')";
        }
        if(request('etapVacc')==3){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='rappel')";
        }
        if(request('date_vac1')!="" && request('date_vac2')!="" ){
            $requete.=" and c.id in(select citoyen_id from vacciners where  vaccination='ok' and dateVaccination between '".request('date_vac1')."' and '".request('date_vac2')."')";
        }
        if(request('typeV')!=0){
            $requete.=" and c.id in(select citoyen_id from vacciners where type_vaccin_id=".request('typeV').")";
        }

        $req2=DB::select("select * from communs where province_id='".$province_id[0]->province_id."'");
        $req2;
       $req = DB::select("SELECT * FROM `pachaliks` where province_id=".$province_id[0]->province_id);
       $req3 = DB::select("SELECT * FROM `types_vaccins`");
        $data=DB::select($requete);
          $d=0;
if(count($data)==0){
    $d=1;
}
    return view('layouts.partials.partialtableres')->with([
                  'data'=>$data,
                     'd'=>$d
               ]);
     /*   return view('admin.index')->with([
            'req'=>$req,
            'req2'=>$req2,
            'req3'=>$req3,
            'data'=>$data,
            'd'=>$d
        ]);*/
    }
 public function searcheProvince(Request $request){

        $user    = auth()->user();
        $id_user = $user->id;

        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();
        $requete="select DISTINCT(c.id),c.cin,c.nom,c.prenom,c.dateNaissance from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs comm on comm.id=q.commun_id inner join commandements coum on coum.id=q.commandement_id inner join provinces p on p.id=comm.province_id inner join regions r on r.id=p.region_id inner join pachaliks pach on pach.id=coum.pachalik_id where c.id!=-1 and p.id=".$province_id[0]->province_id;

        if($request->input('cin')!=""){
            $requete.=" and c.cin='".$request->input('cin')."'";
        }

        if($request->input('pachalik')!=0){
            $requete.=" and pach.id='".$request->input('pachalik')."'";
        }

        if($request->input('Commun')!=0){
            $requete.=" and comm.id='".$request->input('Commun')."'";
        }

        if($request->input('commandement')!=0){
            $requete.=" and coum.id='".$request->input('commandement')."'";
        }

        if($request->input("trancheage")==1){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=24 and DATEDIFF(now(),dateNaissance)/365>=18";
        }
        if($request->input("trancheage")==2){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=34 and DATEDIFF(now(),dateNaissance)/365>=25";
        }
        if($request->input("trancheage")==3){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=44 and DATEDIFF(now(),dateNaissance)/365>=35";
        }
        if($request->input("trancheage")==4){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=54 and DATEDIFF(now(),dateNaissance)/365>=45";
        }
        if($request->input("trancheage")==5){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=59 and DATEDIFF(now(),dateNaissance)/365>=55";
        }
        if($request->input("trancheage")==6){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365>=60";
        }

        if($request->input("etapVacc")==1){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='1ere_prise')";
        }
        if($request->input('etapVacc')==2){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='rappel')";
        }
        if($request->input('date_vac1')!="" && $request->input('date_vac2')!="" ){
            $requete.=" and c.id in(select citoyen_id from vacciners where  vaccination='ok' and dateVaccination between '".$request->input('date_vac1')."' and '".$request->input('date_vac2')."')";
        }
        if($request->input('typeV')!=0){
            $requete.=" and c.id in(select citoyen_id from vacciners where type_vaccin_id=".$request->input('typeV').")";
        }

        $req2=DB::select("select * from communs where province_id='".$province_id[0]->province_id."'");
        $req2;
       $req = DB::select("SELECT * FROM `pachaliks` where province_id=".$province_id[0]->province_id);
       $req3 = DB::select("SELECT * FROM `types_vaccins`");
        $data=DB::select($requete);
          $d=0;
if(count($data)==0){
    $d=1;
}
        return view('admin.index')->with([
            'req'=>$req,
            'req2'=>$req2,
            'req3'=>$req3,
            'data'=>$data,
            'd'=>$d
        ]);
    }
    public function searcheUnite(Request $request){
        $user    = auth()->user();
        $id_user = $user->id;
        $unite_id   =DB::table('user_unites')
                   ->select('unite_vaccination_id')
                   ->where('user_id',$id_user)
                   ->get();
                   
        $requete="select DISTINCT(c.id),c.cin,c.nom,c.prenom,c.dateNaissance from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs comm on comm.id=q.commun_id inner join commandements coum on coum.id=q.commandement_id inner join provinces p on p.id=comm.province_id inner join regions r on r.id=p.region_id inner join pachaliks pach on pach.id=coum.pachalik_id inner join affecters aff on aff.citoyen_id = c.id where c.id!=-1 and aff.unite_vaccination_id=".$unite_id[0]->unite_vaccination_id;


        if($request->input('cin')!=""){
            $requete.=" and c.cin='".$request->input('cin')."'";
        }
         if($request->input("trancheage")==1){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=24 and DATEDIFF(now(),dateNaissance)/365>=18";
        }
        if($request->input("trancheage")==2){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=34 and DATEDIFF(now(),dateNaissance)/365>=25";
        }
        if($request->input("trancheage")==3){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=44 and DATEDIFF(now(),dateNaissance)/365>=35";
        }
        if($request->input("trancheage")==4){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=54 and DATEDIFF(now(),dateNaissance)/365>=45";
        }
        if($request->input("trancheage")==5){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=59 and DATEDIFF(now(),dateNaissance)/365>=55";
        }
        if($request->input("trancheage")==6){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365>=60";
        }

        if($request->input("etapVacc")==1){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='1ere_prise')";
        }
        if($request->input('etapVacc')==2){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='rappel')";
        }
        if($request->input('typeV')!=0){
            $requete.=" and c.id in(select citoyen_id from vacciners where type_vaccin_id=".$request->input('typeV').")";
        }
        if($request->input('date_vac1')!="" && $request->input('date_vac2')!="" ){
            $requete.=" and c.id in(select citoyen_id from vacciners where vaccination='ok' and dateVaccination between '".$request->input('date_vac1')."' and '".$request->input('date_vac2')."')";
        }
        $req3 = DB::select("SELECT * FROM `types_vaccins`");
        $d=0;
        $data=DB::select($requete);
if(count($data)==0){
    $d=1;
}
       
        return view('unite.index')->with([
            'req3'=>$req3,
            'data'=>$data,
            'd'=>$d
        ]);
    }
     public function searcheUnite1(Request $request){
        $user    = auth()->user();
        $id_user = $user->id;
        $unite_id   =DB::table('user_unites')
                   ->select('unite_vaccination_id')
                   ->where('user_id',$id_user)
                   ->get();
                   
        $requete="select DISTINCT(c.id),c.cin,c.nom,c.prenom,c.dateNaissance,c.tel from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs comm on comm.id=q.commun_id inner join commandements coum on coum.id=q.commandement_id inner join provinces p on p.id=comm.province_id inner join regions r on r.id=p.region_id inner join pachaliks pach on pach.id=coum.pachalik_id inner join affecters aff on aff.citoyen_id = c.id where c.id!=-1 and aff.unite_vaccination_id=".$unite_id[0]->unite_vaccination_id;


        if(request('cin')!=""){
            $requete.=" and c.cin='".request('cin')."'";
        }
         if(request("trancheage")==1){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=24 and DATEDIFF(now(),dateNaissance)/365>=18";
        }
        if(request("trancheage")==2){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=34 and DATEDIFF(now(),dateNaissance)/365>=25";
        }
        if(request("trancheage")==3){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=44 and DATEDIFF(now(),dateNaissance)/365>=35";
        }
        if(request("trancheage")==4){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=54 and DATEDIFF(now(),dateNaissance)/365>=45";
        }
        if(request("trancheage")==5){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365<=59 and DATEDIFF(now(),dateNaissance)/365>=55";
        }
        if(request("trancheage")==6){
            $requete.=" and DATEDIFF(now(),dateNaissance)/365>=60";
        }

        if(request("etapVacc")==1){
            $requete.=" and c.id not in (select citoyen_id from vacciners ) and c.id in (select citoyen_id from affecters)";
        } 
        if(request("etapVacc")==2){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='1ere_prise')";
        }
        if(request('etapVacc')==3){
            $requete.=" and c.id in(select citoyen_id from vacciners where etape='rappel')";
        }
        if(request('typeV')!=0){
            $requete.=" and c.id in(select citoyen_id from vacciners where type_vaccin_id=".request('typeV').")";
        }
        if(request('date_vac1')!="" && request('date_vac2')!="" ){
            $requete.=" and c.id in(select citoyen_id from vacciners where vaccination='ok' and dateVaccination between '".request('date_vac1')."' and '".request('date_vac2')."')";
        }
        $req3 = DB::select("SELECT * FROM `types_vaccins`");
        $d=0;
        $data=DB::select($requete);
if(count($data)==0){
    $d=1;
}
        return view('layouts.partials.partialtableres')->with([
                  'data'=>$data,
                     'd'=>$d
               ]);
       /* return view('unite.index')->with([
            'req3'=>$req3,
            'data'=>$data,
            'd'=>$d
        ]);*/
    }
}
