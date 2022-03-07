<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $req=DB::select("select * from citoyens where  maladie_chronique is null");
       // dd($req);
        return view('table')->with([
          'req'=>$req
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updatecitoyen( Request $request)
    {
        if($request->input('chroniquement')=='oui'){
            $req=DB::select("UPDATE citoyens SET maladie_chronique='".$request->input('chroniquement')."',maladie_cardiaque='".$request->input('cardiaque')."',maladie_foie='".$request->input('foie')."',hypertention='".$request->input('hypertention')."',cancer='".$request->input('cancer')."',enceinte='".$request->input('enceinte')."',sufisance_renal='".$request->input('sufisance_renal')."',maladie_resperatoire='".$request->input('maladie_resperatoire')."',sys_uminitaire='".$request->input('sys_uminitaire')."' WHERE id=".$request->input('citoyen'));
        }
        else{
            $req=DB::select("UPDATE citoyens SET maladie_chronique='".$request->input('chroniquement')."' WHERE id=".$request->input('citoyen'));
        }


      return redirect('table') ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unitesa()
    {
        $point=DB::select("select * from  unite_vaccinations");
       //  dd($point);
        return view('unite_san')->with([
            'point'=>$point
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function citoyensvaccines()
    {

       // $idprovince=5;
       $idprovince=Auth::user()->id;
        $vaccinun=[];
        $vaccinunsomm=0;

        $provincconnect=DB::select("select p.id  as idd from user_provinces up INNER JOIN provinces p on p.id=up.province_id where up.user_id=".$idprovince);
         $idd=$provincconnect[0]->idd;


              $verifietableva=DB::select("SELECT count(*) as ver FROM vacciners ");
              if($verifietableva[0]->ver!=0)
              {
                  $vaccinunsom=DB::select("SELECT count(*) as somme FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id   where p.id=".$idd." and v.etape='1ere_prise' and c.id NOT IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel')");
                  $vaccinunsomm =$vaccinunsom[0]->somme;
                  $vaccinun=DB::select("SELECT *,c.id as idcitoyen FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id  inner join unite_vaccinations uv on uv.id=v.unite_vaccination_id where  uv.type_unite='centre de santé' and p.id=".$idd." and v.etape='1ere_prise' and c.id NOT IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel')");
              }

              $rappelsom=DB::select("SELECT count(*) as som FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id   where p.id=".$idd." and c.id  IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel') and v.etape='rappel'");

              $rappel=DB::select(" SELECT *,c.id as idcitoyen FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id   inner join unite_vaccinations uv on uv.id=v.unite_vaccination_id where  uv.type_unite='centre de santé' and p.id=".$idd." and c.id  IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel') and v.etape='rappel' ");


              return view('admin.citoyensvaccines')->with([
                  'vaccinun'=>$vaccinun,
                  'rappel'=>$rappel,
                  'rappelsom'=>$rappelsom[0]->som,
                  'vaccinunsom'=>$vaccinunsomm

              ]);




    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function citoyenraterdv()
    {

       // $idprovince=5;
         $idprovince=Auth::user()->id;


        $provincconnect=DB::select("select p.id  as idd from user_provinces up INNER JOIN provinces p on p.id=up.province_id where up.user_id=".$idprovince);
        $idd=$provincconnect[0]->idd;

         $vaccinun=DB::select("select *,c.id as idcitoyen from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id  inner join affecters aff on aff.citoyen_id=c.id where p.id=".$idd." and DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc ) and p.id=".$idd);


        $rappel=DB::select("select *,c.id as idcitoyen from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id  inner join affecters aff on aff.citoyen_id=c.id where p.id=".$idd." and DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc where vc.etape=1) and c.id not in(SELECT vc.id from vacciners vc where vc.etape!=2) and p.id=".$idd);
$vaccinunsom=DB::select("select count(*) as som from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id  inner join affecters aff on aff.citoyen_id=c.id where p.id=".$idd." and DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc ) and p.id=".$idd);

        $rappelsom=DB::select("select count(*) as somm from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id  inner join affecters aff on aff.citoyen_id=c.id where p.id=".$idd." and DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc where vc.etape=1) and c.id not in(SELECT vc.id from vacciners vc where vc.etape!=2) and p.id=".$idd);


        return view('admin.listecitoyenratrdvp')->with([
            'vaccinun'=>$vaccinun,
            'rappel'=>$rappel,
            'vaccinunsom'=>$vaccinunsom[0]->som,
            'rappelsom'=>$rappelsom[0]->somm

        ]);
    }
    
        public function ajaxxxx(Request $request)
    {
        $results = array();

        $req=DB::select("select * from citoyens where  id='".$request->get('id')."'");

            foreach ($req as $row) {
                $results[] = [
                    $row->sexe,
                    $row->tel,
                    $row->email,
                    $row->profession,
                    $row->lieu_travail,
                    $row->maladie_chronique,
                    $row->maladie_cardiaque,
                    $row->maladie_foie,
                    $row->hypertention,
                    $row->cancer,
                    $row->enceinte,
                    $row->sufisance_renal,
                    $row->maladie_resperatoire,
                    $row->sys_uminitaire,
                     $row->vaccin_grippal,
                $row->date_vaccGripe

                ];
            }

        return response()->json($results);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
