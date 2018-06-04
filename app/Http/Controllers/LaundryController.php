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
            $laundryItem = User::find($user['id'])->items->where('categoryID', $id)->where('pivot.id', $laundryItem['user_itemID'])->first();
            if($laundryItem != null) {
                $laundryItem['symbols'] = $laundryItem->symbols;
                array_push($items,$laundryItem);

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
            $item = User::find($user['id'])->items()->wherePivot('id', $laundryItem['user_itemID'])->first();
            if($item != null) {
                array_push($items, $item);
            }
        }

        return response()->json(['data' => count($items)]);
    }

    private function sortLaundryBycolor() {
        // keep an array to save all items
        $user = Auth::user();
        $items = [];

        // array to sort all laundry by color
        $clothesColors = [
            'white' => [],
            'black' =>[],
            'coloured' => []
        ];

        // get all laundry 
        $laundry = Laundry::get();

        $clothing = Item::get();

        // loop through all laundry items
        foreach($laundry as $laundryItem) {
            // get all items that belongs to the authenticated user
            $item = User::find($user['id'])->items()->wherePivot('id', $laundryItem['user_itemID'])->first();
            $item['symbols'] = $item->symbols;
            $colors = explode(',', $item['color']);
            if(count($colors) > 1) {
                $coloured = $colors[0];
                if($coloured == 'coloured') {
                    array_push($clothesColors[$coloured], $item);
                }
            }

            if(count($colors) <= 1) {
                if($item['color']) {
                    array_push($clothesColors[$item['color']], $item);
                }
            }
        }

        return $clothesColors;
    }

    private function sortLaundryByDegrees() {
        $itemSortedByColor = $this->sortLaundryBycolor();

        $itemsSortedByDegrees = [];

        foreach($itemSortedByColor as $key => $value) {
            foreach($itemSortedByColor[$key] as $symbol) {
                if(!isset($itemsSortedByDegrees[$key])) {
                    $itemsSortedByDegrees[$key] = [];
                }
                foreach($symbol['symbols'] as $sym) {
                    if($sym['type'] == 'wash' || $sym['type'] == 'hand-wash') {
                        if(!isset($itemsSortedByDegrees[$key][$sym['type']]) && !isset($itemsSortedByDegrees[$key][$sym['type']][$sym['degrees']]) && !isset($itemsSortedByDegrees[$key][$sym['type']][$sym['degrees']][$symbol['material']])) {
                            $itemsSortedByDegrees[$key][$sym['type']] = [];
                            $itemsSortedByDegrees[$key][$sym['type']][$sym['degrees']] = [];
                            $itemsSortedByDegrees[$key][$sym['type']][$sym['degrees']][$symbol['material']] = [];
                        }

                        if(isset($itemsSortedByDegrees[$key][$sym['type']][$sym['degrees']][$symbol['material']])) {
                            array_push($itemsSortedByDegrees[$key][$sym['type']][$sym['degrees']][$symbol['material']], $symbol['id']);
                        }
                    }
                }
            }
        }

        return $itemsSortedByDegrees;
    }

    public function sort() {
        $itemSortedByColor = $this->sortLaundryByDegrees();

        return response()->json(['data' => $itemSortedByColor]);
    }
}
