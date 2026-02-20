<?php

/**
 * Category Seeder
 *
 * Seeds the 6 default document categories used to classify documents.
 * Categories help users organize, browse, and filter documents by type.
 *
 * Categories created:
 * - Policy:   Company policies and procedures
 * - Report:   Reports and analytics documents
 * - Template: Document templates and forms
 * - Guide:    User guides and manuals
 * - Form:     Official forms and applications
 * - Other:    Miscellaneous documents
 */

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Create the 6 default document categories.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Policy', 'description' => 'Company policies and procedures'],
            ['title' => 'Report', 'description' => 'Reports and analytics documents'],
            ['title' => 'Template', 'description' => 'Document templates and forms'],
            ['title' => 'Guide', 'description' => 'User guides and manuals'],
            ['title' => 'Form', 'description' => 'Official forms and applications'],
            ['title' => 'Other', 'description' => 'Miscellaneous documents'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
