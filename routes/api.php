<?php

/**
 * API Routes (v1)
 *
 * Defines all REST API endpoints for the Document Management System.
 * All routes are prefixed with /api/v1/ (the /api prefix is added by Laravel automatically).
 *
 * Route groups:
 * 1. PUBLIC ROUTES (no authentication required):
 *    - POST /register  -> Create a new user account
 *    - POST /login     -> Authenticate and get API token
 *    - GET  /departments -> List departments (needed for registration form)
 *
 * 2. PROTECTED ROUTES (require valid Sanctum auth token):
 *    - POST /logout              -> Revoke current API token
 *    - GET  /user                -> Get authenticated user's profile
 *    - GET  /dashboard           -> Get dashboard stats and recent docs
 *    - CRUD /documents           -> Full document management
 *    - GET  /documents/{id}/download -> Download a document file
 *    - GET  /categories          -> List document categories
 */

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\DocumentController;
use Illuminate\Support\Facades\Route;

// All API v1 routes are grouped under the /v1 prefix
Route::prefix('v1')->group(function () {

    // ==========================================
    // PUBLIC ROUTES (no authentication required)
    // ==========================================
    Route::post('/register', [AuthController::class, 'register']);   // User registration
    Route::post('/login', [AuthController::class, 'login']);         // User login
    Route::get('/departments', [DepartmentController::class, 'index']); // List departments (for registration dropdown)

    // ==========================================
    // PROTECTED ROUTES (Sanctum token required)
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {

        // --- Authentication ---
        Route::post('/logout', [AuthController::class, 'logout']);  // Log out (revoke token)
        Route::get('/user', [AuthController::class, 'user']);       // Get current user profile

        // --- Dashboard ---
        Route::get('/dashboard', [DashboardController::class, 'index']); // Dashboard stats & recent docs

        // --- Document CRUD ---
        Route::get('/documents', [DocumentController::class, 'index']);           // List documents (with filters)
        Route::post('/documents', [DocumentController::class, 'store']);          // Upload new document
        Route::get('/documents/{document}', [DocumentController::class, 'show']); // View document details
        Route::patch('/documents/{document}', [DocumentController::class, 'update']); // Update document metadata
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy']); // Delete document
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']); // Download file

        // --- Master Data ---
        Route::get('/categories', [CategoryController::class, 'index']); // List document categories
    });
});
