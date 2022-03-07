<?php

namespace App\Http\Controllers;

use http\QueryString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;

class Qstock extends Controller
{
    public function AfficheQteStock()
    {
        $user    = auth()->user();
        $id_user = $user->id;

        $unite_id   =DB::table('user_unites')
            ->select('unite_vaccination_id')
            ->where('user_id',$id_user)
            ->get();


        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();
        $req1=DB::select("SELECT distinct u.id,u.nomUnite,u.capacite FROM unite_vaccinations u INNER JOIN quartiers q on q.id=u.quartier_id INNER JOIN communs c on c.id=q.commun_id where c.province_id='".$province_id[0]->province_id."'");
        $req2=DB::select("SELECT `id`,`unite_vaccination_id`,SUM(`qteLivree`) as 'livre' from appprovisionnements GROUP BY `id`,`unite_vaccination_id`");
        $req3=DB::select("SELECT u.id,u.nomUnite,SUM(s.qtePerdue) AS 'perdu' FROM unite_vaccinations u
                                INNER JOIN  stock_perdus s ON s.unite_id=u.id group by u.id,u.nomUnite");
        $req4=DB::select("SELECT u.id,v.unite_vaccination_id,u.nomUnite,COUNT(v.unite_vaccination_id) AS 'consommer' FROM unite_vaccinations u INNER JOIN vacciners v on v.unite_vaccination_id=u.id group by u.id,u.nomUnite,v.unite_vaccination_id");
//        $QteStock=DB::select("SELECT u.id,u.`nomUnite`,u.`capacite`,COUNT(v.id) AS 'consommer',SUM(sp.qtePerdue) as 'perdue',SUM(a.qteLivree) as 'livree' FROM unite_vaccinations u
//            INNER JOIN vacciners v on u.id=v.unite_vaccination_id
//            INNER JOIN stock_perdus sp ON u.id=sp.unite_id
//            INNER JOIN appprovisionnements a on u.id=a.unite_vaccination_id GROUP BY u.`id`,u.`nomUnite`,u.`capacite`");
        $vaccin=DB::select("SELECT * FROM `vaccins`");
        $stockProvince=DB::select("SELECT * FROM `stock_provices` where id='1'");
        $QteAffecter=DB::select("SELECT SUM(`qteLivree`)as 'QteAffecter' FROM `appprovisionnements` where id_Province='".$province_id[0]->province_id."'");
        ///////////////////////////////////////////
        $vcn=DB::select("SELECT id,nomVaccin from types_vaccins");
        $stockprovince=DB::select("SELECT tv.id,SUM(sp.quantite_enstock) as 'quantite_enstock',sp.vaccin_id from stock_provices sp INNER JOIN vaccins v on sp.vaccin_id=v.id INNER JOIN types_vaccins tv on v.type_vaccin_id=tv.id where sp.province_id='1' group by tv.id");
        $stockperdu=DB::select("SELECT SUM(ss.qtePerdue) as 'qtePerdue',ss.type_vaccin_id from stock_perdus ss INNER JOIN unite_vaccinations u ON ss.unite_id=u.id INNER JOIN quartiers q on u.quartier_id=q.id INNER JOIN communs c on c.id=q.commun_id INNER JOIN provinces p on p.id=c.province_id where p.id='1' GROUP BY ss.type_vaccin_id");
        $stockconsome=DB::select("SELECT COUNT(v.id) as 'idd',v.type_vaccin_id from vacciners v
                                    INNER JOIN unite_vaccinations u ON u.id=v.unite_vaccination_id
                                    INNER JOIN quartiers q on q.id=u.quartier_id
                                    INNER JOIN communs c ON c.id=q.commun_id
                                    INNER JOIN provinces p ON p.id=c.province_id
                                    WHERE p.id='".$province_id[0]->province_id."'
                                    GROUP BY v.type_vaccin_id");


        return view('admin.Gestion_Stock')->with([
//            'QteStock'=>$QteStock,
            'vaccin'=>$vaccin,
            'unite'=>$req1,
            'livre'=>$req2,
            'perdu'=>$req3,
            'consommer'=>$req4,
            'stockProvince'=>$stockProvince,
            'QteAffecter'=>$QteAffecter,
            ////////////////////////////////////////////////////
            'vcn'=>$vcn,
            'stockprovince'=>$stockprovince,
            'stockperdu'=>$stockperdu,
            'stockconsome'=>$stockconsome,

        ]);
    }
    public function affecter_vaccin_station_form($id){
        $user    = auth()->user();
        $id_user = $user->id;

        $unite_id   =DB::table('user_unites')
            ->select('unite_vaccination_id')
            ->where('user_id',$id_user)
            ->get();


        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();
        
        $vaccines=DB::select("SELECT DISTINCT s.vaccin_id,v.nom,v.type_vaccin_id,tv.nomVaccin FROM `vaccins` v  INNER JOIN stock_provices s on v.id=s.vaccin_id INNER JOIN types_vaccins tv ON tv.id=v.type_vaccin_id WHERE s.province_id='".$province_id[0]->province_id."' GROUP BY v.type_vaccin_id
");
        $livreee=DB::select("SELECT a.id,a.vaccin_id,v.type_vaccin_id ,SUM(a.qteLivree) as 'livre'
                        FROM appprovisionnements a
                        LEFT JOIN vaccins v on v.id=a.vaccin_id
                        LEFT JOIN types_vaccins tv ON tv.id=v.type_vaccin_id
                        where a.unite_vaccination_id='".$id."'
                        group by a.vaccin_id");
        $perduu=DB::select("SELECT sp.id,sp.type_vaccin_id,SUM(sp.qtePerdue) as 'perdu' FROM stock_perdus sp WHERE sp.unite_id='".$id."' group by sp.type_vaccin_id");
        $consome=DB::select("SELECT count(v.id) as 'cnsm',v.type_vaccin_id FROM vacciners v WHERE v.unite_vaccination_id='".$id."' group by v.type_vaccin_id");
        $capa=DB::select("SELECT capacite from unite_vaccinations where id='".$id."'");
        $idprovince=DB::select("SELECT c.province_id FROM `unite_vaccinations` u INNER JOIN quartiers q on q.id=u.`quartier_id` INNER JOIN communs c on c.id=q.commun_id where u.id='".$id."'");
        $typeVaccin=DB::select("SELECT MAX(v.id) as 'id',t.id as 'dd',t.nomVaccin FROM `types_vaccins` t INNER JOIN vaccins v on t.id=v.type_vaccin_id INNER JOIN stock_provices s on v.id=s.vaccin_id WHERE s.province_id='".$province_id[0]->province_id."' GROUP by t.id");
        return view('admin.Affecter_vaccin_station')->with([
            'idprovince'=>$idprovince,
            'idstation'=>$id,
            'typeVaccin'=>$typeVaccin,
            'vaccines'=>$vaccines,
            'livree'=>$livreee,
            'perduu'=>$perduu,
            'consome'=>$consome,
            'capa'=>$capa

        ]);
    }
    public function affecter_vaccin_station(Request $request){
        $req=DB::insert("INSERT INTO `appprovisionnements`(`dateLivraison`, `qteLivree`, `created_at`, `updated_at`, `id_Province`, `unite_vaccination_id`,`vaccin_id`) VALUES ('".now()."','".$request->input('qte_affecter')."','".now()."','".now()."','".$request->input('id_province')."','".$request->input('id_station')."','".$request->input('vaccin')."')");
        /*$qteRecue=DB::select("SELECT `qteRecue` FROM `vaccins` WHERE id='".$request->input('vaccin')."'");
        $qte_affecter=$request->input("qte_affecter");
        $qtenouveau=$qteRecue[0]->qteRecue-$qte_affecter;
        $req2=DB::update("UPDATE `vaccins` SET `qteRecue`='".$qtenouveau."',`updated_at`='".now()."' WHERE id='".$request->input('vaccin')."'");*/
        return redirect('Gestion_Stock')->with([
            'message'=>'ok',
        ]);
    }
    public function affecter_Vc(Request $request){

        return redirect('Gestion_Stock')->with([
            'message'=>'ok',
        ]);
    }
     public function form_Stock_Perdu(Request $request){
         
        $user    = auth()->user();
        $id_user = $user->id;

        $unite_id   =DB::table('user_unites')
            ->select('unite_vaccination_id')
            ->where('user_id',$id_user)
            ->get();


        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();
        $typeVaccin=DB::select("SELECT DISTINCT t.id,t.nomVaccin FROM `types_vaccins` t INNER JOIN vaccins v on t.id=v.type_vaccin_id INNER JOIN appprovisionnements a on v.id=a.vaccin_id WHERE a.unite_vaccination_id='".$unite_id[0]->unite_vaccination_id."'");
       return view('unite.Stock_Perdu')->with([
           'typeVaccin'=>$typeVaccin
       ]);
    }
    public function Stock_Perdu(Request $request){
        $user    = auth()->user();
        $id_user = $user->id;

        $unite_id   =DB::table('user_unites')
            ->select('unite_vaccination_id')
            ->where('user_id',$id_user)
            ->get();


        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();
        $req=DB::insert("INSERT INTO `stock_perdus`(`qtePerdue`, `dateQtePerdu`, `motif`, `created_at`, `updated_at`, `unite_id`,`type_vaccin_id`) VALUES ('".$request->input('quantitePerdue')."','".$request->input('dateDeclaration')."','".$request->input('motif')."','".now()."','".now()."','".$unite_id[0]->unite_vaccination_id."','".$request->input('typeVaccin')."')");
        return redirect('Stock_Perdu')->with([
            'message'=>'ok',
        ]);
    }
    public function declaration_Stock(){
       $typeVaccin=DB::select("SELECT * FROM `types_vaccins`");
        $vaccin=DB::select("SELECT * FROM `vaccins` v INNER JOIN types_vaccins t on v.`type_vaccin_id`=t.id");
        return view('superAdmin.Reception_du_stock')->with([
            'vaccin'=>$vaccin,
            'typeVaccin'=>$typeVaccin
        ]);
    }
       public function receptionVaccin(Request $request){
        if($request->input('typeVaccination')=='autre'){
            $reqq=DB::insert("INSERT INTO `types_vaccins`(`nomVaccin`, `nombreLots`, `created_at`, `updated_at`) VALUES ('".$request->input('nvtypeVaccin')."','".$request->input('NombreLots')."','".now()."','".now()."')");
            $id_typevc=DB::select("SELECT MAX(id) AS 'id' FROM `types_vaccins`");
            $reqqq=DB::insert("INSERT INTO `vaccins`(`qteRecue`, `date_recue`, `temperature_recep`, `created_at`, `updated_at`,`id_Region`, `type_vaccin_id`) VALUES ('".$request->input('quantiteRecu')."','".$request->input('dateReception')."','".$request->input('temperature')."','".now()."','".now()."','1','".$id_typevc[0]->id."')");
        }
        else{
            $req=DB::insert("INSERT INTO `vaccins`(`qteRecue`, `date_recue`, `temperature_recep`, `created_at`, `updated_at`,`id_Region`, `type_vaccin_id`) VALUES ('".$request->input('quantiteRecu')."','".$request->input('dateReception')."','".$request->input('temperature')."','".now()."','".now()."','1','".$request->input('typeVaccination')."')");
        }
        return redirect('declaration_Stock')->with([
            'message'=>'ok'
        ]);
    }
   public function Wilaya_Gestion_Stock()
    {
        $varreq2='';
        $varreq2id='';
        $varreq3ql='';
        $varreq3id='';
        $req1=DB::select("SELECT COUNT(v.citoyen_id) as 'population',p.nomProvince,p.id FROM provinces p LEFT JOIN communs c ON p.id=c.province_id LEFT JOIN quartiers q ON c.id=q.commun_id LEFT JOIN unite_vaccinations u ON q.id=u.quartier_id LEFT JOIN vacciners v ON u.id=v.unite_vaccination_id WHERE p.region_id='1' group by p.id");
        $req2=DB::select("SELECT SUM(quantite_enstock) as 'quantite_enstock',province_id FROM stock_provices GROUP BY province_id");
        $req3=DB::select("SELECT SUM(qteLivree) as 'qteLivree',id_Province FROM appprovisionnements GROUP BY id_Province");
        $req4=DB::select("SELECT SUM(qteRecue) as 'qteRecue' FROM vaccins where id_Region='1'");
        //$req2=DB::select("SELECT u.id,u.nomUnite,SUM(a.qteLivree) as 'livre' FROM unite_vaccinations u INNER JOIN appprovisionnements a ON a.unite_vaccination_id=u.id group by u.id,u.nomUnite");
        /*$req3=DB::select("SELECT u.id,u.nomUnite,SUM(s.qtePerdue) AS 'perdu' FROM unite_vaccinations u
                                INNER JOIN  stock_perdus s ON s.unite_id=u.id group by u.id,u.nomUnite");*/
        //$req4=DB::select("SELECT u.id,u.nomUnite,COUNT(v.unite_vaccination_id) AS 'consommer' FROM unite_vaccinations u INNER JOIN vacciners v on v.unite_vaccination_id=u.id group by u.id,u.nomUnite");
//        $QteStock=DB::select("SELECT u.id,u.`nomUnite`,u.`capacite`,COUNT(v.id) AS 'consommer',SUM(sp.qtePerdue) as 'perdue',SUM(a.qteLivree) as 'livree' FROM unite_vaccinations u
//            INNER JOIN vacciners v on u.id=v.unite_vaccination_id
//            INNER JOIN stock_perdus sp ON u.id=sp.unite_id
//            INNER JOIN appprovisionnements a on u.id=a.unite_vaccination_id GROUP BY u.`id`,u.`nomUnite`,u.`capacite`");
        $vaccin=DB::select("SELECT * FROM `vaccins`");
        $table[]=array('qts'=>'','prov'=>'');
        $table1[]=array('qtl'=>'','prov'=>'');
         
        for($i=0;$i<sizeof($req2);$i++)
        {
            $varreq2qs=$req2[0]->quantite_enstock;
            $varreq2id=$req2[0]->province_id;
            $qts=$req2[$i]->quantite_enstock;
            $prov=$req2[$i]->province_id;
            $table[]=array(
                "qts"=>$qts,
                "prov"=>$prov,
            );
        }for($i=0;$i<sizeof($req3);$i++)
    {
        $varreq3ql=$req3[0]->qteLivree;
        $varreq3id=$req3[0]->id_Province;
        $qts=$req3[$i]->qteLivree;
        $prov=$req3[$i]->id_Province;
        $table1[]=array(
            "qtl"=>$qts,
            "prov"=>$prov,
        );
    }
        $typeVaccin=DB::select("SELECT v.id,v.nom,t.nomVaccin,SUM(v.qteRecue) as 'QteReception' FROM `vaccins` v INNER JOIN types_vaccins t on v.`type_vaccin_id`=t.id GROUP BY `type_vaccin_id`");
        $conssomeprov=DB::select("SELECT vaccin_id,SUM(`quantite_enstock`) as 'QteConsomme' FROM `stock_provices` GROUP BY vaccin_id");
        // dd($table);
        return view('superAdmin.Wilaya_Gestion_Stock')->with([
//            'QteStockWilaya'=>$QteStockWilaya,
            'vaccin'=>$vaccin,
            'rokita'=>$req1,
            'quantite_enstock'=>$varreq2,
            'province_id'=>$varreq2id,
            'qteLivree'=>$varreq3ql,
            'id_Province'=>$varreq3id,
            'qteRecue'=>$req4,
            'table'=>$table,
            'table1'=>$table1,
            'typeVaccin'=>$typeVaccin,
            'conssomeprov'=>$conssomeprov
        ]);
    }

    public function affecter_Vc_Wilaya(Request $request){
        $req=DB::insert("INSERT INTO `stock_provices`(`province_id`, `vaccin_id`, `quantite_enstock`, `date_dispatching`, `created_at`, `updated_at`) VALUES ('".$request->input('id_province')."','".$request->input('type_vaccin')."','".$request->input('qte_affecter')."','".now()."','".now()."','".now()."')");
        return redirect('Wilaya_Gestion_Stock')->with([
            'message'=>'ok',
        ]);

    }
     public  function Stock_unite(){
         $user    = auth()->user();
        $id_user = $user->id;

        $unite_id   =DB::table('user_unites')
            ->select('unite_vaccination_id')
            ->where('user_id',$id_user)
            ->get();


        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();
        $req1=DB::select("SELECT distinct u.id,u.nomUnite,u.capacite FROM unite_vaccinations u INNER JOIN appprovisionnements a on u.id=a.unite_vaccination_id where u.id='1'");
        $req2=DB::select("SELECT `id`,`unite_vaccination_id`,SUM(`qteLivree`) as 'livre' from appprovisionnements GROUP BY `id`,`unite_vaccination_id`");
        $req3=DB::select("SELECT u.id,u.nomUnite,SUM(s.qtePerdue) AS 'perdu' FROM unite_vaccinations u
                                INNER JOIN  stock_perdus s ON s.unite_id=u.id group by u.id,u.nomUnite");
        $req4=DB::select("SELECT u.id,v.unite_vaccination_id,u.nomUnite,COUNT(v.unite_vaccination_id) AS 'consommer' FROM unite_vaccinations u INNER JOIN vacciners v on v.unite_vaccination_id=u.id group by u.id,u.nomUnite,v.unite_vaccination_id");
        ////////////////////////////////////////
        $vaccines=DB::select("SELECT id,nomVaccin from types_vaccins");
        $livreee=DB::select("SELECT a.id as 'ddd',t.id,a.vaccin_id,SUM(a.qteLivree) as 'livre' 
FROM appprovisionnements a 
INNER JOIN vaccins v ON a.vaccin_id=v.id
INNER JOIN types_vaccins t ON v.type_vaccin_id=t.id
WHERE a.unite_vaccination_id='1'
group by a.vaccin_id");
        $perduu=DB::select("SELECT sp.id,sp.type_vaccin_id,SUM(sp.qtePerdue) as 'perdu' FROM stock_perdus sp group by sp.type_vaccin_id");
        $consome=DB::select("SELECT count(v.id) as 'cnsm',v.type_vaccin_id FROM vacciners v  group by v.type_vaccin_id");
        return view('unite.Stock_unite')->with([
            'unite'=>$req1,
            'livre'=>$req2,
            'perdu'=>$req3,
            'consommer'=>$req4,
            ////////////////////////////////////
            'vaccines'=>$vaccines,
            'livree'=>$livreee,
            'perduu'=>$perduu,
            'consome'=>$consome,

        ]);
    }
  public function Wilaya_Gestion_Stock_form($id){
        $vaccin=DB::select("SELECT v.id,t.`nomVaccin` FROM `types_vaccins` t INNER JOIN vaccins v on t.id=v.type_vaccin_id GROUP BY t.id");
        $typeVaccin=DB::select("SELECT s.vaccin_id,t.`nomVaccin`,v.nom,SUM(s.quantite_enstock) as 'qteStock' from stock_provices s INNER JOIN vaccins v on s.vaccin_id=v.id INNER JOIN provinces p on s.province_id=p.id INNER JOIN types_vaccins t ON v.type_vaccin_id=t.id WHERE p.region_id='1' GROUP BY v.type_vaccin_id");
        $QteConsomme=DB::select("SELECT vaccin_id,SUM(qteLivree) as 'qteConsomme' from appprovisionnements WHERE id_province='".$id."' GROUP by `vaccin_id`");
        $QteStock=DB::select("SELECT SUM(quantite_enstock) as 'quantite_enstock',province_id FROM stock_provices WHERE province_id='".$id."' GROUP BY province_id");
        return view('superAdmin.affecter_Vaccin_Wilaya')->with([
            'id'=>$id,
            'QteStock'=>$QteStock,
            'typeVaccin'=>$typeVaccin,
            'QteConsomme'=>$QteConsomme,
            'vaccin'=>$vaccin,
        ]);
    }
}
