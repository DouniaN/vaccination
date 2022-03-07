<?php

namespace App\Http\Controllers;

use App\Commun;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CommunController extends Controller
{
    public function index()
    {
        $communs=DB::select("SELECT p.nomProvince,c.* FROM `communs` c INNER JOIN provinces p ON p.id=c.`province_id`");
        $province=DB::select("SELECT * FROM `provinces`");
        return view('admin.Commune')->with([
            'communs'=>$communs,
            'province'=>$province
        ]);
    }
    public function store(Request $request)
    {
        $insertCommune=DB::insert("INSERT INTO `communs`(`nomCommun`, `province_id`, `created_at`, `updated_at`) VALUES ('".$request->input('nomCommun')."','".$request->input('province_id')."','".now()."','".now()."')");
        return redirect('communes');
    }

    public function destroy($id)
    {
        $delete=DB::delete("DELETE FROM `communs` WHERE id='".$id."'");
        $communs=DB::select("SELECT p.nomProvince,c.* FROM `communs` c INNER JOIN provinces p ON p.id=c.`province_id`");
        $province=DB::select("SELECT * FROM `provinces`");

        return view('admin.Commune')->with([
            'delete'=>$delete,
            'communs'=>$communs,
            'province'=>$province
        ]);
    }
}
