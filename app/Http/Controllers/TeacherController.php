<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Floorplan;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    //get floor
    public function getFloor()
    {
        $floor = Floorplan::with('units')->get();
        return response()->json($floor);
    }

    // //create teacher
    // public function createTeacher(Request $request)
    // {
    //     // Validate request data
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:teachers,email',
    //         'floor' => 'required|string', // Ensure floor ID exists in the floorplans table
    //         'unit' => 'required|exists:floorplan_units,id', // Ensure unit ID exists in the units table
    //     ]);

    //     // If validation fails, return errors
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Create and save the teacher
    //     $teacher = Teacher::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'floorplan_unit_id' => $request->unit,
    //         'floor' => $request->unit_id,
    //     ]);

    //     return response()->json([
    //         'message' => 'Teacher created successfully',
    //         'teacher' => $teacher
    //     ], 201);
    // }



    public function createTeacher(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'floor' => 'required|string', // Ensure floor ID exists in the floorplans table
            'unit' => 'required|exists:floorplan_units,id', // Ensure unit ID exists in the units table
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048', // Add validation for file (image/PDF)
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle the file upload if there's a file
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Store the file in the public directory and get the path
            $filePath = $file->store('teachers_files', 'public');
        }

        // Create and save the teacher
        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'floorplan_unit_id' => $request->unit,
            'floor' => $request->floor, // Update to use 'floor' directly
            'file_path' => $filePath, // Store the file path
        ]);

        return response()->json([
            'message' => 'Teacher created successfully',
            'teacher' => $teacher
        ], 201);
    }

    // Update an existing teacher
    public function updateTeacher(Request $request, $id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $id,
            'floor' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpeg,png,jpg',
        ]);

        // Handle file upload if present
        $filePath = $teacher->file;
        if ($request->hasFile('file')) {
            // Delete old file if it exists
            if ($filePath && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // Store new file
            $filePath = $request->file('file')->store('teacher_files', 'public');
        }

        // Update teacher details
        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
            'floor' => $request->floor,
            'floorplan_unit_id' => $request->unit,
            'file_path' => $filePath, // Update file path if new file was uploaded
        ]);

        return response()->json($teacher, 200);
    }

    // get teachers
    public function getTeacher()
    {
        $teachers = Teacher::join('floorplan_units', 'teachers.floorplan_unit_id', '=', 'floorplan_units.id')
            ->join('floorplans', 'floorplan_units.floorplan_id', '=', 'floorplans.id')
            ->select('teachers.*', 'floorplan_units.unit', 'floorplans.floor')
            ->orderBy('teachers.created_at', 'desc')->get();

        return response()->json($teachers);
    }

    public function deleteTeacher($id)
    {
        // Find the teacher by ID
        $teacher = Teacher::find($id);

        // Check if the teacher exists
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        // Delete the file if it exists
        if ($teacher->file && Storage::exists($teacher->file)) {
            Storage::delete($teacher->file);
        }

        // Delete the teacher record from the database
        $teacher->delete();

        // Return a success response
        return response()->json(['message' => 'Teacher deleted successfully'], 200);
    }
}
