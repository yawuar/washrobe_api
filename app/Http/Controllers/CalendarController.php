<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\CalendarItem;
use App\User;
use App\Wardrobe;

class CalendarController extends Controller
{
    public function getClothesOfUserByDay($day) {
        $userId = Auth::user()['id'];
        $items = CalendarItem::whereDate('date', $day)->get();

        $calendarItems = [];

        if(count($items) > 0) {
            foreach($items as $item) {
                $userItem = User::find($userId)->items()->wherePivot('id', $item['user_itemID'])->wherePivot('deleted_at', null)->first();
                if($userItem != null) {
                    $userItem['type'] = $userItem->wardrobe()->first()['name'];
                    array_push($calendarItems, $userItem);
                }
            }
        }

        return response()->json(['data' => $calendarItems]);
    }

    public function addClothToCalendar(Request $request) {
        $calendarItem = CalendarItem::create($request->all());
        return response()->json(['data' => $calendarItem]);
    }

    public function removeItemFromCalendar(Request $request) {
        $uiID = $request['user_itemID'];
        $date = $request['date'];

        $item = CalendarItem::where('user_itemID', $uiID)->whereDate('date', $date)->first();
    
        if($item) {
            $item->delete();
        }

        if(!$item) {
            $item = false;
        }

        return response()->json(['data' =>$item ]);

    }
}
