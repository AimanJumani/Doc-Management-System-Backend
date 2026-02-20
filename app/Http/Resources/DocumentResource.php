<?php

/**
 * Document API Resource
 *
 * Transforms the Document model into a structured JSON response.
 * This is the most detailed resource in the system, including:
 * - Document metadata (title, description, access level)
 * - File information (name, type, size)
 * - Related data (category, department, uploader) loaded conditionally
 * - Tracking data (download count, timestamps)
 *
 * Related resources (category, department, uploader) are only included
 * when the relationship has been eager-loaded using `whenLoaded()`,
 * preventing N+1 query issues.
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the Document model into an array for JSON serialization.
     *
     * @param  Request  $request
     * @return array  Full document data with conditionally-loaded relationships
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,                   // Document title
            'description' => $this->description,       // Optional description
            'file_name' => $this->file_name,           // Original uploaded file name
            'file_type' => $this->file_type,           // File extension (pdf, docx, etc.)
            'file_size' => $this->file_size,           // File size in bytes
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),       // Nested category data (if loaded)
            'department_id' => $this->department_id,
            'department' => new DepartmentResource($this->whenLoaded('department')), // Nested department data (if loaded)
            'uploaded_by' => $this->uploaded_by,
            'uploader' => new UserResource($this->whenLoaded('uploader')),           // Nested uploader data (if loaded)
            'access_level' => $this->access_level,     // "public", "department", or "private"
            'download_count' => $this->download_count, // Total download count
            'created_at' => $this->created_at,         // When the document was uploaded
            'updated_at' => $this->updated_at,         // When the document was last modified
        ];
    }
}
