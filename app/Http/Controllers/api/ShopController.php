<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop;

class ShopController extends Controller
{
    function readAll(){

        $shops = Shop::all();

        return response()->json([
            'data' => $shops
        ], 200);

    }

    function readRecommendation(){

        $shops = Shop::orderBy('rate', 'desc')
        ->limit(5)
        ->get();

        if(count($shops) > 0){
            return response()->json([
                'data' => $shops,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not found',
                'data' => $shops
            ], 404); // 404 Not Found
        }
    }

    function searchByCity($name){

        $shops = Shop::where('city', 'like', '%' . $name. '%') // search city by nama citynya
        ->orderBy('name') // nama shop secara alfabet
        ->get();


        if(count($shops) > 0){
            return response()->json([
                'data' => $shops,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not found',
                'data' => $shops
            ], 404); // 404 Not Found
        }

    }

}
