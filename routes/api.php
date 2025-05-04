<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FloorPlanController;
use App\Http\Controllers\MostVisitedController;
use App\Http\Controllers\TeacherController;
use App\Models\Teacher;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    // uploading svg layout
    Route::post('/floorplan/upload', [FloorPlanController::class, 'uploadFloorPlan']);
    // save facilities
    Route::post('/floorplan/units', [FloorPlanController::class, 'storeFacilites']);
    // get the floor with unites
    Route::get('/floorplan/unit/collections', [FloorPlanController::class, 'unitCollections']);
    Route::post('/floorplan/unit/update/{id}', [FloorPlanController::class, 'update']);

    //get floor for adding new teacher
    Route::get('/floorplan', [TeacherController::class, 'getFloor']);
    Route::post('/create-teacher', [TeacherController::class, 'createTeacher']);
    Route::post('/update-teacher/{id}', [TeacherController::class, 'updateTeacher']);
    Route::delete('/delete-teacher/{id}', [TeacherController::class, 'deleteTeacher']);
    Route::get('/getTeacher', [TeacherController::class, 'getTeacher']);

    // announcement
    Route::post('/announcement', [AnnouncementController::class, 'store']);

    Route::post('/announcement/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcement/{id}', [AnnouncementController::class, 'destroy']);

});

//announcement
Route::get('/announcement', [AnnouncementController::class, 'getData']);
// analytics
Route::post('/unit-search',[MostVisitedController::class, 'clickedUnit']);
Route::get('/analytics',[MostVisitedController::class, 'analyticsForMostVisited']);

Route::post('reset-password', [AuthController::class, 'sendResetLink']);
Route::post('reset', [AuthController::class, 'reset']);


// get the floor with unites
Route::get('/floorplan/unit/collections/kiosk', [FloorPlanController::class, 'unitCollections']);

// allow accessing images
Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (!file_exists($file)) {
        abort(404);
    }

    return response()->file($file, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
    ]);
})->where('path', '.*');
