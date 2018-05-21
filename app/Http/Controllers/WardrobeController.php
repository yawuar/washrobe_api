<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Wardrobe;
use App\Item;

class WardrobeController extends Controller
{
    public function categories(Request $request) 
    {
        $gender = $request['gender'];
        $categories = Wardrobe::get();
        $newCategory = [];

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

    public function category($id) {
        $items = Item::where('categoryID', $id)->get();
        return response()->json(['data' => $items], 200); 
    }

    public function delete($id) {
        $items = Item::where('id', $id)->delete();
        return response()->json(['data' => $items], 200); 
    }
}
