<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MemberPaymentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\OfferController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('sports', SportController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('media', MediaController::class);
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::apiResource('member_payments', MemberPaymentController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('article_categories', ArticleCategoryController::class);
    Route::apiResource('articles', ArticleController::class);
    Route::apiResource('offers', OfferController::class);
});