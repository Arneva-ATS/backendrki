<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class XenditController extends Controller
{
    //
    public function create_invoice(Request $request) {
        $external_id = $request->external_id;
        $id = $request->id_pos;

    }
}
