<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class api_portailController extends Controller
{
    //
    public function getnombreparage(){
        $dataR=DB::select("
                            SELECT count(*) as nbrVacc,ceil(DATEDIFF(v.dateVaccination,c.dateNaissance)/360) as age from vacciners v 
                            inner join citoyens c on c.id=v.citoyen_id
                            inner join quartiers q on q.id=c.quartier_id
                            inner join communs com on com.id=q.commun_id
                            inner join provinces p on p.id=com.province_id
                            inner join regions r on r.id=p.region_id
                            group by age
                        ");

           return response()->json($dataR) ;
     
    }
    
    public function map(){
        $data=DB::select("select nomUnite,capacite,X,Y,id from unite_vaccinations");
        return response()->json($data) ;
    }
    
   public function getpourcentageparprov(){
        $data=DB::select("select p.id,p.nomProvince as prov,count(c.id) as nbrVacc  from provinces p left join communs com on com.province_id=p.id left join quartiers q on q.commun_id=com.id left join citoyens c on c.quartier_id=q.id group by p.id");

        $data2=DB::select("select p.id,count(v.citoyen_id) as nbrVacc  from provinces p left join communs com on com.province_id=p.id left join quartiers q on q.commun_id=com.id left join citoyens c on c.quartier_id=q.id left join vacciners v on v.citoyen_id=c.id group by p.id");

        $datafinal=array();

   
       
            foreach ($data2 as $item2) {

                foreach ($data as $item) {
                  
                        if ($item->id == $item2->id) {
                            array_push($datafinal, array(
                                "nbrVacc" =>( $item->nbrVacc != 0) ? ceil(($item2->nbrVacc / $item->nbrVacc) * 100) : 0,
                                "prov" => $item->prov,
                            )
                            );
                         
                        }
                  
                }
            }


        return $datafinal;
    } 
  
  public function getnombreparprov(){
      $data=DB::select("select p.nomProvince as prov,count(v.citoyen_id) as nbrVacc from provinces p left join communs com on com.province_id=p.id left join quartiers q on q.commun_id=com.id left join citoyens c on c.quartier_id=q.id left join vacciners v on v.citoyen_id=c.id group by p.id");
      return $data;
  }
  
  public function getvaccsemaine(){
        
        $data=DB::select("SELECT count(*) as nbrVacc,dateVaccination as dateVacc FROM vacciners where etape='1ere_prise' group by dateVaccination order by dateVacc");
            
        return response()->json($data) ;
    } 
}
