<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Wardrobe;

class WardrobeController extends Controller
{
    public function categories(Request $request) 
    {
        $gender = $request['gender'];
        $categories = Wardrobe::get();
        $newCategory = [];

        return response()->json(['data' => $request->all()]);

        foreach($categories as $value) {
            $arr = explode(',', $value['gender']);
            if(count($arr) > 1) {
                array_push($newCategory, $value);
            } else if(count($arr) == 1) {
                if($arr[0] == $gender) {
                    array_push($newCategory, $value);
                }
            }
        }

        return response()->json(['data' => $newCategory], 200); 
    }
}
