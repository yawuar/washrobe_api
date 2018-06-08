<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\CalendarItem;
use App\User;

class CalendarController extends Controller
{
    public function getClothesOfUserByDay($day) {
        $userId = Auth::user()['id'];
        $items = CalendarItem::whereDate('created_at', $day)->get();

        $calendarItems = [];

        if(count($items) > 0) {
            foreach($items as $item) {
                $calendarItems = User::find($userId)->items()->wherePivot('id', $item['user_itemID'])->first();
            }
        }

        return response()->json(['data' => $calendarItems]);
    }
}
