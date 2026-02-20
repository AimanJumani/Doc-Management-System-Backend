<?php

/**
 * User API Resource
 *
 * Transforms the User model into a structured JSON response.
 * Includes the user's roles (from Spatie Permission) and their department.
 * Sensitive data like passwords are automatically excluded via the model's $hidden property.
 * This resource is used in authentication responses and as nested data in document resources.
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the User model into an array for JSON serialization.
     *
     * @param  Request  $request
     * @return array  Contains: id, name, email, department, roles, timestamps
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->department_id,
            'department' => new DepartmentResource($this->whenLoaded('department')), // Nested department (if loaded)
            'roles' => $this->getRoleNames(), // Array of role names, e.g., ["admin"] or ["employee"]
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
