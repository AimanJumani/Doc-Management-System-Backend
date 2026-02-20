<?php

/**
 * User Seeder
 *
 * Seeds test users for development and demonstration purposes.
 * Creates 12 users across the 3 roles and 5 departments:
 *
 * Admin (1 user):
 * - admin@example.com (IT department) - Full system access
 *
 * Managers (4 users, one per department except Operations):
 * - hr.manager@example.com       (Human Resources)
 * - finance.manager@example.com   (Finance)
 * - it.manager@example.com        (IT)
 * - marketing.manager@example.com (Marketing)
 *
 * Employees (7 users, distributed across departments):
 * - john.smith@example.com, jane.doe@example.com        (HR)
 * - bob.wilson@example.com                                (Finance)
 * - alice.brown@example.com                               (IT)
 * - charlie.davis@example.com                             (Marketing)
 * - diana.evans@example.com, edward.clark@example.com    (Operations)
 *
 * All test users have the password: "password"
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Create test users with predefined roles and departments.
     */
    public function run(): void
    {
        // ---- Create Admin User ----
        // Admin belongs to IT department (dept ID 3) and has full system access
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'department_id' => 3,
        ]);
        $admin->assignRole('admin');

        // ---- Create Manager Users ----
        // One manager per department (except Operations which has no manager)
        // Managers can upload, edit, and delete documents in their department
        $managers = [
            ['name' => 'HR Manager', 'email' => 'hr.manager@example.com', 'department_id' => 1],
            ['name' => 'Finance Manager', 'email' => 'finance.manager@example.com', 'department_id' => 2],
            ['name' => 'IT Manager', 'email' => 'it.manager@example.com', 'department_id' => 3],
            ['name' => 'Marketing Manager', 'email' => 'marketing.manager@example.com', 'department_id' => 4],
        ];

        foreach ($managers as $managerData) {
            $manager = User::create(array_merge($managerData, ['password' => 'password']));
            $manager->assignRole('manager');
        }

        // ---- Create Employee Users ----
        // Employees have read-only access to public and department-level documents
        $employees = [
            ['name' => 'John Smith', 'email' => 'john.smith@example.com', 'department_id' => 1],
            ['name' => 'Jane Doe', 'email' => 'jane.doe@example.com', 'department_id' => 1],
            ['name' => 'Bob Wilson', 'email' => 'bob.wilson@example.com', 'department_id' => 2],
            ['name' => 'Alice Brown', 'email' => 'alice.brown@example.com', 'department_id' => 3],
            ['name' => 'Charlie Davis', 'email' => 'charlie.davis@example.com', 'department_id' => 4],
            ['name' => 'Diana Evans', 'email' => 'diana.evans@example.com', 'department_id' => 5],
            ['name' => 'Edward Clark', 'email' => 'edward.clark@example.com', 'department_id' => 5],
        ];

        // Create each employee and assign the "employee" role
        foreach ($employees as $employeeData) {
            $employee = User::create(array_merge($employeeData, ['password' => 'password']));
            $employee->assignRole('employee');
        }
    }
}
