<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Foundation\Auth\User;

class PromoController extends Controller
{
    function readAll(){

        $promos = Promo::with('shop')->get();

        return response()->json([
            'data' => $promos
        ], 200);

    }

    function readLimit(){

        // menampilkan promo terbaru dengan limit 5
        $promos = Promo::orderBy('created_at', 'desc')
        ->limit(5)
        ->with('shop')
        ->get();

        if(count($promos) > 0){
            return response()->json([
                'data' => $promos,
            ], 200);
        } else {
            return response()->json([
                'message' => 'No promos found',
                'data' => $promos
            ], 404); // 404 Not Found
        }
    }

}
