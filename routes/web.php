<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZoomAuthController;
use App\Http\Controllers\ZoomMeetingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/zoom', [ZoomAuthController::class,'redirectToZoom']);
Route::get('/auth/zoom/callback', [ZoomAuthController::class,'handleZoomCallback']);
Route::post('/meetings/create', [ZoomMeetingController::class,'createMeeting']);

// Route for updating a Zoom meeting
Route::put('/meetings/{meetingId}/update', [ZoomMeetingController::class,'updateMeeting']);

// Route for deleting a Zoom meeting
Route::delete('/meetings/{meetingId}/delete', [ZoomMeetingController::class,'deleteMeeting']);

