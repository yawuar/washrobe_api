<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Wardrobe;

class WardrobeController extends Controller
{
    public function categories() 
    {
        $categories = Wardrobe::get();

        return response()->json(['data' => $categories]); 
    }
}
