<?php

/**
 * Department Seeder
 *
 * Seeds the 5 organizational departments used throughout the application.
 * Departments are the primary organizational unit - every user and document
 * is assigned to a department. Department IDs are referenced by other seeders:
 * - ID 1: Human Resources
 * - ID 2: Finance
 * - ID 3: Information Technology
 * - ID 4: Marketing
 * - ID 5: Operations
 */

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Create the 5 default departments.
     */
    public function run(): void
    {
        $departments = [
            'Human Resources',         // ID 1
            'Finance',                 // ID 2
            'Information Technology',  // ID 3
            'Marketing',               // ID 4
            'Operations',              // ID 5
        ];

        foreach ($departments as $name) {
            Department::create(['name' => $name]);
        }
    }
}
