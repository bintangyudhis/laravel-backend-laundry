<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
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
