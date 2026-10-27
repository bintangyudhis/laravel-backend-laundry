<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Laundry;

class LaundryController extends Controller
{
    //
    function readAll()
    {

        $laundries = Laundry::with('user', 'shop')->get(); // eager loading user dan shop sesuai relasi di model Laundry

        return response()->json([
            'data' => $laundries
        ], 200);
    }


    function whereUserId($id)
    {

        $laundries = Laundry::where('user_id', '=', $id)
            ->with('user', 'shop')
            ->orderBy('created_at', 'desc')
            ->get();


        if (count($laundries) > 0) {
            return response()->json([
                'data' => $laundries,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not found',
                'data' => $laundries
            ], 404); // 404 Not Found
        }
    }

    function store(Request $request)
    {

        $this->validate($request, [
            'shop_id' => 'required|exists:shops,id',
            'weight' => 'required|numeric|min:0.1',
            'with_pickup' => 'required|boolean',
            'with_delivery' => 'required|boolean',
            'pickup_address' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'total' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // atribut diisi satu per satu karena daftar fillable model
        // tidak memuat kolom status dengan benar
        $laundry = new Laundry();
        $laundry->claim_code = strtolower(Str::random(8));
        $laundry->user_id = $request->user()->id;
        $laundry->shop_id = $request->shop_id;
        $laundry->weight = $request->weight;
        $laundry->with_pickup = $request->with_pickup;
        $laundry->with_delivery = $request->with_delivery;
        $laundry->pickup_address = $request->pickup_address ?? '';
        $laundry->delivery_address = $request->delivery_address ?? '';
        $laundry->total = $request->total;
        $laundry->description = $request->description ?? '';
        $laundry->status = 'Queue';
        $laundry->save();

        return response()->json([
            'data' => $laundry->load('user', 'shop'),
            'message' => 'Laundry created successfully'
        ], 201); // 201 Created
    }

    function claim(Request $request)
    {

        $laundry = Laundry::where([
            ['id', '=', $request->id],
            ['claim_code', '=', $request->claim_code]
        ])->first();

        if (!$laundry) {
            return response()->json([
                'message' => 'Not found',
            ], 404); // 404 Not Found
        }

        if ($laundry->user_id != 0) {
            return response()->json([
                'message' => 'Laundry has been claimed',
            ], 400); // 400 Bad Request
        }

        $laundry->user_id = $request->user_id;
        $updated = $laundry->save();

        if ($updated) {
            return response()->json([
                'data' => $updated,
            ], 201); // 201 Created
        } else {
            return response()->json([
                'message' => 'Can not be updated',
            ], 500); // 500 Internal Server Error
        }
    }
}
