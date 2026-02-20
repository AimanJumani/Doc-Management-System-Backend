<?php

/**
 * User Model
 *
 * Represents an authenticated user in the Document Management System.
 * Users are assigned to a department and have a role that determines their permissions:
 *
 * Roles (managed by Spatie Laravel Permission package):
 * - "admin"    -> Full access to all documents, can upload/edit/delete anything
 * - "manager"  -> Can upload documents to their department, edit/delete their own uploads
 * - "employee" -> Can view public documents and department-level documents in their own dept
 *
 * Authentication is handled via Laravel Sanctum (token-based API authentication).
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /**
     * HasFactory    -> Enables model factories for testing/seeding
     * Notifiable    -> Enables sending notifications to the user
     * HasApiTokens  -> Enables Sanctum API token authentication
     * HasRoles      -> Enables Spatie role-based access control (admin, manager, employee)
     */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',           // User's full name
        'email',          // User's email (used for login)
        'password',       // User's password (automatically hashed via cast)
        'department_id',  // Foreign key -> departments table
    ];

    /**
     * Attributes hidden from JSON serialization.
     * Password and remember_token are never exposed in API responses.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute type casting.
     * - email_verified_at is cast to a Carbon datetime instance
     * - password is automatically hashed when set (Laravel's "hashed" cast)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the department this user belongs to.
     * Every user must be assigned to exactly one department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all documents uploaded by this user.
     * Uses 'uploaded_by' as the foreign key on the documents table.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }
}
