<?php

/**
 * Department Controller (API v1)
 *
 * Handles department-related API endpoints.
 * Departments represent organizational units (e.g., HR, Finance, IT, Marketing, Operations).
 * This controller provides a read-only endpoint to list all departments.
 * Departments are used during user registration and document upload/filtering.
 * NOTE: This endpoint is publicly accessible (no auth required) so the registration
 * form can load the department dropdown before the user logs in.
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * List all departments.
     *
     * Returns all departments for use in dropdown selectors.
     * Available as a public route (no authentication required).
     *
     * @return JsonResponse  Array of department resources
     */
    public function index(): JsonResponse
    {
        $departments = Department::all();

        return response()->json([
            'data' => DepartmentResource::collection($departments),
        ]);
    }
}
