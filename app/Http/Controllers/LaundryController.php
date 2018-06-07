<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

use App\User;
use App\Wardrobe;
use App\Laundry;
use App\Item;

class LaundryController extends Controller
{
    private $laundryArr = [
        'information' => [

        ],
        'laundry' => [
            'white' => [],
            'black' => [],
            'coloured' => []
        ]
    ];

    public function getLaundryByCategory() {
        $items = Laundry::get();
        return response()->json(['data' => $items]);
    }

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
                $items = Laundry::get();
                $count = 0;
                foreach($items as $item) {
                    if(count(User::find($user['id'])->items->where('categoryID', $value['id'])->where('pivot.id', $item['user_itemID'])) > 0) {
                        $count++;
                    }
                }
                $value['amount'] = $count;
                array_push($newCategory, $value);
            } else if(count($arr) == 1) {
                if($arr[0] == $gender) {
                    $items = Laundry::get();
                    $count = 0;
                    foreach($items as $item) {
                        if(count(User::find($user['id'])->items->where('categoryID', $value['id'])->where('pivot.id', $item['user_itemID'])) > 0) {
                            $count++;
                        }
                    }
                    $value['amount'] = $count;
                }
            }
        }
        
        return response()->json(['data' => $newCategory], 200); 
    }

    public function putInLaundry($id) {
        $user = Auth::user();
        $items = User::find($user['id'])->items()->where('item_id', $id)->get();

        $message = 'false';

        $index = 0;
        $length = count($items);

        // if there is only one item add in db
        if($length == 1) {
            $pivotID = $items[0]->pivot->id;
            $checkIfIsLaundry = Laundry::where('user_itemID', $pivotID)->get();
            if(count($checkIfIsLaundry) > 0) {
                $message = 'This item is already in the laundry';
            } else {
                Laundry::create(['user_itemID' => $pivotID]);
                $message = 'The item is successfully added to the laundry';
            }
        }
        
        if($length > 1) {
            if($index <= $length) {
                for($i = 0; $i < $length; $i++) {
                    $pivotID = $items[$index]->pivot->id;
                    $checkIfIsLaundry = Laundry::where('user_itemID', $pivotID)->get();
                    if(count($checkIfIsLaundry) > 0) {
                        $index++;
                    } else {
                        Laundry::create(['user_itemID' => $pivotID]);
                        $message = 'The item is successfully added to the laundry';
                        break;
                    }
                }
            } else {
                $message = 'All these items are already in the laundry';
            }
        }

        return response()->json(['data' => $message]);
    }

    public function getLaundryById($id) {
        $user = Auth::user();
        $items = [];

        $laundry = Laundry::get();
        foreach($laundry as $laundryItem) {
            if(!$laundryItem['isWashed']) {
            $laundryItem = User::find($user['id'])->items->where('categoryID', $id)->where('pivot.id', $laundryItem['user_itemID'])->first();
                if($laundryItem != null) {
                    $laundryItem['symbols'] = $laundryItem->symbols;
                    array_push($items,$laundryItem);

                }
            }
        }

        return response()->json(['data' => $items]);
    }

    public function deleteLaundryById($id) {
        $laundry = Laundry::where('user_itemID', $id)->delete();
        return response()->json(['data' => $laundry]);
    }

    public function getLaundryByUser() {
        $user = Auth::user();

        $items = [];

        $laundry = Laundry::get();
        foreach($laundry as $laundryItem) {
            if(!$laundryItem['isWashed']) {
                $item = User::find($user['id'])->items()->wherePivot('id', $laundryItem['user_itemID'])->first();
                if($item != null) {
                    array_push($items, $item);
                }
            }
        }

        return response()->json(['data' => count($items)]);
    }

    private function addTypeAndDegrees() {
        $arr = $this->laundryArr;
        $user = Auth::user();

        $laundry = Laundry::get();
        foreach($laundry as $laundryItem) {
            if(!$laundryItem['isWashed']) {
                $item = User::find($user['id'])->items()->wherePivot('id', $laundryItem['user_itemID'])->first();
                if(isset($item->symbols)) {
                    $item['symbols'] = $item->symbols;
                    $color = explode(',', $item['color'])[0];
                    foreach($item['symbols'] as $symbol) {
                        if($symbol['type'] == 'wash' || $symbol['type'] == 'hand-wash') {
                            $arr['laundry'][$color][$symbol['type']][$symbol['degrees']] = [];
                        }
                    }
                }
            }
        }
        return $arr;
    }

    private function addMaterialToDegrees() {
        $arr = $this->addTypeAndDegrees();
        $user = Auth::user();

        $laundry = Laundry::get();
        foreach($laundry as $laundryItem) {
            if(!$laundryItem['isWashed']) {
                $item = User::find($user['id'])->items()->wherePivot('id', $laundryItem['user_itemID'])->first();
                if(isset($item->symbols)) {
                    $item['symbols'] = $item->symbols;
                    $color = explode(',', $item['color'])[0];
                    
                    foreach($item['symbols'] as $symbol) {
                        if($symbol['type'] == 'wash' || $symbol['type'] == 'hand-wash') {
                            $arr['laundry'][$color][$symbol['type']][$symbol['degrees']][$item['material']] = [];
                        }
                    }
                }
            }
        }
        return $arr;
    }

    public function sort() {
        $arr = $this->addMaterialToDegrees();
        $user = Auth::user();

        $laundry = Laundry::get();
        foreach($laundry as $laundryItem) {
            if(!$laundryItem['isWashed']) {
                $item = User::find($user['id'])->items()->wherePivot('id', $laundryItem['user_itemID'])->first();
                $obj = User::find($user['id'])->items()->wherePivot('id', $laundryItem['user_itemID'])->first();

                if(isset($item->symbols)) {
                    $item['symbols'] = $item->symbols;
                    $color = explode(',', $item['color'])[0];
                    foreach($item['symbols'] as $symbol) {
                        if($symbol['type'] == 'wash' || $symbol['type'] == 'hand-wash') {
                            array_push($arr['laundry'][$color][$symbol['type']][$symbol['degrees']][$item['material']], $obj);
                        }
                    }
                }
            }
        }

        return response()->json(['data' => $arr]);
    }
}
