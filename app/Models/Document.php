<?php

/**
 * Document Model
 *
 * Represents a document stored in the Document Management System.
 * Documents are the core entity of the application. Each document has:
 * - Metadata: title, description, category, department, access level
 * - File info: original file name, storage path, file type, file size
 * - Tracking: who uploaded it, how many times it has been downloaded
 *
 * Access levels control who can view a document:
 * - "public"     -> Visible to all authenticated users
 * - "department" -> Visible only to users in the same department
 * - "private"    -> Visible only to the uploader and admins
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These fields can be set via Document::create() or $document->fill().
     */
    protected $fillable = [
        'title',            // Document title displayed in the UI
        'description',      // Optional description of the document contents
        'file_name',        // Original file name as uploaded by the user
        'file_path',        // Storage path on the server (e.g., "documents/filename.pdf")
        'file_type',        // File extension (e.g., "pdf", "docx", "xlsx")
        'file_size',        // File size in bytes
        'category_id',      // Foreign key -> categories table
        'department_id',    // Foreign key -> departments table
        'uploaded_by',      // Foreign key -> users table (the user who uploaded this)
        'access_level',     // Access control: "public", "department", or "private"
        'download_count',   // Number of times this document has been downloaded
    ];

    /**
     * Attribute type casting.
     * Ensures file_size and download_count are always treated as integers.
     */
    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    /**
     * Get the category this document belongs to.
     * Every document must be classified under one category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the department this document belongs to.
     * Used for department-level access control.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who uploaded this document.
     * Uses 'uploaded_by' as the foreign key instead of the default 'user_id'.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
