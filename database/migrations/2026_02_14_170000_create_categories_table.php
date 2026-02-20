<?php

/**
 * Migration: Create Categories Table
 *
 * Creates the categories table for classifying documents.
 * Categories help users organize and filter documents by type.
 * Examples: Policy, Report, Template, Guide, Form, Other
 *
 * Each category has a unique title and an optional description.
 * Documents reference this table via a foreign key (category_id).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();       // Category name (must be unique)
            $table->text('description')->nullable();  // Optional description of the category
            $table->timestamps();                     // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
