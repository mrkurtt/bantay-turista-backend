<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmergencyHotlineNumbersController;
use App\Http\Controllers\EssentialServiceProviderController;
use App\Http\Controllers\EstablishmentTypeController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TouristController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TouristSpotController;
use App\Models\User;
use App\Models\Tourist;
use Illuminate\Support\Facades\Route;

Route::get('/', function (){
    
    return response()->json([
        'msg' => 'Hello Camiguin Bantay Turista!'
    ], 200);

});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'loginUser');
    Route::post('/register', 'createUser');
    Route::post('/logout', 'logout');
    Route::post('/refresh', 'refresh');
});

Route::controller(TouristController::class)->prefix('v1/tourist')->group(function () {
    Route::get('/list', 'allTourist');
    Route::get('/{id}', 'singleTourist');
    Route::get('/{id}/home', 'touristHome');
    Route::get('/{id}/profile', 'touristProfile');
    Route::put('/{id}/profile/update', 'updateTourist');
});

Route::controller(EstablishmentController::class)->prefix('v1/establishment')->group(function () {
    Route::get('/', 'getAllEstablishments');
    Route::get('/{id}', 'getEstablishmentById');
    Route::get('/{id}/logs', 'viewEntryLogs');
    Route::get('/{id}/home', 'establishmentHome');
    Route::get('/{id}/profile', 'establishmentProfile');
    Route::put('/{id}/profile/update', 'updateEstablishment');
    Route::post('/{id}/scan', 'submitEntryLogs');
});

Route::controller(EstablishmentTypeController::class)->prefix('v1/establishment-type')->group(function () {
    Route::get('/', 'getEstablishmentTypes');
    Route::post('/', 'createEstablishmentType');
});

Route::controller(AdminController::class)->prefix('v1/admin')->group(function () {
    Route::get('/list', 'allAdmin');
    Route::get('/{id}', 'singleAdmin');
    Route::get('/logs/list', 'viewLogs');
    Route::get('/logs/list/{id}', 'viewFilterLogs');
    Route::post('/tourist_spots/create', 'createTouristSpot');
});

Route::controller(LogController::class)->prefix('v1/log')->group(function () {
    Route::get('/list', 'allLogs');
    Route::get('/', 'getLogsByEstablishmentIdAndDate');
    Route::post('/create', 'createLog');
});

Route::controller(ComplaintController::class)->prefix('v1/complaints')->group(function () {
    Route::get('/list', 'getAllComplaints');
    Route::get('/create', 'createComplaints');
});

Route::controller(TouristSpotController::class)->prefix('v1/tourist-spot')->group(function(){
    Route::get('/', 'getAllTouristSpots');
    Route::get('/{id}', 'getTouristSpotById');
    Route::post('/', 'createTouristSpot');
    Route::put('/{id}/update', 'updateTouristSpot');
    Route::delete('/{id}/delete', 'deleteTouristSpot');
});

Route::controller(EssentialServiceProviderController::class)->prefix('v1/essential-service-provider')->group(function(){
    Route::get('/', 'getAllProviders');
    Route::get('/{id}', 'getProviderById');
    Route::post('/', 'createProvider');
    Route::put('/{id}/update', 'updateProvider');
    Route::delete('/{id}/delete', 'deleteProvider');
});

Route::controller(EmergencyHotlineNumbersController::class)->prefix('v1/hotline')->group(function(){
    Route::get('/', 'getAllHotlines');
    Route::get('/{id}', 'getHotlineById');
    Route::post('/', 'createHotline');
    Route::put('/{id}/update', 'updateHotline');
    Route::delete('/{id}/delete', 'deleteHotline');
});

Route::controller(ScheduleController::class)->prefix('v1/schedule')->group(function(){
    Route::get('/', 'getAllSchedules');
    Route::get('/{id}', 'getScheduleById');
    Route::post('/', 'createSchedule');
    Route::put('/{id}/update', 'updateSchedule');
    Route::delete('/{id}/delete', 'deleteSchedule');
});

Route::controller(ComplaintController::class)->prefix('v1/complaint')->group(function(){
    Route::get('/', 'getAllComplaints');
    Route::get('/{id}', 'getComplaintsByTouristId');
    Route::post('/create', 'createComplaint');
    Route::put('/{id}/update', 'updateComplaintResponse');
    Route::delete('/{id}/delete', 'deleteComplaint');
});

Route::controller(FeedbackController::class)->prefix('v1/feedback')->group(function(){
    Route::get('/', 'getAllFeedback');
    Route::get('/{id}', 'getFeedbackById');
    Route::post('/create', 'createFeedback');
    Route::delete('/{id}/delete', 'deleteFeedback');
});

// generate fake users
Route::get('/v1/generateUsers/{count}', function ($count) {
    User::factory()->count($count)->create();
});

// generate fake tourists
Route::get('/v1/generateTourists/{count}', function ($count) {
    Tourist::factory()->count($count)->create();
});

/* 
    END TESTING ROUTES 
*/