<?php

/**
 * Document Seeder
 *
 * Seeds 30 sample documents for development and demonstration purposes.
 * Creates a realistic mix of documents with different access levels:
 * - 15 Public documents  (50%) - Visible to all users
 * - 9 Department documents (30%) - Visible only within the owning department
 * - 6 Private documents  (20%) - Visible only to the uploader and admins
 *
 * Each document is assigned to a category, department, and uploader.
 * Dummy files are created on disk in the "documents" directory.
 * The uploader is automatically matched to a manager/admin in the same department.
 */

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DocumentSeeder extends Seeder
{
    /**
     * Create 30 sample documents with dummy files on disk.
     */
    public function run(): void
    {
        // Ensure the documents storage directory exists
        Storage::disk('local')->makeDirectory('documents');

        // Fetch users who have upload permissions (admins and managers)
        $admin = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first();
        $managers = User::whereHas('roles', fn($q) => $q->where('name', 'manager'))->get();

        // Document definitions organized by access level
        $documents = [
            // ===== PUBLIC DOCUMENTS (50% = 15 docs) =====
            // These are visible to all authenticated users regardless of department
            ['title' => 'Company Code of Conduct', 'description' => 'Official code of conduct for all employees', 'category_id' => 1, 'department_id' => 1, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Annual Financial Report 2025', 'description' => 'Comprehensive annual financial report', 'category_id' => 2, 'department_id' => 2, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Employee Handbook 2026', 'description' => 'Updated employee handbook with company policies', 'category_id' => 4, 'department_id' => 1, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'IT Security Guidelines', 'description' => 'Security best practices for all employees', 'category_id' => 4, 'department_id' => 3, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Brand Guidelines', 'description' => 'Official brand usage and style guide', 'category_id' => 4, 'department_id' => 4, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Leave Request Form', 'description' => 'Standard leave request form for all departments', 'category_id' => 5, 'department_id' => 1, 'access_level' => 'public', 'file_type' => 'docx'],
            ['title' => 'Expense Report Template', 'description' => 'Template for submitting expense reports', 'category_id' => 3, 'department_id' => 2, 'access_level' => 'public', 'file_type' => 'xlsx'],
            ['title' => 'Meeting Minutes Template', 'description' => 'Standard template for recording meeting minutes', 'category_id' => 3, 'department_id' => 5, 'access_level' => 'public', 'file_type' => 'docx'],
            ['title' => 'Safety Policy Document', 'description' => 'Workplace safety policies and procedures', 'category_id' => 1, 'department_id' => 5, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Marketing Campaign Report Q4', 'description' => 'Q4 2025 marketing campaign performance report', 'category_id' => 2, 'department_id' => 4, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'New Employee Onboarding Guide', 'description' => 'Step-by-step onboarding process guide', 'category_id' => 4, 'department_id' => 1, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Project Proposal Template', 'description' => 'Standard project proposal document template', 'category_id' => 3, 'department_id' => 3, 'access_level' => 'public', 'file_type' => 'docx'],
            ['title' => 'Travel Policy', 'description' => 'Company travel and reimbursement policy', 'category_id' => 1, 'department_id' => 2, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Quarterly Business Review', 'description' => 'Q4 2025 quarterly business review presentation', 'category_id' => 2, 'department_id' => 5, 'access_level' => 'public', 'file_type' => 'pdf'],
            ['title' => 'Remote Work Policy', 'description' => 'Guidelines for remote work arrangements', 'category_id' => 1, 'department_id' => 1, 'access_level' => 'public', 'file_type' => 'pdf'],

            // ===== DEPARTMENT DOCUMENTS (30% = 9 docs) =====
            // These are visible only to users within the same department
            ['title' => 'HR Performance Review Form', 'description' => 'Internal performance evaluation form', 'category_id' => 5, 'department_id' => 1, 'access_level' => 'department', 'file_type' => 'docx'],
            ['title' => 'Budget Planning Spreadsheet', 'description' => 'Annual budget planning template', 'category_id' => 3, 'department_id' => 2, 'access_level' => 'department', 'file_type' => 'xlsx'],
            ['title' => 'Server Infrastructure Report', 'description' => 'Monthly server infrastructure status report', 'category_id' => 2, 'department_id' => 3, 'access_level' => 'department', 'file_type' => 'pdf'],
            ['title' => 'Social Media Strategy', 'description' => 'Q1 2026 social media marketing strategy', 'category_id' => 2, 'department_id' => 4, 'access_level' => 'department', 'file_type' => 'pdf'],
            ['title' => 'Warehouse Operations Manual', 'description' => 'Standard operating procedures for warehouse', 'category_id' => 4, 'department_id' => 5, 'access_level' => 'department', 'file_type' => 'pdf'],
            ['title' => 'Recruitment Process Guide', 'description' => 'Internal recruitment and hiring process', 'category_id' => 4, 'department_id' => 1, 'access_level' => 'department', 'file_type' => 'pdf'],
            ['title' => 'Financial Audit Checklist', 'description' => 'Internal audit checklist and procedures', 'category_id' => 5, 'department_id' => 2, 'access_level' => 'department', 'file_type' => 'xlsx'],
            ['title' => 'System Architecture Document', 'description' => 'Current IT system architecture overview', 'category_id' => 6, 'department_id' => 3, 'access_level' => 'department', 'file_type' => 'pdf'],
            ['title' => 'Campaign Analytics Report', 'description' => 'Detailed analytics for recent campaigns', 'category_id' => 2, 'department_id' => 4, 'access_level' => 'department', 'file_type' => 'xlsx'],

            // ===== PRIVATE DOCUMENTS (20% = 6 docs) =====
            // These are visible only to the uploader and admin users
            ['title' => 'Salary Structure Document', 'description' => 'Confidential salary bands and structure', 'category_id' => 6, 'department_id' => 1, 'access_level' => 'private', 'file_type' => 'xlsx'],
            ['title' => 'Tax Filing Records', 'description' => 'Confidential tax filing documents', 'category_id' => 6, 'department_id' => 2, 'access_level' => 'private', 'file_type' => 'pdf'],
            ['title' => 'Security Incident Report', 'description' => 'Confidential security breach investigation', 'category_id' => 2, 'department_id' => 3, 'access_level' => 'private', 'file_type' => 'pdf'],
            ['title' => 'Vendor Contract Details', 'description' => 'Confidential vendor pricing and terms', 'category_id' => 6, 'department_id' => 4, 'access_level' => 'private', 'file_type' => 'pdf'],
            ['title' => 'Employee Disciplinary Records', 'description' => 'Confidential disciplinary action records', 'category_id' => 6, 'department_id' => 1, 'access_level' => 'private', 'file_type' => 'pdf'],
            ['title' => 'IT Budget Proposal 2026', 'description' => 'Confidential IT department budget proposal', 'category_id' => 2, 'department_id' => 3, 'access_level' => 'private', 'file_type' => 'xlsx'],
        ];

        // Combine admin and managers into a collection of potential uploaders
        $uploaders = collect([$admin])->merge($managers);

        // Create each document record with a corresponding dummy file
        foreach ($documents as $index => $doc) {
            // Match uploader to the department's manager, fall back to admin
            $uploader = $uploaders->first(function ($u) use ($doc) {
                return $u->department_id === $doc['department_id'];
            }) ?? $admin;

            // Generate a filename from the document title (spaces -> underscores)
            $fileName = str_replace(' ', '_', strtolower($doc['title'])) . '.' . $doc['file_type'];
            $filePath = 'documents/' . $fileName;

            // Write a placeholder file to local storage
            Storage::disk('local')->put($filePath, 'This is a dummy file for: ' . $doc['title']);

            // Create the document record in the database
            Document::create([
                'title' => $doc['title'],
                'description' => $doc['description'],
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $doc['file_type'],
                'file_size' => rand(50000, 5000000),   // Random size between 50KB and 5MB
                'category_id' => $doc['category_id'],
                'department_id' => $doc['department_id'],
                'uploaded_by' => $uploader->id,
                'access_level' => $doc['access_level'],
                'download_count' => rand(0, 50),        // Random initial download count
            ]);
        }
    }
}
