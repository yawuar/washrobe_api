<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

use App\User;

use App\Laundry;

class LaundryController extends Controller
{
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
                    }
                }
            } else {
                $message = 'All these items are already in the laundry';
            }
        }

        return response()->json(['data' => $message]);
        // $laundry = Laundry::
    }
}
