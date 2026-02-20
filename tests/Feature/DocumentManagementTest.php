<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function seedRolesAndDepartments(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'manager', 'guard_name' => 'web']);
        Role::create(['name' => 'employee', 'guard_name' => 'web']);

        Department::create(['name' => 'Human Resources']);
        Department::create(['name' => 'Finance']);
        Department::create(['name' => 'Information Technology']);
        Department::create(['name' => 'Marketing']);
        Department::create(['name' => 'Operations']);

        Category::create(['title' => 'Policy', 'description' => 'Company policies']);
        Category::create(['title' => 'Report', 'description' => 'Reports']);
        Category::create(['title' => 'Template', 'description' => 'Templates']);
        Category::create(['title' => 'Guide', 'description' => 'Guides']);
        Category::create(['title' => 'Form', 'description' => 'Forms']);
        Category::create(['title' => 'Other', 'description' => 'Other documents']);
    }

    private function createUser(string $role, int $departmentId = 1): User
    {
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'department_id' => $departmentId,
        ]);
        $user->assignRole($role);
        return $user;
    }

    private function createDocument(User $uploader, array $overrides = []): Document
    {
        Storage::disk('local')->put('documents/test.pdf', 'dummy content');

        return Document::create(array_merge([
            'title' => 'Test Document',
            'description' => 'A test document',
            'file_name' => 'test.pdf',
            'file_path' => 'documents/test.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'category_id' => 1,
            'department_id' => $uploader->department_id,
            'uploaded_by' => $uploader->id,
            'access_level' => 'public',
        ], $overrides));
    }

    // Test 1: User Registration
    public function test_user_can_register(): void
    {
        $this->seedRolesAndDepartments();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'department_id' => 1,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'department_id', 'roles'],
                'token',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    // Test 2: User Login
    public function test_user_can_login(): void
    {
        $this->seedRolesAndDepartments();
        $user = $this->createUser('employee');

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user',
                'token',
            ]);
    }

    // Test 3: User Logout
    public function test_user_can_logout(): void
    {
        $this->seedRolesAndDepartments();
        $user = $this->createUser('employee');
        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }

    // Test 4: Manager Can Upload Document
    public function test_manager_can_upload_document(): void
    {
        $this->seedRolesAndDepartments();
        Storage::fake('local');

        $manager = $this->createUser('manager', 1);
        $token = $manager->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/documents', [
                'title' => 'Test Upload',
                'description' => 'A test upload',
                'category_id' => 1,
                'department_id' => 1,
                'access_level' => 'public',
                'file' => UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf'),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'title', 'file_name'],
            ]);
    }

    // Test 5: Employee Cannot Upload Document
    public function test_employee_cannot_upload_document(): void
    {
        $this->seedRolesAndDepartments();
        Storage::fake('local');

        $employee = $this->createUser('employee', 1);
        $token = $employee->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/documents', [
                'title' => 'Test Upload',
                'description' => 'A test upload',
                'category_id' => 1,
                'department_id' => 1,
                'access_level' => 'public',
                'file' => UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf'),
            ]);

        $response->assertStatus(403);
    }

    // Test 6: User Can Search Documents
    public function test_user_can_search_documents(): void
    {
        $this->seedRolesAndDepartments();

        $admin = $this->createUser('admin', 3);
        $this->createDocument($admin, ['title' => 'Laravel Guide']);
        $this->createDocument($admin, ['title' => 'React Tutorial']);

        $token = $admin->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/documents?search=Laravel');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Laravel Guide', $response->json('data.0.title'));
    }

    // Test 7: User Can Filter Documents by Category
    public function test_user_can_filter_documents_by_category(): void
    {
        $this->seedRolesAndDepartments();

        $admin = $this->createUser('admin', 3);
        $this->createDocument($admin, ['title' => 'Policy Doc', 'category_id' => 1]);
        $this->createDocument($admin, ['title' => 'Report Doc', 'category_id' => 2]);

        $token = $admin->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/documents?category_id=1');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Policy Doc', $response->json('data.0.title'));
    }

    // Test 8: Admin Can Delete Any Document
    public function test_admin_can_delete_any_document(): void
    {
        $this->seedRolesAndDepartments();

        $manager = $this->createUser('manager', 1);
        $document = $this->createDocument($manager);

        $admin = $this->createUser('admin', 3);
        $token = $admin->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/documents/{$document->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Document deleted successfully']);

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    // Additional RBAC Tests

    // Test 9: Employee cannot see private documents
    public function test_employee_cannot_view_private_documents(): void
    {
        $this->seedRolesAndDepartments();

        $manager = $this->createUser('manager', 1);
        $this->createDocument($manager, ['access_level' => 'private', 'title' => 'Private Doc']);
        $this->createDocument($manager, ['access_level' => 'public', 'title' => 'Public Doc']);

        $employee = $this->createUser('employee', 2);
        $token = $employee->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/documents');

        $response->assertStatus(200);
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('Public Doc', $titles);
        $this->assertNotContains('Private Doc', $titles);
    }

    // Test 10: Manager cannot upload to other department
    public function test_manager_cannot_upload_to_other_department(): void
    {
        $this->seedRolesAndDepartments();
        Storage::fake('local');

        $manager = $this->createUser('manager', 1);
        $token = $manager->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/documents', [
                'title' => 'Cross Dept Upload',
                'category_id' => 1,
                'department_id' => 2, // Different department
                'access_level' => 'public',
                'file' => UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf'),
            ]);

        $response->assertStatus(403);
    }
}
