<?php

Route::apiResource('parents', ParentController::class);
Route::apiResource('staff', StaffController::class);
Route::apiResource('groups', GroupController::class);
Route::apiResource('children', ChildController::class);
Route::apiResource('activities', ActivityController::class);
Route::apiResource('progress-reports', ProgressReportController::class);
Route::apiResource('payments', PaymentController::class);

Route::get('attendance', [AttendanceController::class, 'index']);
Route::get('attendance/{attendance}', [AttendanceController::class, 'show']);
Route::post('attendance', [AttendanceController::class, 'store']);
Route::put('attendance/{attendance}', [AttendanceController::class, 'update']);
Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy']);

Route::get('children/{child}/attendance', [AttendanceController::class, 'indexByChild']);
Route::post('children/{child}/attendance', [AttendanceController::class, 'storeForChild']);
