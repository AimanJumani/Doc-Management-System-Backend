<?php

/**
 * Dashboard Controller (API v1)
 *
 * Provides summary statistics and recent documents for the user's dashboard page.
 * The data returned is filtered based on the user's role:
 * - Admin: sees counts for ALL documents in the system
 * - Manager: sees public docs + their own department's docs
 * - Employee: sees only public docs and department-level docs in their department
 *
 * Returns:
 * - stats: total documents, department document count, user's upload count
 * - recent_documents: the 5 most recently created documents (role-filtered)
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics and recent documents.
     *
     * Calculates document counts and fetches the 5 most recent documents,
     * all filtered according to the authenticated user's role-based access.
     *
     * @param  Request  $request
     * @return JsonResponse  Stats object and recent documents array
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Document::query();

        // Calculate document counts based on user role
        if ($user->hasRole('admin')) {
            // Admin sees ALL documents in the entire system
            $totalDocuments = Document::count();
            $departmentDocs = Document::where('department_id', $user->department_id)->count();
        } elseif ($user->hasRole('manager')) {
            // Manager sees public documents + all docs in their department
            $totalDocuments = Document::where(function ($q) use ($user) {
                $q->where('access_level', 'public')
                  ->orWhere('department_id', $user->department_id);
            })->count();
            $departmentDocs = Document::where('department_id', $user->department_id)->count();
        } else {
            // Employee sees public docs + department-level docs in their own department
            $totalDocuments = Document::where(function ($q) use ($user) {
                $q->where('access_level', 'public')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('access_level', 'department')
                         ->where('department_id', $user->department_id);
                  });
            })->count();
            $departmentDocs = Document::where('department_id', $user->department_id)
                ->where(function ($q) {
                    $q->where('access_level', 'public')
                      ->orWhere('access_level', 'department');
                })->count();
        }

        // Count documents uploaded by the current user
        $myUploads = Document::where('uploaded_by', $user->id)->count();

        // Fetch the 5 most recent documents (with relationships eager-loaded)
        // Non-admin users only see documents they have access to
        $recentDocuments = Document::with(['category', 'department', 'uploader'])
            ->when(!$user->hasRole('admin'), function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('access_level', 'public')
                       ->orWhere('department_id', $user->department_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_documents' => $totalDocuments,
                'department_documents' => $departmentDocs,
                'my_uploads' => $myUploads,
            ],
            'recent_documents' => DocumentResource::collection($recentDocuments),
        ]);
    }
}
