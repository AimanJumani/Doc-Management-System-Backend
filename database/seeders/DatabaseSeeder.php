<?php

/**
 * Database Seeder (Master Seeder)
 *
 * This is the main seeder that orchestrates all individual seeders.
 * It runs them in the correct order to respect foreign key dependencies:
 *
 * 1. DepartmentSeeder  - Creates the 5 organizational departments
 * 2. CategorySeeder    - Creates the 6 document categories
 * 3. RoleSeeder        - Creates 3 roles: admin, manager, employee
 * 4. UserSeeder        - Creates test users (1 admin, 4 managers, 7 employees)
 * 5. DocumentSeeder    - Creates 30 sample documents with dummy files
 *
 * Run with: php artisan db:seed
 * Or with fresh migration: php artisan migrate:fresh --seed
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Calls each seeder in dependency order.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,  // Must run first (users & docs depend on departments)
            CategorySeeder::class,    // Must run before documents
            RoleSeeder::class,        // Must run before users (users get assigned roles)
            UserSeeder::class,        // Must run before documents (docs need uploaders)
            DocumentSeeder::class,    // Runs last (depends on all above)
        ]);
    }
}
