<?php

namespace App\Http\Controllers;

use App\Pachalik;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PachalikController extends Controller
{
    public function index()
    {
        $pachalik=DB::select("SELECT pr.nomProvince,p.*,p.id as 'idp' FROM `pachaliks` p INNER JOIN provinces pr ON pr.id=p.`province_id`");
        $province=DB::select("SELECT * FROM `provinces`");
        return view('admin.pachaliks')->with([
            'pachalik'=>$pachalik,
            'province'=>$province
        ]);
    }
    public function store(Request $request)
    {
        $insertPachalik=DB::insert("INSERT INTO `pachaliks`(`nomPachalik`, `province_id`, `created_at`, `updated_at`) VALUES ('".$request->input('nomPachalik')."','".$request->input('province_id')."','".now()."','".now()."')");
        return redirect('pachalik');
    }

    public function destroy($id)
    {
        $delete=DB::delete("DELETE FROM `pachaliks` WHERE id='".$id."'");
        $pachalik=DB::select("SELECT pr.nomProvince,p.* FROM `pachaliks` p INNER JOIN provinces pr ON pr.id=p.`province_id`");
        $province=DB::select("SELECT * FROM `provinces`");

        return view('admin.pachaliks')->with([
            'delete'=>$delete,
            'pachalik'=>$pachalik,
            'province'=>$province
        ]);
    }
}
