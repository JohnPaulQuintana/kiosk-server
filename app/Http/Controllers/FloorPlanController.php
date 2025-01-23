<?php

namespace App\Http\Controllers;

use App\Models\Floorplan;
use App\Models\FloorplanUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FloorPlanController extends Controller
{
    public function uploadFloorPlan(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:svg|max:2048', // Max file size 2MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Unique file name
            // Store the file in the 'maps' directory inside 'public'
            $filePath = $file->storeAs('public/maps', $fileName);

            return response()->json([
                'message' => 'File uploaded successfully!',
                'fileName' => $fileName,
                'filePath' => Storage::url($filePath),
            ]);
        }

        return response()->json(['message' => 'No file uploaded.'], 400);
    }

    // save facilities
    public function storeFacilites(Request $request)
    {
        $request->validate([
            'uploadedFile' => 'required|array|min:1', // Ensure it's a non-empty array
            'floor' => 'required',              // Ensure it's not null
            'facilities' => 'required|array|min:1',       // Ensure it's a non-empty array
        ]);
        $uploadedFile = $request->input('uploadedFile');
        $groups = $request->input('facilities');
        $floor = $request->input('floor');
        
        $initial = Floorplan::create([
            "floor"=>$floor,
            "filename"=>$uploadedFile[0]['fileName'],
            "filepath"=>$uploadedFile[0]['filePath']
        ]);

        if($initial){
            // save the units for this floor
            foreach ($groups as $key => $group) {
                FloorplanUnit::create([
                    'floorplan_id'=> $initial->id,
                    'unit'=>$group['name'],
                    'door'=>$group['id'],
                    'availability'=>$group['availability'],
                    'old_unit'=>$group['name'],
                ]);
            }
            return response()->json([
                'floor' => $floor,
                'facilities' => $groups,
                'uploadedFile' => $uploadedFile,
                'message' => 'Facilities saved successfully!',
            ]);
        }else{
            return response()->json([
                'floor' => $floor,
                'facilities' => $groups,
                'uploadedFile' => $uploadedFile,
                'message' => 'Something went wrong on the server or it\'s not valid information!',
            ]);
        }
        
    }

    public function unitCollections(Request $request)
    {
        // Fetch paginated data and include the relationship (e.g., 'rooms')
        $units = Floorplan::with('units') // 'rooms' is the name of the relationship
            // ->where('floor',$request->input('currentFloor'))
            ->paginate(10); // Paginate with 10 items per page

        // Return the paginated data with child relationships as JSON
        return response()->json($units);
    }
}
