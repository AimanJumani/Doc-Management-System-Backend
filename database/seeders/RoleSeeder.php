<?php

/**
 * Role Seeder
 *
 * Creates the three user roles used for role-based access control (RBAC).
 * Uses the Spatie Laravel Permission package to manage roles.
 *
 * Roles and their permissions:
 * - admin:    Full access to all documents and operations
 * - manager:  Can upload/edit/delete own documents within their department
 * - employee: Read-only access to public and department-level documents
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Create the three application roles.
     * The guard_name 'web' is the default Laravel auth guard.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);     // Full system access
        Role::create(['name' => 'manager', 'guard_name' => 'web']);   // Department-level management
        Role::create(['name' => 'employee', 'guard_name' => 'web']);  // Read-only access
    }
}
