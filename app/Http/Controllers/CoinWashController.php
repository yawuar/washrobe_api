<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CoinWash;

class CoinWashController extends Controller
{
    public function getCoinWash() {
        $coinwashes = CoinWash::get();

        return response()->json(['data' => $coinwashes]);
    }
}
