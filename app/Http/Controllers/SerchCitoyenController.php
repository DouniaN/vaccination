<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Citoyen;
use App\Unite_vaccination;
use App\Vaccin;
use App\Vacciner;
use App\Affecter;
use App\types_vaccin;
use DateTime;
use Illuminate\Support\Facades\DB;

class SerchCitoyenController extends Controller
{


    public function index(){

           $user    = auth()->user();
           $id_user = $user->id;
           $unite_id   =DB::table('user_unites')
          ->select('unite_vaccination_id')
          ->where('user_id',$id_user)
          ->get();

          $unite_vaccination_id=$unite_id[0]->unite_vaccination_id;
          /*Get DateTime Now */
          $today   =new Datetime();

        /*******************Get MembreEquipe**************/

          $QueryMembreEquipe = DB::table('contenirs')
          ->join('membre_equipes', 'contenirs.membre_equipe_id', '=', 'membre_equipes.id')
          ->select('membre_equipes.id','membre_equipes.nom','membre_equipes.prenom')
          ->where('unite_vaccination_id',$unite_vaccination_id)
          ->get();


        /*****************Get Type_Vaccin**************/

             $QueryTypevaccin =DB::table('types_vaccins')
             ->select('id','nombreLots','nomVaccin')
             ->get();

           $records=DB::select
           ( "SELECT
                c.*,
                a.datePrevueVacc,
                a.citoyen_id,
                a.unite_vaccination_id,
                a.plage_horaire,
                uv.nomUnite,
                uv.adresse

                FROM
                    affecters a
                JOIN citoyens c ON
                    c.id = a.citoyen_id
                JOIN unite_vaccinations uv ON
                    uv.id = a.unite_vaccination_id
                WHERE
                    a.id IN(
                        SELECT
                            MAX(a2.id)
                        FROM
                            affecters a2
                        WHERE
                            a2.citoyen_id = a.citoyen_id
                    )
                and

                a.unite_vaccination_id = $unite_vaccination_id"

            );
           $empty='non';
           $nbrDays='';
           $dureeVaccination='';
           foreach($records as $row){

                /********vaccin gripabe*********/
                //return $row->citoyen_id;

                /********get Age*********/
                $datenaiss =new DateTime($row->dateNaissance);
                $age=$today->diff($datenaiss)->format("%y ans");

                /************get duration Vaccination***********/
                /****get datePrevueVaccination ***/

                $datePrevueVaccin  = $row->datePrevueVacc;
                $date_vaccGripe    = $row->date_vaccGripe;

                $date1 = "2020-12-24";
                $date2 = "2020-01-15";

            /*   $diff = abs(strtotime($date2) - strtotime($date1));
               $years = floor($diff /365);
               $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
               $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                 dd($years);
                $yearc =$years*365+$months*60+$days;*/

                $datePrevueVaccinat    = strtotime($datePrevueVaccin);
                $dateVaccinationGripal = strtotime($date_vaccGripe);

                if($date_vaccGripe!=''){
                  $datediff = $datePrevueVaccinat - $dateVaccinationGripal;
                  $dureeVG  = round($datediff / (60 * 60 * 24));
                }

                /*****get dateVaccinationGrippale*****/



                $return[] = array(
                  /**Informations Citoyenne */
                  'cin' => $row->cin,
                  'nom' =>$row->nom,
                  'prenom' =>$row->prenom,
                  'dateNaissance' =>$age,
                  'adresse' =>$row->adresse,
                  'tel' =>$row->tel,
                  'email' =>$row->email,
                  'profession'  =>$row->profession,
                  'lieuTravail' =>$row->lieu_travail,
                  'email' =>$row->email,
                  'citoyen_id'=>$row->citoyen_id,
                  'adressc'=>$row->adresse,
                  /**Informations RDV */
                  'unite_vaccination_id'=>$row->unite_vaccination_id,
                  'datePrevueVacc' =>$row->datePrevueVacc,
                  'nomUnite' =>$row->nomUnite,
                  'etape' =>$this->getEtape($row->citoyen_id)['EtapeVaccination'],
                  'TypeVaccination' =>$this->getEtape($row->citoyen_id)['TypeVaccination'],
                  'maladie_cardiaque'=>$row->maladie_cardiaque,
                  'maladie_chronique'=>$row->maladie_chronique,
                  'maladie_foie'=>$row->maladie_foie,
                  'hypertention'=>$row->hypertention,
                  'cancer'=>$row->cancer,
                  'enceinte'=>$row->enceinte,
                  'sufisance_renal'=>$row->sufisance_renal,
                  'maladie_resperatoire'=>$row->maladie_resperatoire,
                  'sys_uminitaire'=>$row->sys_uminitaire,
                  'sexe'=>$row->sexe,
                  'vaccin_grippal'=>$row->vaccin_grippal,
                  'plage_horaire'=>$row->plage_horaire,

                );

            }

           return view('unite.list_citoyen')->with([
             'return'      =>$return,
             'MembreEquipes' =>$QueryMembreEquipe,
             'QueryTypevaccin'=>$QueryTypevaccin
           ]);
           return $return;

    }

    /*get Etape_Vaccination ,Type_Vaccination By Citoyen*/
    public function getEtape($Citoyenn_ID){

       $EtapeVaccination='';
       $TypeVaccination='';
       $DataVaccins=[];
       /****get date System****/
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

        /*Etape de vaccin = premiére prise Si [le citoyen n'a jamais été vacciné ]*/


        if ($EtapeCitoyen==0){
            $EtapeVaccination = '1ere_prise';
        }
        /*Etape de vaccin = Rappel Si [le citoyen a vacciné le premiere prise]*/

        if ($etapeV=='1ere_prise'){
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

    public function getdetail(Request $request){

      $cin          = $request->get('cinCitoyen');
      $citoyen_id   = $request->get('idcitoyen');
      $datas        =[];
      $datacitoyens =DB::select
       (" SELECT
           c.*,
           a.datePrevueVacc,
           a.citoyen_id,
           a.unite_vaccination_id,
           a.plage_horaire,
           uv.nomUnite,
           uv.adresse as 'adresse_unite',
           p.nomProvince
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
               )
           and
           c.cin = '$cin'"

        );

        $datavaccination = DB::table('vacciners')
        ->join('types_vaccins','vacciners.type_vaccin_id','=', 'types_vaccins.id')
        ->join('membre_equipes','vacciners.membre_id','=', 'membre_equipes.id')
        ->select('vacciners.dateVaccination','vacciners.etape','vacciners.lot','types_vaccins.nomVaccin','membre_equipes.nom','membre_equipes.prenom')
        ->where('vacciners.citoyen_id',$citoyen_id)
        ->get();

        $cnt=count($datavaccination);

        $datas=[
           'datacitoyens'     =>$datacitoyens,
           'datavaccination'  =>$datavaccination,
           'Vaccination' =>$cnt
        ];
        return $datas;

    }
    public function vacciner(Request $request){

      $citoyen_id           = $request->get('idcitoyen');


      $getvaccination = DB::table('vacciners')
      ->select('vacciners.dateVaccination','vacciners.etape')
      ->where('vacciners.citoyen_id',$citoyen_id)
      ->get();

      $cnt=count($getvaccination);

      return $cnt;
    }

    public function add_vaccination(Request $request){

        $vaccination                         = new Vacciner ;
        $idcitoyen                           = $request->input('idcitoyen');
        $etape                               = $request->input('etape');
        $unite_vaccination_id                = $request->input('unite_vaccination_id');
        $Date_Vaccination                    = $request->input('Date_Vaccination');
        $Date_Prevue_Vaccination             = $request->input('Date_rdv');
        $id_type_vaccination                 = $request->input('id_type_vaccination');
        $plage_horaire                       = $request->input('plage_horaire');
        $date_prevue_vaccination_2eme_dose   ='' ;

        $getnombreLots =DB::table('types_vaccins')
               ->select('nombreLots')
               ->where('id',$id_type_vaccination)
               ->get();
        $nombreLot                           = $getnombreLots[0]->nombreLots;
        if($nombreLot==3){
            $lot                             = $request->input('lottyp2');
        }
        if($nombreLot==4){
           $lot                              = $request->input('lottyp1');
        }
        $membre_id                           = $request->input('membre_id');

        if($etape=='1ere_prise'){
            if($id_type_vaccination==1){
                 $date_prevue_vaccination_2eme_dose= date('Y-m-d', strtotime($Date_Prevue_Vaccination. ' + 21 days'));
                 $timestamp = strtotime($date_prevue_vaccination_2eme_dose);
                 $dayNow = date('D', $timestamp);

            }
            else if($id_type_vaccination==2){
                 $date_prevue_vaccination_2eme_dose= date('Y-m-d', strtotime($Date_Prevue_Vaccination. ' + 28 days'));
                 $timestamp = strtotime($date_prevue_vaccination_2eme_dose);
                 $dayNow = date('D', $timestamp);
            }

            if($dayNow=='Sun'){
                $date_prevue_vaccination_2eme_dose= date('Y-m-d', strtotime($date_prevue_vaccination_2eme_dose. ' + 1 days'));

            }

            $GetCountCitoyenByDate = DB::select("select count(*) as NBRCitoyen from update_rdvs where datePrevueVacc ='$date_prevue_vaccination_2eme_dose'");
            $NBRCitoyens = $GetCountCitoyenByDate[0]->NBRCitoyen;

            $GetCapaciteByUnite    = DB::select("select capacite from unite_vaccinations where id=$unite_vaccination_id");
            $Capacite = $GetCapaciteByUnite[0]->capacite;

            if($NBRCitoyens==$Capacite){
                $date_prevue_vaccination_2eme_dose= date('Y-m-d', strtotime($date_prevue_vaccination_2eme_dose. ' + 1 days'));
            }

            $updateRDVS        = DB::update("update update_rdvs set datePrevueVacc='$date_prevue_vaccination_2eme_dose',etape='rappel' WHERE citoyen_id=$idcitoyen");
            $queryInsertaffect = DB::insert("insert into affecters (datePrevueVacc,citoyen_id,unite_vaccination_id,plage_horaire) values('$date_prevue_vaccination_2eme_dose',$idcitoyen,$unite_vaccination_id,'$plage_horaire')");
        }

        $ADDVaccins        = DB::insert("insert into vacciners(dateVaccination,etape,citoyen_id,unite_vaccination_id,lot,membre_id,type_vaccin_id,vaccination) values('$Date_Vaccination','$etape',$idcitoyen,$unite_vaccination_id,'$lot',$membre_id,$id_type_vaccination,'ok' )");

        return redirect('getCitoyens')->with('success','Action effectuée avec succéss');

    }



    public function updatecitoyen(Request $request)
    {
        $req=DB::select("UPDATE citoyens SET email='".$request->input('email')."',tel='".$request->input('tel')."',sexe='".$request->input('sexe')."',profession='".$request->input('profession')."',lieu_travail='".$request->input('lieu_travail')."',vaccin_grippal='".$request->input('vaccin_grip')."',date_vaccGripe='".$request->input('date_vaccin_grip')."',enceinte='".$request->input('enceinte')."' WHERE id=".$request->input('citoyen'));
        if($request->input('chroniquement')=='oui'){
            $req=DB::select("UPDATE citoyens SET maladie_chronique='".$request->input('chroniquement')."',maladie_cardiaque='".$request->input('cardiaque')."',maladie_foie='".$request->input('foie')."',hypertention='".$request->input('hypertention')."',cancer='".$request->input('cancer')."',sufisance_renal='".$request->input('sufisance_renal')."',maladie_resperatoire='".$request->input('maladie_resperatoire')."',sys_uminitaire='".$request->input('sys_uminitaire')."' WHERE id=".$request->input('citoyen'));
        }
        else{
            $req=DB::select("UPDATE citoyens SET maladie_chronique='".$request->input('chroniquement')."',maladie_cardiaque='',maladie_foie='',hypertention='',cancer='',sufisance_renal='',maladie_resperatoire='',sys_uminitaire='' WHERE id=".$request->input('citoyen'));
        }

        return redirect('getCitoyens')->with('success','Action effectuée avec succéss');
    }




}
