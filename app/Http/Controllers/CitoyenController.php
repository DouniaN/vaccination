<?php

namespace App\Http\Controllers;

use App\Citoyen;
use App\Mail\notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Array_;

class CitoyenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function searchResult(Request $request){


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

        if(count($unite_id)!=0){
          //  $data=DB::select("select * from citoyens c inner join
            //quartiers q on q.id=c.quartier_id inner join
            //communs com on com.id=q.commun_id inner join
            //provinces p on p.id=com.province_id inner join
            //regions r on r.id=p.id
            //inner join affecters aff on aff.citoyen_id=c.id
            //inner join unite_vaccinations uv on uv.id=aff.unite_vaccination_id where uv.id=".$unite_id[0]->unite_vaccination_id);
            $unite_vaccination_id=$unite_id[0]->unite_vaccination_id;
            $records=DB::select
            ( "SELECT
                c.*,
                a.datePrevueVacc,
                a.citoyen_id,
                a.unite_vaccination_id,
                uv.nomUnite,
                uv.adresse

                FROM
                    affecters a
                JOIN citoyens c ON
                    c.id = a.citoyen_id
                JOIN unite_vaccinations uv ON
                    uv.id = a.unite_vaccination_id
                WHERE
                    c.cin='".$request->input('data')."' and
                a.unite_vaccination_id =$unite_vaccination_id limit 1"
            );

if(count($records)!=0){
    foreach($records as $row){
        $return[] = array(
            /**Informations Citoyenne */
            'cin' => $row->cin,
            'nom' =>$row->nom,
            'prenom' =>$row->prenom,
            'dateNaissance' =>$row->dateNaissance,
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
            'etape' =>$this->getEtape($row->citoyen_id)
        );

    }
}
    else{
        $return[] = array();
    }
    return view('unite.searchResult')->with([
        'return'      =>$return,
        'rec'=>count($records)
    ]);



            //return view('unite.searchResult');
        }
      if(count($province_id)!=0){
         // $data=DB::select("select * from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id inner join regions r on r.id=p.id where p.id=".$province_id[0]->$province_id);
          //Get id_user//

          //$unite_id = user_unites::where('user_id',$id_user)->first()->user_id;

          $provinces_id=$province_id[0]->province_id;



          $records=DB::select
          ( "SELECT
            c.*,
            a.datePrevueVacc,
            a.citoyen_id,
            a.unite_vaccination_id,
            uv.nomUnite,
            uv.adresse

            FROM
                affecters a
            JOIN citoyens c ON
                c.id = a.citoyen_id
            JOIN unite_vaccinations uv ON
                uv.id = a.unite_vaccination_id
                JOIN quartiers q on q.id=c.quartier_id JOIN
                communs com on com.id=q.commun_id
                JOIN provinces p on p.id=com.province_id
                JOIN regions r on r.id=p.region_id
            WHERE
           c.cin='".$request->input('data')."' and
            p.id =$provinces_id  limit 1"
          );

          foreach($records as $row){
              $return[] = array(
                  /**Informations Citoyenne */
                  'cin' => $row->cin,
                  'nom' =>$row->nom,
                  'prenom' =>$row->prenom,
                  'dateNaissance' =>$row->dateNaissance,
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
                  'etape' =>$this->getEtape($row->citoyen_id)

              );

          }

          return view('admin.searchResult')->with([
              'return'      =>$return
          ]);

        }
      else{
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
              c.cin='".$request->input('data')."' limit 1
               )"
          );
// return $records;
          foreach($records as $row){
              $return[] = array(
                  /**Informations Citoyenne */
                  'province_name' => $row->nomProvince,
                  'cin'           => $row->cin,
                  'nom'           => $row->nom,
                  'prenom'        =>$row->prenom,
                  'dateNaissance' =>$row->dateNaissance,
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
                  'etape' =>$this->getEtape($row->citoyen_id)

              );

          }
          return view('superAdmin.searchResult')->with([
              'return'      =>$return
          ]);
      }
    }

    public function getEtape($Citoyenn_ID){
        //$query="select count(*) from vacciners where citoyen_id =$reference";
        $EtapeVaccination='';
        $EtapeCitoyen = DB::table('vacciners')
            ->where('vacciners.citoyen_id',$Citoyenn_ID)
            ->count();
        if($EtapeCitoyen>0){
            $EtapeVaccination='Rappelle';
        }
        else{
            $EtapeVaccination='Premiére_Prise';
        }
        return $EtapeVaccination;
    }
    public function CitoyensMail(){
//auth()->user()
        $req1=DB::select("SELECT c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin from citoyens c inner join affecters aff on aff.citoyen_id=c.id where c.id not in(select v.citoyen_id from vacciners v) and DATEDIFF(datePrevueVacc,now())=1");
        $req2=DB::select("select c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin from citoyens c inner join affecters aff on aff.citoyen_id=c.id where c.id in(select v.citoyen_id from vacciners v where etape!='2') and DATEDIFF(datePrevueVacc,now())=1");
/*
        $req=DB::select("select c.email,aff.id,c.nom,c.prenom,aff.datePrevueVacc from affecters aff inner join citoyens c on c.id=aff.citoyen_id where DATEDIFF(datePrevueVacc,now())<=1
                            and sendMail is null");
        foreach ($req as $item){
            mail::to($item->email)->send(new notification($item->nom,$item->prenom,$item->datePrevueVacc));
        }
*/
        return view('unite/CitoyensMail')->with([
            'req1'=>$req1,
            'req2'=>$req2,
        ]);
    }

    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Citoyen  $citoyen
     * @return \Illuminate\Http\Response
     */
    public function show(Citoyen $citoyen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Citoyen  $citoyen
     * @return \Illuminate\Http\Response
     */
    public function edit(Citoyen $citoyen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Citoyen  $citoyen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Citoyen $citoyen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Citoyen  $citoyen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Citoyen $citoyen)
    {
        //
    } 
    public function CitoyensMailUnite(){
        $user    = auth()->user();
        $id_user = $user->id;

        $unite_id   =DB::table('user_unites')
            ->select('unite_vaccination_id')
            ->where('user_id',$id_user)
            ->get();

        $req1=DB::select("SELECT c.id,c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin,p.nomProvince
from citoyens c inner join affecters aff on aff.citoyen_id=c.id
inner join quartiers q on q.id=c.quartier_id
inner join communs comm on comm.id=q.commun_id
inner join provinces p on p.id=comm.province_id
inner join regions r on r.id=p.region_id
inner join unite_vaccinations uv on uv.id=aff.unite_vaccination_id
where c.id not in(select v.citoyen_id from vacciners v) and DATEDIFF(datePrevueVacc,now())=1 and uv.id='".$unite_id[0]->unite_vaccination_id."'");

        $req2=DB::select("select c.id,c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin,p.nomProvince
from citoyens c inner join affecters aff on aff.citoyen_id=c.id
inner join quartiers q on q.id=c.quartier_id
inner join communs comm on comm.id=q.commun_id
inner join provinces p on p.id=comm.province_id
inner join regions r on r.id=p.region_id
inner join unite_vaccinations uv on uv.id=aff.unite_vaccination_id
where c.id in(select v.citoyen_id from vacciners v where etape!='rappel') and DATEDIFF(datePrevueVacc,now())=1 and uv.id='".$unite_id[0]->unite_vaccination_id."'");

        return view('unite/CitoyensMail')->with([
            'req1'=>$req1,
            'req2'=>$req2,
        ]);
    }

    public function CitoyensMailRegion(){
$req1=DB::select("SELECT c.id,c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin,p.nomProvince
from citoyens c inner join affecters aff on aff.citoyen_id=c.id
inner join quartiers q on q.id=c.quartier_id
inner join communs comm on comm.id=q.commun_id
inner join provinces p on p.id=comm.province_id
inner join regions r on r.id=p.region_id
inner join unite_vaccinations uv on uv.id=aff.unite_vaccination_id
where c.id not in(select v.citoyen_id from vacciners v) and DATEDIFF(datePrevueVacc,now())=1");

$req2=DB::select("select  c.id,c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin,p.nomProvince
from citoyens c inner join affecters aff on aff.citoyen_id=c.id
inner join quartiers q on q.id=c.quartier_id
inner join communs comm on comm.id=q.commun_id
inner join provinces p on p.id=comm.province_id
inner join regions r on r.id=p.region_id
inner join unite_vaccinations uv on uv.id=aff.unite_vaccination_id
where c.id in(select v.citoyen_id from vacciners v where etape!='rappel') and DATEDIFF(datePrevueVacc,now())=1");

        return view('superAdmin/CitoyensMail')->with([
            'req1'=>$req1,
            'req2'=>$req2,
        ]);
    }

    public function CitoyensMailProvince(){
        $user    = auth()->user();
        $id_user = $user->id;

        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();

        $req1=DB::select("SELECT c.id,c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin,p.nomProvince
from citoyens c inner join affecters aff on aff.citoyen_id=c.id
inner join quartiers q on q.id=c.quartier_id
inner join communs comm on comm.id=q.commun_id
inner join provinces p on p.id=comm.province_id
inner join regions r on r.id=p.region_id
inner join unite_vaccinations uv on uv.id=aff.unite_vaccination_id
where c.id not in(select v.citoyen_id from vacciners v) and DATEDIFF(datePrevueVacc,now())=1 and uv.id='".$province_id[0]->province_id."'");

        $req2=DB::select("select  c.id,c.nom,c.prenom,c.adresse,c.tel,c.email,aff.datePrevueVacc,c.cin,p.nomProvince
from citoyens c inner join affecters aff on aff.citoyen_id=c.id
inner join quartiers q on q.id=c.quartier_id
inner join communs comm on comm.id=q.commun_id
inner join provinces p on p.id=comm.province_id
inner join regions r on r.id=p.region_id
inner join unite_vaccinations uv on uv.id=aff.unite_vaccination_id
where c.id in(select v.citoyen_id from vacciners v where etape!='rappel') and DATEDIFF(datePrevueVacc,now())=1 and uv.id='".$province_id[0]->province_id."'");

        return view('admin/CitoyensMail')->with([
            'req1'=>$req1,
            'req2'=>$req2,
        ]);
    }




    public function searchResultRegion(Request $request){

        $user    = auth()->user();
        $id_user = $user->id;


        $records=DB::select( "SELECT
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
              c.cin='".$request->input('data')."'  limit 1
               ");
// return $records;

        if(count($records)!=0) {
            foreach ($records as $row) {
                $return[] = array(
                    /**Informations Citoyenne */
                    'province_name' => $row->nomProvince,
                    'cin' => $row->cin,
                    'nom' => $row->nom,
                    'prenom' => $row->prenom,
                    'dateNaissance' => $row->dateNaissance,
                    'adresse' => $row->adresse_c,
                    'tel' => $row->tel,
                    'email' => $row->email,
                    'profession' => $row->profession,
                    'lieuTravail' => $row->lieu_travail,
                    'email' => $row->email,
                    'citoyen_id' => $row->citoyen_id,
                    'adressc' => $row->adresse,
                    /**Informations RDV */
                    'unite_vaccination_id' => $row->unite_vaccination_id,
                    'datePrevueVacc' => $row->datePrevueVacc,
                    'nomUnite' => $row->nomUnite,
                    'etape' => $this->getEtape($row->citoyen_id)
                );
            }
        }
        else{
            $return[] = array();
        }
        return view('superAdmin.searchResult')->with([
            'return'      =>$return,
            'rec'=>count($records)
        ]);
    }

    public function searchResultProvince(Request $request){
        $user    = auth()->user();
        $id_user = $user->id;

        $province_id   =DB::table('user_provinces')
            ->select('province_id')
            ->where('user_id',$id_user)
            ->get();

        $unite_vaccination_id=$province_id[0]->province_id;
        $records=DB::select
        ( "SELECT c.*, a.datePrevueVacc, a.citoyen_id, a.unite_vaccination_id, uv.nomUnite, uv.adresse FROM affecters a JOIN citoyens c ON c.id = a.citoyen_id JOIN unite_vaccinations uv ON uv.id = a.unite_vaccination_id join quartiers q on q.id=c.quartier_id join communs comm on comm.id=q.commun_id join provinces p on p.id=comm.province_id
                WHERE
                    c.cin='".$request->input('data')."' and
                a.unite_vaccination_id =$unite_vaccination_id  limit 1"
        );

        if(count($records)!=0){
            foreach($records as $row){
                $return[] = array(
                    /**Informations Citoyenne */
                    'cin' => $row->cin,
                    'nom' =>$row->nom,
                    'prenom' =>$row->prenom,
                    'dateNaissance' =>$row->dateNaissance,
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
                    'etape' =>$this->getEtape($row->citoyen_id)

                );

            }
        }
        else{
            $return[] = array();
        }

        return view('admin.searchResult')->with([
            'return'      =>$return,
            'rec'=>count($records)
        ]);

    }
    
    public function sansRDV()
    {
        $citoyRDV=DB::select("SELECT *,c.adresse as 'adressec',c.tel as 'telc',a.`id` AS'idct' FROM `affecters` a INNER JOIN citoyens c ON c.id=a.`citoyen_id` INNER JOIN unite_vaccinations u ON u.id=a.`unite_vaccination_id` INNER JOIN quartiers q ON q.id=c.quartier_id INNER JOIN communs co ON co.id=q.commun_id INNER JOIN provinces pr ON pr.id=co.province_id WHERE a.`datePrevueVacc` IS NULL and u.`type_unite`='Centre de santé'");
        $citoyn=DB::select("SELECT * FROM `affecters`");
        return view('superAdmin.citoyensansRDV')->with([
            'citoyRDV'=>$citoyRDV,
            'citoyn'=>$citoyn
        ]);
    }
    public function ajoutC($id)
    {
        $citoyn=DB::select("SELECT * FROM `affecters` where id='".$id."'");
        return view('superAdmin.AjouterRDV')->with([
            'citoyn'=>$citoyn
        ]);
    }
    public function ajout(Request $request)
    {
        $id=$request->input('idcit');
        $updateC=DB::update("UPDATE `affecters` SET `datePrevueVacc`='".$request->input('datePrevueVacc')."',`plage_horaire`='".$request->input('plage_horaire')."' WHERE id='".$id."'");
      // return $id;
        //return $updateC;
        return redirect('citoyensansRDV');
    }

}
