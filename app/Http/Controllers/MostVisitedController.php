<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FloorplanUnit;
use App\Models\MostVisited;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\TeacherVisitNotification;
use Illuminate\Support\Facades\Notification;

class MostVisitedController extends Controller
{
    //insert the count
    public function clickedUnit (Request $request)
    {
        $teacher = '';
        // Check if teacher_id is invalid (null, empty, or 0)
        if ($request->teacher_id === null || $request->teacher_id === '' || $request->teacher_id == 0) {
            // Set teacher_id to a default value, for example, 0 or handle as needed
            $teacher_id = 0;
        } else {
            // Otherwise, use the provided teacher_id
            $teacher_id = $request->teacher_id;
            // Find the teacher by ID
            $teacher = Teacher::find($teacher_id);
             // Check if the teacher exists
            if ($teacher) {
                // Send the notification to the teacher's email
                Notification::route('mail', $teacher->email)
                            ->notify(new TeacherVisitNotification($teacher));

                // return response()->json(['message' => 'Notification sent successfully']);
            }
        }

        MostVisited::create([
            'floorplan_unit_id' => $request->id,
            'clicked' => 1,
        ]);

        // $units = FloorplanUnit::where('id',$request->id);
        // // Push the unit name to the teacher object
        // $teacher->unit = $units; // Assuming 'name' is the column in the floorplan_units table

        return response()->json(['message'=>'successfully inserted!','teacher'=>$teacher]);
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
