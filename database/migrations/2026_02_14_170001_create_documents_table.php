<?php

/**
 * Migration: Create Documents Table
 *
 * Creates the main documents table which is the core of the application.
 * Each record represents an uploaded document with:
 * - Metadata: title, description
 * - File info: file_name, file_path (storage location), file_type, file_size
 * - Relationships: category_id, department_id, uploaded_by (user)
 * - Access control: access_level (public/department/private)
 * - Analytics: download_count (tracks how many times the file has been downloaded)
 *
 * All foreign keys use cascading deletes - if a category, department, or user
 * is deleted, their associated documents are also removed.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');                          // Document display title
            $table->text('description')->nullable();           // Optional document description
            $table->string('file_name');                       // Original uploaded file name
            $table->string('file_path');                       // Server storage path
            $table->string('file_type');                       // File extension (pdf, docx, etc.)
            $table->unsignedBigInteger('file_size');            // File size in bytes
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');    // Document category
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');  // Owning department
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');          // User who uploaded
            $table->enum('access_level', ['public', 'department', 'private'])->default('public'); // Visibility control
            $table->unsignedInteger('download_count')->default(0); // Download tracking counter
            $table->timestamps();                              // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
