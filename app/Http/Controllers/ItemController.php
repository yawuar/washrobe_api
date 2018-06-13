<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Hashids\Hashids;
use App\User;
use App\Item;

use DB;

class ItemController extends Controller
{
    public function addItemToUser($item_id) {
        $user = Auth::user();
        $hashids = new Hashids('appelblauwzeegroen', 10);
        $id = $hashids->decode($item_id);

        // because attach method only returns a NULL
        // I have to try and catch this for security reasons
        // So, if there is something wrong with the query it returns a boolean
        try {
            // attach itemID to user_item table
            $addedUser = User::find($user['id'])->items()->attach($id);
            $item = DB::table('user_item')->where('user_id', $user['id'])->orderBy('created_at', 'desc')->first();
        } catch(\Illuminate\Database\QueryException $ex) {
            // when query fails, return a false
            $addedUser = false;
        }

        return response()->json([
            'success' => $addedUser, 
            'data' => $item
        ]);
    }

    public function encodeItem($item_id) {
        // encode itemID
        $hashids = new Hashids('appelblauwzeegroen', 10);
        $hash = $hashids->encode($item_id);
        return response()->json(['data' => $hash]);
    }

    public function getItemById($item_id) {
        $item = Item::where('id', $item_id)->get();
        return response()->json(['data' => $item]);
    }

    public function getItemByHash($hash) {
        $hashids = new Hashids('appelblauwzeegroen', 10);
        $id = $hashids->decode($hash);

        $item = Item::where('id', $id)->get();
        return response()->json(['data' => $item]);
    }
}
