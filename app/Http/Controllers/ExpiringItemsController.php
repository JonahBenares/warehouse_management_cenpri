<?php

namespace App\Http\Controllers;
use App\Models\Items;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ExpiringItemsController extends Controller
{
    public function expiringSoon()
    {
        $today = Carbon::today();
        $sevenDaysFromNow = Carbon::today()->addDays(7);

        // Get items that have at least 1 variant with a non-null expiration date within the next 7 days (including today)
        $items = Items::whereHas('variants', function ($query) use ($today, $sevenDaysFromNow) {
            $query->whereNotNull('expiration')
                  ->whereBetween('expiration', [$today, $sevenDaysFromNow]);
        })->with(['variants' => function ($query) use ($today, $sevenDaysFromNow) {
            $query->whereNotNull('expiration')
                  ->whereBetween('expiration', [$today, $sevenDaysFromNow]);
        }])->get();

        return response()->json($items);
    }
}