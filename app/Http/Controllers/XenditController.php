<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class XenditController extends Controller
{
    //
    public function create_invoice(Request $request)
    {
        $external_id = $request->externalId;
        $id = $request->id_pos;
        $update_invoice = DB::table('tr_pos_master')
            ->where('id', $id)
            ->update([
                'external_id' => $external_id,
            ]);
        if (!$update_invoice) {
            return response()->json([
                "response_code" => "01",
                "response_message" => "Fail",
            ]);
        }
        return response()->json([
            'response_code' => "00",
            "response_message" => "OK",
        ]);
    }
}
