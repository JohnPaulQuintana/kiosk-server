<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Floorplan;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    //get floor
    public function getFloor()
    {
        $floor = Floorplan::with('units')->get();
        return response()->json($floor);
    }

    //create teacher
    public function createTeacher(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'floor' => 'required|string', // Ensure floor ID exists in the floorplans table
            'unit' => 'required|exists:floorplan_units,id', // Ensure unit ID exists in the units table
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create and save the teacher
        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'floorplan_unit_id' => $request->unit,
            'floor' => $request->unit_id,
        ]);

        return response()->json([
            'message' => 'Teacher created successfully',
            'teacher' => $teacher
        ], 201);
    }

    // get teachers
    public function getTeacher()
    {
        $teachers = Teacher::join('floorplan_units', 'teachers.floorplan_unit_id','=','floorplan_units.id')
        ->join('floorplans', 'floorplan_units.floorplan_id', '=', 'floorplans.id')
        ->select('teachers.*','floorplan_units.unit','floorplans.floor')
        ->orderBy('teachers.created_at', 'desc')->get();

        return response()->json($teachers);
    }
}
