<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    // Store the announcement with image
    public function store(Request $request)
    {
        // Validation for announcement form fields
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // Max 2MB for images
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('public/announcements'); // Save image in 'storage/app/public/announcements'
        }

        // Store the announcement in the database
        $announcement = Announcement::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath, // Save the image path
        ]);

        // Return a response (you can modify this to return a success message or redirect)
        return response()->json([
            'message' => 'Announcement created successfully!',
            'announcement' => $announcement,
        ], 201);
    }

    //get all announcement
    public function getData()
    {
        $announcements = Announcement::latest()->get();
        return response()->json([
            'status' => 'success',
            'announcements' => $announcements
        ]);
    }

    // Update an existing announcement
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:400',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Max 2MB for images
        ]);

        // $validator = Validator::make($request->all(), [
        //     'title' => 'required|string|max:255',
        //     'description' => 'required|string',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 400);
        // }

        // If a new image is uploaded, handle the image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if (Storage::exists($announcement->image)) {
                Storage::delete($announcement->image);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('public/announcements');
            $announcement->image = $imagePath;
        }

        // Update the announcement details
        $announcement->title = $request->title;
        $announcement->description = $request->description;
        $announcement->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement updated successfully!',
            'announcement' => $announcement
        ]);
    }

    public function destroy($id)
    {
        // Find the announcement by its ID
        $announcement = Announcement::find($id);

        // Check if the announcement exists
        if (!$announcement) {
            return response()->json([
                'message' => 'Announcement not found'
            ], 404);
        }

        // Delete the announcement
        $announcement->delete();

        // Return a response indicating successful deletion
        return response()->json([
            'message' => 'Announcement deleted successfully'
        ], 200);
    }
}
