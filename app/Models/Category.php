<?php

/**
 * Category Model
 *
 * Represents a document category (e.g., Policy, Report, Template, Guide, Form).
 * Categories are used to classify and organize documents in the system.
 * Each category can have many documents assigned to it.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * - title: The name of the category (e.g., "Policy", "Report")
     * - description: A short explanation of what the category is for
     */
    protected $fillable = ['title', 'description'];

    /**
     * Get all documents that belong to this category.
     * A category can have many documents (one-to-many relationship).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
