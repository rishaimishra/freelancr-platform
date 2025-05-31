<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function getByPais($pais_id)
{
    $provincias = Provincia::where('fk_pais', $pais_id)->get();
    return response()->json($provincias);
}
}
