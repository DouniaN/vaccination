<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WilayaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function citoyensvaccineswillaya()
    {

        //$idprovince=5;

         $idprovince=Auth::user()->id;
        $vaccinun=[];
        $vaccinunsomm=0;




        $verifietableva=DB::select("SELECT count(*) as ver FROM vacciners ");
        if($verifietableva[0]->ver!=0)
        {
            $vaccinunsom=DB::select("SELECT count(*) as somme FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id   where  v.etape='1ere_prise' and c.id NOT IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel')");
            $vaccinunsomm =$vaccinunsom[0]->somme;
            $vaccinun=DB::select("SELECT * ,c.id as idcitoyen FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id inner join unite_vaccinations uv on uv.id=v.unite_vaccination_id where  uv.type_unite='centre de santé' and v.etape='1ere_prise' and c.id NOT IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel')");
        }

        $rappelsom=DB::select("SELECT count(*) as som FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id   where  c.id  IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel') and v.etape='rappel'");

        $rappel=DB::select(" SELECT * ,c.id as idcitoyen FROM  citoyens c INNER JOIN quartiers q on q.id=c.quartier_id INNER JOIN communs cc on cc.id=q.commun_id inner JOIN provinces p on p.id=cc.province_id inner join vacciners v on v.citoyen_id=c.id   inner join unite_vaccinations uv on uv.id=v.unite_vaccination_id where  uv.type_unite='centre de santé' and  c.id  IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel') and v.etape='rappel'");


        return view('superAdmin.citoyensvaccineswillaya')->with([
            'vaccinun'=>$vaccinun,
            'rappel'=>$rappel,
            'rappelsom'=>$rappelsom[0]->som,
            'vaccinunsom'=>$vaccinunsomm

        ]);


    }

    public function citoyenraterdv()
    {


        $vaccinun=DB::select("select * ,c.id as idcitoyen from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id inner join regions r on r.id=p.region_id inner join affecters aff on aff.citoyen_id=c.id INNER JOIN unite_vaccinations un ON un.id=aff.citoyen_id where  DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc )");

        $rappel=DB::select("select *,c.id as idcitoyen from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id inner join regions r on r.id=p.region_id inner join affecters aff on aff.citoyen_id=c.id INNER JOIN unite_vaccinations un ON un.id=aff.citoyen_id where DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc where vc.etape=1) and c.id not in(SELECT vc.id from vacciners vc where vc.etape!='rappel')");
 $vaccinunsom=DB::select("select count(*) as som  from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id inner join regions r on r.id=p.region_id inner join affecters aff on aff.citoyen_id=c.id where  DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc )");
        $rappelsom=DB::select("select count(*) as somm from citoyens c inner join quartiers q on q.id=c.quartier_id inner join communs com on com.id=q.commun_id inner join provinces p on p.id=com.province_id inner join regions r on r.id=p.region_id inner join affecters aff on aff.citoyen_id=c.id where DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc where vc.etape=1) and c.id not in(SELECT vc.id from vacciners vc where vc.etape!='rappel')");

        return view('superAdmin.listecitoyenratrdvw')->with([
            'vaccinun'=>$vaccinun,
            'rappel'=>$rappel,
            'vaccinunsom'=>$vaccinunsom[0]->som,
            'rappelsom'=>$rappelsom[0]->somm

        ]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
