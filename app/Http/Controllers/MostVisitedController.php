<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MostVisited;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MostVisitedController extends Controller
{
    //insert the count
    public function clickedUnit (Request $request)
    {

        MostVisited::create([
            'floorplan_unit_id' => $request->id,
            'clicked' => 1,
        ]);

        return response()->json(['message'=>'successfully inserted!']);
    }

    //get analytics
    public function analyticsForMostVisited ()
    {
        $mostVisited = MostVisited::join('floorplan_units', 'most_visiteds.floorplan_unit_id', '=', 'floorplan_units.id')
        ->join('floorplans', 'floorplan_units.floorplan_id', '=', 'floorplans.id')
        ->select(
            'floorplans.floor as floor',
            'floorplan_units.unit as room',
            DB::raw('SUM(most_visiteds.clicked) as visits'),
            DB::raw('DATE(most_visiteds.created_at) as date')
        )
        ->groupBy('floorplans.floor', 'floorplan_units.unit', 'date')
        ->get();
        return response()->json($mostVisited);
    }
}
