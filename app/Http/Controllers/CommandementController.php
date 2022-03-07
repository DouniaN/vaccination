<?php

namespace App\Http\Controllers;

use App\Commandement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CommandementController extends Controller
{
    public function index()
    {
        $commandemant=DB::select("SELECT p.nomPachalik,c.* FROM `commandements` c INNER JOIN pachaliks p ON p.id=c.`pachalik_id`");
        $pachalik=DB::select("SELECT * FROM `pachaliks`");
        return view('admin.commandement')->with([
            'commandemant'=>$commandemant,
            'pachalik'=>$pachalik
        ]);
    }

    public function store(Request $request)
    {
        $insertCommand=DB::insert("INSERT INTO `commandements`(`nomCommandement`, `pachalik_id`, `created_at`, `updated_at`) VALUES ('".$request->input('nomCommandement')."','".$request->input('pachalik_id')."','".now()."','".now()."')");
        return redirect('commandement');
    }

}
