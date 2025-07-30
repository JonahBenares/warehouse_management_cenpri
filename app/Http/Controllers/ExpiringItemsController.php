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
        $nextWeek = Carbon::today()->addWeek();

        // Items that have at least 1 variant expiring within 7 days
        $items = Items::whereHas('variants', function ($query) use ($today, $nextWeek) {
            $query->whereBetween('expiration_date', [$today, $nextWeek]);
        })->with(['variants' => function ($query) use ($today, $nextWeek) {
            $query->whereBetween('expiration_date', [$today, $nextWeek]);
        }])->get();

        return response()->json($items);
    }
}
