<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Citoyen;
use App\Affecter;
use Illuminate\Support\Facades\Validator;
use App\Exports\PostsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DateTime;

class AffecterCitoyen extends Controller
{
    /**
     * .return page affecter citoyen
     *
     * @return \Illuminate\Http\Response
     */
    public function affecterCitoyen()
    {
        $user    = auth()->user();
        $id_user = $user->id;



       $unite_id = DB::table('user_unites')
       ->join('unite_vaccinations', 'user_unites.unite_vaccination_id', '=', 'unite_vaccinations.id')
       ->select('user_unites.unite_vaccination_id','unite_vaccinations.nomUnite')
       ->where('user_unites.user_id',$id_user)
       ->get();

       $unite_vaccination_id=$unite_id[0]->unite_vaccination_id;
       $unite_vaccination=$unite_id[0]->nomUnite;
       $communs=DB::select("SELECT * FROM `communs` ");

       return view('unite.Affecter_Citoyen')->with([
        'communs'      =>$communs,
        'unite_vaccination' =>$unite_vaccination,
        'unite_vaccination_id'=>$unite_vaccination_id
      ]);


    }
    public function addC(Request $request)
    {
        $cit= request()->validate([
            'cin' =>'required',
            'nom' =>'required',
             'prenom'=>'required',
             'nomAr' =>'required',
             'prenomAr'=>'required',
             'dateNaissance'=>'required',
             'sexe'=>'required',
             'adresse'=>'required',
             'tel'=>'required',
             'email'=>'required | email',
             'profession'=>'required',
             'quartier_id'=>'sometimes',
             'lieu_travail'=>'required',
             'maladie_chronique'=>'sometimes',
             'maladie_foie'=>'sometimes',
             'hypertention'=>'sometimes',
             'cancer'=>'sometimes',
             'enceinte'=>'sometimes',
             'sufisance_renal'=>'sometimes',
             'maladie_resperatoire'=>'sometimes',
             'sys_uminitaire'=>'sometimes',
             'vaccin_grippal'=>'required',
             'date_vaccGripe'=>'sometimes',
             'source'=>'required'
            ]);

        $Citoyen_non_verified = Citoyen::create($cit);

       $GetCitoyenID = DB::select("select max(id) as idCitoyen from citoyens");
       $CitoyenID    = $GetCitoyenID[0]->idCitoyen;

       $Aff=new Affecter;
       $Aff->dateAffectation = new \DateTime('NOW');

       $Aff->datePrevueVacc=$request->input('dateAffecter');
       $Aff->citoyen_id=$CitoyenID;
       $Aff->unite_vaccination_id=$request->input('id_unite');
       $Aff->plage_horaire    =$request->input('plage_horaire');
       $Aff->save();

        return redirect('affecterCitoyen')->with("success","test");
    }
}
