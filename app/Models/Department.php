<?php

/**
 * Department Model
 *
 * Represents an organizational department (e.g., HR, Finance, IT, Marketing, Operations).
 * Departments are used to group users and documents. Each user belongs to one department,
 * and documents are associated with the department that owns them.
 * Department-level access control restricts document visibility to department members.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * - name: The department name (e.g., "Human Resources", "Information Technology")
     */
    protected $fillable = ['name'];

    /**
     * Get all users (employees) who belong to this department.
     * A department can have many users (one-to-many relationship).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all documents that belong to this department.
     * A department can have many documents (one-to-many relationship).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
