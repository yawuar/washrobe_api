<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

use App\Wardrobe;
use App\Item;
use App\User;
use App\UserItem;

use DB;

class WardrobeController extends Controller
{
    public function categories(Request $request) 
    {
        $gender = $request['gender'];
        $categories = Wardrobe::get();
        $newCategory = [];

        $user = Auth::user();
        $items = [];

        foreach($categories as $value) {
            $arr = explode(',', $value['gender']);
            if(count($arr) > 1) {
                $value['amount'] = count(User::find($user['id'])->items()->where('categoryID', $value['id'])->wherePivot('deleted_at', null)->get());
                array_push($newCategory, $value);
            } else if(count($arr) == 1) {
                if($arr[0] == $gender) {
                    $value['amount'] = count(User::find($user['id'])->items()->where('categoryID', $value['id'])->wherePivot('deleted_at', null)->get());
                    array_push($newCategory, $value);
                }
            }
        }
        
        return response()->json(['data' => $newCategory], 200); 
    }

    public function category($id) {
        // Add symbols to item
        $user = Auth::user();
        $items = User::find($user['id'])->items()->where('categoryID', '=', $id)->wherePivot('deleted_at', null)->orderBy('items.id')->select('*', DB::raw('COUNT(*) as amountOfItems'))->groupBy('item_id')->get();
        for($i = 0; $i < count($items); $i++) {
            $items[$i]['symbols'] = $items[$i]->symbols;
        }
        return response()->json(['data' => $items], 200); 
    }

    public function delete($id) {
        $item = UserItem::where('id', $id)->delete();
        return response()->json(['data' => $item], 200); 
    }
}
