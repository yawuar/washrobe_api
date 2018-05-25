<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

use App\User;

class ItemController extends Controller
{
    public function addItemToUser($item_id) {
        $user = Auth::user();

        // because attach method only returns a NULL
        // I have to try and catch this for security reasons
        // So, if there is something wrong with the query return a boolean
        try {
            // attach itemID to user_item table
            $addedUser = User::find($user['id'])->items()->attach($item_id);
        } catch(\Illuminate\Database\QueryException $ex) {
            // when query fails, return a false
            $addedUser = false;
        }

        return response()->json(['data' => $addedUser]);
    }
}
