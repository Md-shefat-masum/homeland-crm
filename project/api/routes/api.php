<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetCode']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes (require authentication)
Route::prefix('v1')->middleware('auth:api')->group(function () {
    // Auth routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Address routes (dependency for customer creation)
    Route::get('/addresses/tree', [\App\Http\Controllers\Api\AddressController::class, 'tree']);
    Route::get('/addresses/type/{type}', [\App\Http\Controllers\Api\AddressController::class, 'getByType']);
    Route::get('/addresses/{id}/children', [\App\Http\Controllers\Api\AddressController::class, 'getChildren']);
    Route::apiResource('addresses', \App\Http\Controllers\Api\AddressController::class);

    // Profession routes (dependency for customer creation)
    Route::get('/professions/type/{type}', [\App\Http\Controllers\Api\ProfessionController::class, 'getByType']);
    Route::apiResource('professions', \App\Http\Controllers\Api\ProfessionController::class);

    // Customer Group routes (dependency for customer creation)
    Route::apiResource('customer-groups', \App\Http\Controllers\Api\CustomerGroupController::class);

    // Lead Source routes
    Route::apiResource('lead-sources', \App\Http\Controllers\Api\LeadSourceController::class);

    // Interested Type routes
    Route::apiResource('interested-types', \App\Http\Controllers\Api\InterestedTypeController::class);

    // Lead routes
    Route::apiResource('leads', \App\Http\Controllers\Api\LeadController::class);

    // Customer routes
    Route::get('/customers/search', [\App\Http\Controllers\Api\CustomerController::class, 'search']);
    Route::apiResource('customers', \App\Http\Controllers\Api\CustomerController::class);

    // Customer Notes routes (nested under customers)
    Route::apiResource('customers.notes', \App\Http\Controllers\Api\CustomerNoteController::class)->shallow();

    // Customer Assignment routes
    Route::get('/customers/{customerId}/assignments', [\App\Http\Controllers\Api\CustomerAssignmentController::class, 'getByCustomer']);
    Route::get('/employees/{employeeId}/assignments', [\App\Http\Controllers\Api\CustomerAssignmentController::class, 'getByEmployee']);
    Route::get('/customer-assignments/date/{date}', [\App\Http\Controllers\Api\CustomerAssignmentController::class, 'getByDate']);
    Route::post('/customer-assignments/bulk', [\App\Http\Controllers\Api\CustomerAssignmentController::class, 'bulkStore']);
    Route::patch('/customer-assignments/{id}/status', [\App\Http\Controllers\Api\CustomerAssignmentController::class, 'updateStatus']);
    Route::apiResource('customer-assignments', \App\Http\Controllers\Api\CustomerAssignmentController::class);

    // Project routes
    Route::apiResource('crm-projects', \App\Http\Controllers\Api\ProjectController::class);
});
