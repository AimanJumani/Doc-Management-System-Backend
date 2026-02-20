<?php

/**
 * Document Controller (API v1)
 *
 * Handles all CRUD operations for documents in the Document Management System.
 * This is the primary controller for document management, providing:
 * - index:    List documents with filters, search, sorting, and pagination (role-filtered)
 * - store:    Upload a new document with file (admin/manager only)
 * - show:     View a single document's details (access-level checked)
 * - update:   Edit document metadata (admin or document owner who is a manager)
 * - destroy:  Delete a document and its file (admin or document owner who is a manager)
 * - download: Download the document file and increment download counter
 *
 * Role-Based Access Control (RBAC):
 * - Admin:    Full access to all documents
 * - Manager:  Can view public + own department docs, upload to own dept, edit/delete own uploads
 * - Employee: Can view public docs + department-level docs in their department (read-only)
 *
 * Document Access Levels:
 * - public:     Visible to all authenticated users
 * - department: Visible only to users in the same department
 * - private:    Visible only to the uploader and admins
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * List all documents with filtering, searching, sorting, and pagination.
     *
     * Applies RBAC filtering so users only see documents they're allowed to access.
     * Supports query parameters: search, category_id, department_id, access_level,
     * sort_by, sort_order, page, per_page.
     *
     * @param  Request  $request
     * @return JsonResponse  Paginated list of documents with metadata
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Document::with(['category', 'department', 'uploader']);

        // Apply RBAC filtering
        if ($user->hasRole('admin')) {
            // Admin sees all documents
        } elseif ($user->hasRole('manager')) {
            // Manager sees public + own department documents
            $query->where(function ($q) use ($user) {
                $q->where('access_level', 'public')
                  ->orWhere('department_id', $user->department_id);
            });
        } else {
            // Employee sees public documents + department docs if in same department
            $query->where(function ($q) use ($user) {
                $q->where('access_level', 'public')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('access_level', 'department')
                         ->where('department_id', $user->department_id);
                  });
            });
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // Filter by access level
        if ($request->filled('access_level')) {
            $query->where('access_level', $request->input('access_level'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['title', 'created_at', 'file_size', 'download_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination - default 20 per page, maximum 100 allowed
        $perPage = min($request->input('per_page', 20), 100);
        $documents = $query->paginate($perPage);

        return response()->json([
            'data' => DocumentResource::collection($documents),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ]);
    }

    /**
     * Upload a new document.
     *
     * Only admins and managers can upload documents.
     * Managers can only upload to their own department.
     * The file is stored on the local disk under the "documents" directory.
     * Accepted file types: PDF, DOCX, XLSX, JPG, JPEG, PNG (max 10MB).
     *
     * @param  Request  $request  Contains: title, description, category_id, department_id, access_level, file
     * @return JsonResponse  201 on success with the created document data
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Only admin and manager can upload
        if (!$user->hasAnyRole(['admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized. Only admins and managers can upload documents.'], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['required', 'exists:categories,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'access_level' => ['required', 'in:public,department,private'],
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,docx,xlsx,jpg,jpeg,png'],
        ]);

        // Manager can only upload to own department
        if ($user->hasRole('manager') && !$user->hasRole('admin')) {
            if ((int) $validated['department_id'] !== $user->department_id) {
                return response()->json(['message' => 'Managers can only upload to their own department.'], 403);
            }
        }

        $file = $request->file('file');
        $path = $file->store('documents', 'local');

        $document = Document::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'category_id' => $validated['category_id'],
            'department_id' => $validated['department_id'],
            'uploaded_by' => $user->id,
            'access_level' => $validated['access_level'],
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => new DocumentResource($document->load(['category', 'department', 'uploader'])),
        ], 201);
    }

    /**
     * Show a single document's details.
     *
     * Checks if the authenticated user has permission to view this document
     * based on the document's access level and the user's role/department.
     *
     * @param  Request   $request
     * @param  Document  $document  Automatically resolved via route model binding
     * @return JsonResponse  Document data or 403 if unauthorized
     */
    public function show(Request $request, Document $document): JsonResponse
    {
        $user = $request->user();

        if (!$this->canViewDocument($user, $document)) {
            return response()->json(['message' => 'Unauthorized to view this document.'], 403);
        }

        return response()->json([
            'data' => new DocumentResource($document->load(['category', 'department', 'uploader'])),
        ]);
    }

    /**
     * Update a document's metadata (title, description, category, department, access level).
     *
     * Only admins and the manager who uploaded the document can edit it.
     * Managers cannot reassign documents to a different department.
     * Note: The actual file cannot be changed - only metadata is updated.
     *
     * @param  Request   $request   Contains optional: title, description, category_id, department_id, access_level
     * @param  Document  $document  Automatically resolved via route model binding
     * @return JsonResponse  Updated document data or 403 if unauthorized
     */
    public function update(Request $request, Document $document): JsonResponse
    {
        $user = $request->user();

        if (!$this->canEditDocument($user, $document)) {
            return response()->json(['message' => 'Unauthorized to edit this document.'], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'department_id' => ['sometimes', 'required', 'exists:departments,id'],
            'access_level' => ['sometimes', 'required', 'in:public,department,private'],
        ]);

        // Manager can only change department to own department
        if ($user->hasRole('manager') && !$user->hasRole('admin')) {
            if (isset($validated['department_id']) && (int) $validated['department_id'] !== $user->department_id) {
                return response()->json(['message' => 'Managers can only assign documents to their own department.'], 403);
            }
        }

        $document->update($validated);

        return response()->json([
            'message' => 'Document updated successfully',
            'data' => new DocumentResource($document->load(['category', 'department', 'uploader'])),
        ]);
    }

    /**
     * Delete a document and its associated file from storage.
     *
     * Only admins and the manager who uploaded the document can delete it.
     * The physical file is removed from the local storage disk.
     *
     * @param  Request   $request
     * @param  Document  $document  Automatically resolved via route model binding
     * @return JsonResponse  Success message or 403 if unauthorized
     */
    public function destroy(Request $request, Document $document): JsonResponse
    {
        $user = $request->user();

        if (!$this->canDeleteDocument($user, $document)) {
            return response()->json(['message' => 'Unauthorized to delete this document.'], 403);
        }

        // Delete the file from storage
        Storage::disk('local')->delete($document->file_path);

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully',
        ]);
    }

    /**
     * Download a document's file.
     *
     * Checks view permissions, verifies the file exists on disk,
     * increments the download counter, and streams the file to the client.
     *
     * @param  Request   $request
     * @param  Document  $document  Automatically resolved via route model binding
     * @return StreamedResponse|JsonResponse  File download stream or error response
     */
    public function download(Request $request, Document $document): StreamedResponse|JsonResponse
    {
        $user = $request->user();

        // Check if user has permission to view (and therefore download) this document
        if (!$this->canViewDocument($user, $document)) {
            return response()->json(['message' => 'Unauthorized to download this document.'], 403);
        }

        // Verify the physical file still exists on disk
        if (!Storage::disk('local')->exists($document->file_path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        // Track download count for analytics
        $document->increment('download_count');

        // Stream the file to the browser with the original filename
        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    /**
     * Check if a user is allowed to view a document based on access level.
     *
     * Rules:
     * - Admin can view everything
     * - Public documents are visible to everyone
     * - Department documents are visible to users in the same department
     * - Private documents are only visible to the uploader
     *
     * @param  mixed     $user
     * @param  Document  $document
     * @return bool
     */
    private function canViewDocument($user, Document $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($document->access_level === 'public') {
            return true;
        }

        if ($document->access_level === 'department' && $document->department_id === $user->department_id) {
            return true;
        }

        if ($document->access_level === 'private' && $document->uploaded_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is allowed to edit a document.
     *
     * Rules:
     * - Admin can edit any document
     * - Manager can only edit documents they uploaded themselves
     * - Employees cannot edit any documents
     *
     * @param  mixed     $user
     * @param  Document  $document
     * @return bool
     */
    private function canEditDocument($user, Document $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager') && $document->uploaded_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is allowed to delete a document.
     *
     * Rules (same as edit):
     * - Admin can delete any document
     * - Manager can only delete documents they uploaded themselves
     * - Employees cannot delete any documents
     *
     * @param  mixed     $user
     * @param  Document  $document
     * @return bool
     */
    private function canDeleteDocument($user, Document $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager') && $document->uploaded_by === $user->id) {
            return true;
        }

        return false;
    }
}
