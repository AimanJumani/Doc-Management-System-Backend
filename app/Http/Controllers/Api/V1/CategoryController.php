<?php

/**
 * Category Controller (API v1)
 *
 * Handles category-related API endpoints.
 * Categories are used to classify documents (e.g., Policy, Report, Template, Guide, Form).
 * This controller provides a read-only endpoint to list all available categories.
 * Categories are used in document upload and filter forms on the frontend.
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * List all document categories.
     *
     * Returns all categories for use in dropdown selectors and filter options.
     * This endpoint requires authentication (protected by Sanctum middleware).
     *
     * @return JsonResponse  Array of category resources
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }
}
