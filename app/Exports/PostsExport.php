<?php

namespace App\Exports;

use App\Citoyen_non_verified;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class PostsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $cit= Citoyen_non_verified::all();

        $cit_array[] = array('id', 'cin', 'nom', 'prenom', 'nomAR', 'prenomAR', 'dateNaissance', 'sexe', 'adresse', 'email', 'profession','lieu_travail','maladie_chronique','maladie_cardiaque','maladie_foie','hypertention','cancer','enceinte','sufisance_renal','maladie_resperatoire','sys_uminitaire','nomQuartier','nomCommun','nomCommandement','vaccin_grippal','date_vaccGripe');


        foreach($cit as $item)
        {
            //dd($item->quartier_id);
            $req=DB::select('select q.nomQuartier,cu.nomCommun,c.nomCommandement from quartiers q inner join commandements c on c.id = q.commandement_id  inner join communs cu on cu.id= q.commun_id where q.id = '.$item->quartier_id );

            $cit_array[] = array(
            'id'  => $item->id,
            'cin'   => $item->cin,
            'nom'    => $item->nom,
            'prenom'  => $item->prenom,
            'nomAR'   => $item->nomAR,
            'prenomAR'   => $item->prenomAR,
            'dateNaissance'   => $item->dateNaissance,
            'sexe'   => $item->sexe,
            'adresse'   => $item->adresse,
            'email'   => $item->email,
            'profession'   => $item->profession,
            'lieu_travail'=> $item->lieu_travail,
             'maladie_chronique'=>$item->maladie_chronique,
             'maladie_cardiaque'=>$item->maladie_cardiaque,
             'maladie_foie'=>$item->maladie_foie,
             'hypertention'=>$item->hypertention,
             'cancer'=>$item->cancer,
             'enceinte'=>$item->enceinte,
             'sufisance_renal'=>$item->sufisance_renal	,
             'maladie_resperatoire'=>$item->maladie_resperatoire,
             'sys_uminitaire'=>$item->sys_uminitaire,
             'nomQuartier'=>$req[0]->nomQuartier,
             'nomCommun'=>$req[0]->nomCommun,
             'nomCommandement'=>$req[0]->nomCommandement,
             'vaccin_grippal'=>$item->vaccin_grippal,
             'date_vaccGripe'=>$item->date_vaccGripe

            );
        }

        return collect($cit_array);
    }
}
