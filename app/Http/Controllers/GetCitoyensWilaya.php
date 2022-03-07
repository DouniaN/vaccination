<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Citoyen;
use App\Unite_vaccination;
use App\Vaccin;
use App\Vacciner;
use App\Affecter;
use DateTime;
use Illuminate\Support\Facades\DB;

class GetCitoyensWilaya extends Controller
{
    public function index(){

     /*Get DateTime Now */
      $today   =new Datetime();

    $records=DB::select
           ( "SELECT
           c.*,
           a.datePrevueVacc,
           a.citoyen_id,
           a.unite_vaccination_id,
           uv.nomUnite,
           uv.adresse as 'adresse_c',p.nomProvince

           FROM
               affecters a
           JOIN citoyens c ON
               c.id = a.citoyen_id
           JOIN unite_vaccinations uv ON
               uv.id = a.unite_vaccination_id
               join quartiers q on q.id=c.quartier_id
               join communs cc on cc.id=q.commun_id
               join provinces p on p.id=cc.province_id
           WHERE
               a.id IN(
                   SELECT
                       MAX(a2.id)
                   FROM
                       affecters a2
                   WHERE
                       a2.citoyen_id = a.citoyen_id
               )"

            );
// return $records;
       foreach($records as $row){
            $datenaiss=new DateTime($row->dateNaissance);
            $age=$today->diff($datenaiss)->format("%y ans");
         $return[] = array(
           /**Informations Citoyenne */
           'province_name' => $row->nomProvince,
           'cin'           => $row->cin,
           'nom'           => $row->nom,
           'prenom'        =>$row->prenom,
           'dateNaissance' =>$age,
           'adresse'       =>$row->adresse_c,
           'tel'           =>$row->tel,
           'email'         =>$row->email,
           'profession'    =>$row->profession,
           'lieuTravail'   =>$row->lieu_travail,
           'email'         =>$row->email,
           'citoyen_id'    =>$row->citoyen_id,
           'adressc'       =>$row->adresse,
           /**Informations RDV */
           'unite_vaccination_id'=>$row->unite_vaccination_id,
           'datePrevueVacc' =>$row->datePrevueVacc,
           'nomUnite' =>$row->nomUnite,
           'etape' =>$this->getEtape($row->citoyen_id,$row->datePrevueVacc)['EtapeVaccination'],
           'TypeVaccination' =>$this->getEtape($row->citoyen_id,$row->datePrevueVacc)['TypeVaccination'],

         );

       }
       return view('superAdmin.list_citoyen_willaya')->with([
         'return'      =>$return
       ]);


    }


 /*get Etape_Vaccination ,Type_Vaccination By Citoyen*/


 public function getEtape($Citoyenn_ID,$datePrevueVacc){

    $EtapeVaccination ='';
    $TypeVaccination  ='';
    $DataVaccins      = [];
    /****get date Systeme****/
    $now = date('Y-m-d');
    /***get total of citoyen in vacciner***/
    $EtapeCitoyen = DB::table('vacciners')
    ->where('vacciners.citoyen_id',$Citoyenn_ID)
    ->count();

    /***get Etape de vaccination By Citoyen***/
     $EtapeVaccin=DB::select
     ( "SELECT
             v.etape,v.citoyen_id,tv.nomVaccin
             FROM
             vacciners v
             INNER join types_vaccins tv
             on v.type_vaccin_id = tv.id
             WHERE
                 v.id IN(
                     SELECT
                         MAX(v2.id)
                     FROM
                     vacciners v2
                     where v2.citoyen_id=v.citoyen_id
                 )
             and
             v.citoyen_id=$Citoyenn_ID"
     );
     $etapeV          = isset($EtapeVaccin[0]) && is_object($EtapeVaccin[0]) ? $EtapeVaccin[0]->etape : null;
     $TypeVaccination = isset($EtapeVaccin[0]) && is_object($EtapeVaccin[0]) ? $EtapeVaccin[0]->nomVaccin : null;

     /*Etape de vaccin = premiére prise Si [le citoyen n'a jamais été vacciné OU il a vacciné le premiére prise mais il a raté son RDV de rappel de x jours ]*/
     if ((strtotime($now) > strtotime($datePrevueVacc) && $etapeV=='1ere_prise') ||($EtapeCitoyen==0)){
         $EtapeVaccination = '1ere_prise';
     }

     /*Etape de vaccin = Rappel Si [le citoyen a vacciné le premiere prise et il n'a pas ratté son RDV de rappel
     OU il a vacciné le premiére prise  mais il a raté son RDV de rapel de x jours ]*/

     if (strtotime($now) < strtotime($datePrevueVacc) && $etapeV=='1ere_prise'){
         $EtapeVaccination='rappel';
     }

     /**Etape de vaccination = Vacciné Si le Citoyen a vacciné  */
     if($etapeV=='rappel'){
         $EtapeVaccination='vaccine';
     }


    $DataVaccins=[
       'EtapeVaccination'=> $EtapeVaccination,
       'TypeVaccination' => $TypeVaccination

    ];
    return $DataVaccins;
 }


}
