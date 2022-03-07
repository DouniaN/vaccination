<?php

namespace App\Http\Controllers;

use App\Quartier;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class QuartierController extends Controller
{
    public function index()
    {
        $Quartier=DB::select("SELECT c.nomCommandement,cm.nomCommun,q.* FROM `quartiers` q INNER JOIN commandements c ON c.id=q.`commandement_id` INNER JOIN communs cm ON cm.id=q.`commun_id`");
        $commandement=DB::select("SELECT * FROM `commandements`");
        $commun=DB::select("SELECT * FROM `communs`");
        return view('admin.Quartier')->with([
            'Quartier'=>$Quartier,
            'commandement'=>$commandement,
            'commun'=>$commun
        ]);
    }

    public function store(Request $request)
    {
        $insertQuartier=DB::insert("INSERT INTO `quartiers`(`nomQuartier`, `commandement_id`, `commun_id`, `created_at`, `updated_at`) VALUES ('".$request->input('nomQuartier')."','".$request->input('commandement_id')."','".$request->input('commun_id')."','".now()."','".now()."')");
        return redirect('Quartier');
    }

    public function destroy($id)
    {
        $delete=DB::delete("DELETE FROM `quartiers` WHERE id='".$id."'");
        $Quartier=DB::select("SELECT c.nomCommandement,cm.nomCommun,q.* FROM `quartiers` q INNER JOIN commandements c ON c.id=q.`commandement_id` INNER JOIN communs cm ON cm.id=q.`commun_id`");
        $commandement=DB::select("SELECT * FROM `commandements`");
        $commun=DB::select("SELECT * FROM `communs`");

        return view('admin.Quartier')->with([
            'delete'=>$delete,
            'Quartier'=>$Quartier,
            'commandement'=>$commandement,
            'commun'=>$commun
        ]);
    }
}
