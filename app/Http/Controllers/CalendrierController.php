<?php

namespace App\Http\Controllers;

use App\Affecter;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;




class CalendrierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function somme(Request $request)
    {
        $results = array();
        $idunite=Auth::user()->id;


        $uniteconnect=DB::select("select unite_vaccination_id	 as idd from user_unites where user_id=".$idunite);
        $idd=$uniteconnect[0]->idd;
      $matin= DB::select("select count(*) as mat from update_rdvs where plage_horaire='Matin' and unite_id='".$idd."' and datePrevueVacc='".$request->date."'");
        $soire=DB::select("select count(*) as soi from update_rdvs where plage_horaire='Apres-midi' and unite_id='".$idd."'and datePrevueVacc='".$request->date."'");
          $capacity=DB::select("SELECT capacite as capacite FROM unite_vaccinations where id='".$idd."'");

        $results[] = [$matin[0]->mat,$soire[0]->soi,$capacity[0]->capacite];

        return response()->json($results);

    }

    public function calendrieer($id)
    {
        $datee='2020-12-06';
       // dd($datee<now());
        $idunite=Auth::user()->id;

        $table []=array("dataAff"=>"","nbr"=>"","capacite"=>"","datea"=>"","dateu"=>"",'nbrr2'=>"");
//info
        $uniteconnect=DB::select("select unite_vaccination_id	 as idd from user_unites where user_id=".$idunite);
        $idd=$uniteconnect[0]->idd;
        $nomunite=DB::select("select nomUnite	 as nom from unite_vaccinations where id='".$idd."'");
        $unitenom=$nomunite[0]->nom;
        $info=DB::select("SELECT * FROM citoyens  where id=".$id);
        $toutdateaffecter=DB::select("SELECT DISTINCT(datePrevueVacc) as dateAffectation FROM affecters UNION SELECT DISTINCT(datePrevueVacc) as dateAffectation FROM update_rdvs");
        $capacity=DB::select("SELECT * FROM unite_vaccinations where id='".$idd."'");
        $j=0;
       $cit=DB::select("SELECT * FROM citoyens c inner JOIN vacciners v on v.citoyen_id=c.id where c.id='".$id."'");
       //dd($cit);
        if($cit==[]){$etape='1ere_prise';}
        else{$etape='rappel';}
        //calendreier
           for($i=0;$i<sizeof($toutdateaffecter);$i++){
if($toutdateaffecter[$i]->dateAffectation<now()){
                 //  $nbvaccine=DB::select("SELECT count(*) as nbr FROM vacciners WHERE  dateVaccination='".$toutdateaffecter[$i]->dateAffectation."' and type_vaccin_id=1");
                 //  $nbvaccine2=DB::select("SELECT count(*) as nbr FROM vacciners WHERE  dateVaccination='".$toutdateaffecter[$i]->dateAffectation."' and type_vaccin_id=2");
                   $toutdatead=DB::select("select date_add('".$toutdateaffecter[$i]->dateAffectation."',interval 21 day) as datee from affecters");
                   $toutdateadui=DB::select("select date_add('".$toutdateaffecter[$i]->dateAffectation."',interval 28 day) as dateee from affecters");
                   $nombreaffacettdate=DB::select("SELECT count(*) as nbaffe FROM update_rdvs WHERE  datePrevueVacc='".$toutdatead[$j]->datee."'");
                   $nombreaffacettdate2=DB::select("SELECT count(*) as nbaffe FROM update_rdvs WHERE  datePrevueVacc='".$toutdateadui[$j]->dateee."'");
                   $nbrr=$capacity[0]->capacite-($nombreaffacettdate2[0]->nbaffe);
                   $nbr=$capacity[0]->capacite-($nombreaffacettdate[0]->nbaffe);
                   if($nbr==0){
                       $nbr=$capacity[0]->capacite;
                   }
                   if( $nombreaffacettdate[0]->nbaffe==0)
                   {
                       $nbr=0;
                   }
               if($nbrr==0){
                   $nbrr=$capacity[0]->capacite;
               }
               if($nombreaffacettdate2[0]->nbaffe==0)
               {
                   $nbrr=0;
               }
                   $dateviun=$toutdatead[$j]->datee;
                   $dateviunui=$toutdateadui[$j]->dateee;

                   $table []=array(
                       "dataAff"=>$dateviun,
                       "nbr"=>$nbr,
                       "capacite"=>$capacity[0]->capacite,
                       "datea"=>$toutdateaffecter[$i]->dateAffectation,
                       "dateu"=>$dateviunui,
                       "nbrr2"=>$nbrr
                   );
                  }
             else{
                 $toutdatead=DB::select("select date_add('".$toutdateaffecter[$i]->dateAffectation."',interval 21 day) as datee from affecters");
                 $toutdateadui=DB::select("select date_add('".$toutdateaffecter[$i]->dateAffectation."',interval 28 day) as dateee from affecters");
                 $dateviun=$toutdatead[$j]->datee;
                 $dateviunui=$toutdateadui[$j]->dateee;

                 $table []=array(
                     "dataAff"=>$dateviun,
                     "nbr"=>$capacity[0]->capacite,
                     "capacite"=>$capacity[0]->capacite,
                     "datea"=>$toutdateaffecter[$i]->dateAffectation,
                     "dateu"=>$dateviunui,
                     "nbrr2"=>$capacity[0]->capacite,
                 );
             }
           }


       //dd($table);
        return view('unite.calendrier')->with([
            'table'=>$table,
            'id'=>$id,
            'info'=>$info,
            'unitenom'=>$unitenom,
            'etape'=>$etape

        ]);
    }

 public function calendrier($id)
    {

        $idunite=Auth::user()->id;

        $table []=array("nbr"=>"","capacite"=>"","datea"=>"");
//info
        $uniteconnect=DB::select("select unite_vaccination_id	 as idd from user_unites where user_id=".$idunite);
        $idd=$uniteconnect[0]->idd;
        $nomunite=DB::select("select nomUnite	 as nom from unite_vaccinations where id='".$idd."'");
        $unitenom=$nomunite[0]->nom;
        $info=DB::select("SELECT * FROM citoyens  where id=".$id);
        $toutdateaffecter=DB::select("SELECT DISTINCT(datePrevueVacc) as dateAffectation FROM update_rdvs ");
        $capacity=DB::select("SELECT * FROM unite_vaccinations where id='".$idd."'");
        $j=0;
        $cit=DB::select("SELECT * FROM citoyens c inner JOIN vacciners v on v.citoyen_id=c.id where c.id='".$id."'");
        //dd($cit);
        if($cit==[]){$etape='1ere_prise';}
        else{$etape='rappel';}
        //calendreier
        for($i=0;$i<sizeof($toutdateaffecter);$i++){
            if($toutdateaffecter[$i]->dateAffectation>now()){
                $nombreaffacettdate=DB::select("SELECT count(*) as nbaffe FROM update_rdvs WHERE  datePrevueVacc='".$toutdateaffecter[$i]->dateAffectation."'");

                $nbr=$capacity[0]->capacite-($nombreaffacettdate[0]->nbaffe);
                if($nbr==0){
                    $nbr=$capacity[0]->capacite;
                }

                $table []=array(
                    "nbr"=>$nbr,
                    "capacite"=>$capacity[0]->capacite,
                    "datea"=>$toutdateaffecter[$i]->dateAffectation,
                );
            }

        }


     //  dd($table);
        return view('unite.calendrier')->with([
            'table'=>$table,
            'id'=>$id,
            'info'=>$info,
            'unitenom'=>$unitenom,
            'etape'=>$etape

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
        $idunite=Auth::user()->id;


        $uniteconnect=DB::select("select unite_vaccination_id	 as idd from user_unites where user_id=".$idunite);
        $idd=$uniteconnect[0]->idd;
               $uniteconnect=DB::update("UPDATE update_rdvs SET datePrevueVacc='".$request->affecter."',unite_id='".$idd."',plage_horaire='".$request->plagehoraire."',etape='".$request->etape."' WHERE  citoyen_id='".$request->citoyen."'");
     
     
     $affecter=new Affecter();
        $affecter->id=time();
        $affecter->dateAffectation=$request->affecter;
        $affecter->citoyen_id=$request->citoyen;
        $affecter->unite_vaccination_id=$idd;
        $affecter->plage_horaire=$request->plagehoraire;
        $affecter->datePrevueVacc=$request->affecter;
        $affecter->save();
     
     
        return redirect("getCitoyens");
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
