<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class apiController extends Controller
{
   public function getVaccSemaine(Request $request){
        $idprov=$request->input('idprov');
        $tupevacc=$request->input('tupevacc');
        $data=[];
        
        if($idprov==0 && $tupevacc==0)
            $data=DB::select("SELECT count(*) as nbrVacc,dateVaccination as dateVacc FROM vacciners where etape='1ere_prise' group by dateVaccination order by dateVacc");
        else if($idprov==0 && $tupevacc!=0)
            $data=DB::select("SELECT count(*) as nbrVacc,dateVaccination as dateVacc FROM vacciners where etape='1ere_prise' and type_vaccin_id='$tupevacc' group by dateVaccination order by dateVacc");
        else if($idprov!=0 && $tupevacc==0)
            $data=DB::select("
                SELECT count(*) as nbrVacc,v.dateVaccination as dateVacc FROM vacciners v
                inner join citoyens c on c.id=v.citoyen_id
                inner join quartiers q on q.id=c.quartier_id
                inner join communs com on com.id=q.commun_id
                inner join provinces p on p.id=com.province_id 
                where p.id='1' and etape='1ere_prise'
                group by dateVaccination order by dateVacc
            ");
        else if($idprov!=0 && $tupevacc!=0)
            $data=DB::select("
                SELECT count(*) as nbrVacc,v.dateVaccination as dateVacc FROM vacciners v
                inner join citoyens c on c.id=v.citoyen_id
                inner join quartiers q on q.id=c.quartier_id
                inner join communs com on com.id=q.commun_id
                inner join provinces p on p.id=com.province_id 
                where p.id='$idprov' and etape='1ere_prise' and v.type_vaccin_id='$tupevacc'
                group by dateVaccination order by dateVacc
            ");
            
        return response()->json($data) ;
    } 
    public function getpourcentageparprov(){
        $data=DB::select("
            select p.id as idprov,p.nomProvince as prov,count(c.id) as pop  from provinces p 
            left join communs com on com.province_id=p.id 
            left join quartiers q on q.commun_id=com.id 
            left join citoyens c on c.quartier_id=q.id 
            group by p.id order by prov;
        ");
        $data2=DB::select(
            "
                select p.id as idprov,count(v.citoyen_id)as nbrVacc from provinces p 
                left join communs com on com.province_id=p.id 
                left join quartiers q on q.commun_id=com.id 
                left join citoyens c on c.quartier_id=q.id 
                left join vacciners v on v.citoyen_id=c.id 
                where v.etape='1ere_prise' group by p.id
            "
            );

        $datafinal=array();
        $idprov=0;
        $nbrPop=0;
        $nbrVacc=0;
        $prov="";
        foreach ($data as $item) {
            $idprov=$item->idprov;
            $nbrPop=$item->pop;
            $prov=$item->prov;
            foreach ($data2 as $item2) {
                if ($item->idprov == $item2->idprov) {
                    $nbrVacc=$item2->nbrVacc;
                    break;
                }
                else
                    $nbrVacc=0;
                    
            }
            array_push($datafinal, array(
                            "id"=>$idprov,
                            "pourcentage" =>( $nbrPop != 0) ? ceil(($nbrVacc / $nbrPop) * 100) : 0,
                            "prov" => $prov,
                            )
                        );
        }
        
        return $datafinal;
    } 
    public function getTopDashboard(Request $request){
        $id=$request->input('id');
        $data1=[];
        $data2=[];
        $data3=[];
        if($id==0){
            $data1=DB::select("select count(*) as nbr from citoyens");
            $data2=DB::select("select count(citoyen_id) as nbr from vacciners where etape='1ere_prise' and citoyen_id not in(select vac.citoyen_id from vacciners vac where vac.etape='rappel')");
            $data3=DB::select("select count(*) as nbr from vacciners where etape='rappel'");
        }else{
            $data1=DB::select("
                select count(*) as nbr from citoyens c
                inner join quartiers q on q.id=c.quartier_id
                inner join communs com on com.id=q.commun_id
                inner join provinces p on p.id=com.province_id 
                where p.id='$id'
            ");
            $data2=DB::select("
                select count(citoyen_id) as nbr from vacciners v 
                inner join citoyens c on c.id=v.citoyen_id
                inner join quartiers q on q.id=c.quartier_id
                inner join communs com on com.id=q.commun_id
                inner join provinces p on p.id=com.province_id 
                where p.id='$id' and etape='1ere_prise' and v.citoyen_id not in(select vac.citoyen_id from vacciners vac where vac.etape='rappel')
            ");
            $data3=DB::select("
                select count(*) as nbr from vacciners v
                inner join citoyens c on c.id=v.citoyen_id
                inner join quartiers q on q.id=c.quartier_id
                inner join communs com on com.id=q.commun_id
                inner join provinces p on p.id=com.province_id 
                where p.id='$id' and etape='rappel';
            ");
        }
        
        $data4=0;
        $data5=0;

        $datafinal=array([
            'nombrePopulation'=>$data1[0]->nbr,
            'prise1'=>$data2[0]->nbr,
            'prise2'=>$data3[0]->nbr,
            'stockR'=>$data4,
            'rdv'=>$data5
        ]);
        return $datafinal[0];
    }
    
    public function getDataMap(){
        $data=DB::select("select nomUnite,capacite,X,Y,id from unite_vaccinations");
        return response()->json($data) ;
    }
    
}
