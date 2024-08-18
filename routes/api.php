<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\SportController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\FacilityController;
use App\Http\Controllers\API\MediaController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\ArticleCategoryController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\PaymentController;

Route::controller(LoginController::class)->group(function () {
    Route::post('login', 'login');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    //Users
    Route::apiResource('users', UserController::class)
        ->middleware(['permission:userProcess']);
    Route::put('users/{id}/reset-password', [UserController::class, 'resetPassword']);

    // Sports
    Route::apiResource('sports', SportController::class)
        ->middleware(['permission:sportProcess']);


    Route::apiResource('rooms', RoomController::class)
        ->middleware(['permission:roomProcess']);
    Route::apiResource('facilities', FacilityController::class)
        ->middleware(['permission:facilityProcess']);
    Route::apiResource('media', MediaController::class)
        ->middleware(['permission:mediaProcess']);
    Route::delete('media/{id}/sport/{sportId}', [MediaController::class, 'destroy']);

    Route::apiResource('subscriptions', SubscriptionController::class)
        ->middleware(['permission:subscriptionProcess']);
    Route::apiResource('tags', TagController::class)
        ->middleware(['permission:tagProcess']);
    Route::apiResource('article_categories', ArticleCategoryController::class)
        ->middleware(['permission:articleCategoryProcess']);
    Route::apiResource('articles', ArticleController::class)
        ->middleware(['permission:articleProcess']);
    Route::apiResource('offers', OfferController::class)
        ->middleware(['permission:offerProcess']);
    Route::apiResource('members', MemberController::class)
        ->middleware(['permission:memberProcess']);
    Route::apiResource('payments', PaymentController::class)
        ->middleware(['permission:paymentProcess']);
});