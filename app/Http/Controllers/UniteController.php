<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UniteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function citoyensvaccinesunite()
    {
       // $idunite=2;

        $idunite=Auth::user()->id;
        $vaccinun=[];
        $vaccinunsomm=0;

        $uniteconnect=DB::select("select unite_vaccination_id	 as idd from user_unites where user_id=".$idunite);
        $idd=$uniteconnect[0]->idd;


        $verifietableva=DB::select("SELECT count(*) as ver FROM vacciners ");
        if($verifietableva[0]->ver!=0)
        {
            $vaccinunsom=DB::select("SELECT count(*) as somme FROM  citoyens c   inner join vacciners v on v.citoyen_id=c.id   where v.unite_vaccination_id=".$idd." and v.etape='1ere_prise' and c.id NOT IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel')");
            $vaccinunsomm =$vaccinunsom[0]->somme;
            $vaccinun=DB::select("SELECT *,c.id as idcitoyen FROM  citoyens c  inner join vacciners v on v.citoyen_id=c.id   where v.unite_vaccination_id=".$idd." and v.etape='1ere_prise' and c.id NOT IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel')");
        }

        $rappelsom=DB::select("SELECT count(*) as som FROM  citoyens c  inner join vacciners v on v.citoyen_id=c.id   where v.unite_vaccination_id=".$idd." and c.id  IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel')and v.etape='rappel'");

        $rappel=DB::select("SELECT *,c.id as idcitoyen FROM  citoyens c  inner join vacciners v on v.citoyen_id=c.id   where v.unite_vaccination_id=".$idd." and c.id  IN (select c.id from citoyens c INNER JOIN vacciners v on v.citoyen_id=c.id where v.etape='rappel') and v.etape='rappel' ");


        return view('unite.citoyensvaccinesunite')->with([
            'vaccinun'=>$vaccinun,
            'rappel'=>$rappel,
            'rappelsom'=>$rappelsom[0]->som,
            'vaccinunsom'=>$vaccinunsomm

        ]);
    }




    public function citoyenraterdv()
    {
       // $idunite=2;

         $idunite=Auth::user()->id;
        $vaccinun=[];

        $uniteconnect=DB::select("select unite_vaccination_id as idd from user_unites where user_id=".$idunite);
        $idd=$uniteconnect[0]->idd;

            $vaccinun=DB::select("select c.* from citoyens c inner join update_rdvs ur on c.id=ur.citoyen_id where ur.unite_id=".$idd." and DATEDIFF(now(),ur.datePrevueVacc)>0 and etape='1ere_prise'");



        $rappel=DB::select("select c.* from citoyens c inner join update_rdvs ur on c.id=ur.citoyen_id where ur.unite_id=".$idd." and DATEDIFF(now(),ur.datePrevueVacc)>0 and etape='rappel'");

$rappelsom=DB::select("select count(*) as somm from citoyens c inner join vacciners v on v.citoyen_id=c.id  inner join affecters aff on aff.citoyen_id=c.id where  v.unite_vaccination_id=".$idd." and DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc where vc.etape='1ere_prise') and c.id not in(SELECT vc.id from vacciners vc where vc.etape!='rappel') ");
        $vaccinunsom=DB::select("select count(*) as som from citoyens c inner join vacciners v on v.citoyen_id=c.id  inner join affecters aff on aff.citoyen_id=c.id where v.unite_vaccination_id=".$idd." and DATEDIFF(now(),aff.datePrevueVacc)>0 and c.id not in(SELECT vc.id from vacciners vc )");

        return view('unite.listecitoyenratrdvu')->with([
            'vaccinun'=>$vaccinun,
            'rappel'=>$rappel,
 'vaccinunsom'=>$vaccinunsom[0]->som,
            'rappelsom'=>$rappelsom[0]->somm,


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
     public function find($id){
        return DB::select('select us.username "login" from users us join user_unites uu on uu.user_id=us.id where uu.unite_vaccination_id='.$id);
    }
}
