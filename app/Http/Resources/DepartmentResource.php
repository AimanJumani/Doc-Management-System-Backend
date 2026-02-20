<?php

/**
 * Department API Resource
 *
 * Transforms the Department model into a structured JSON response.
 * Used when returning department data in API responses, including
 * the departments list endpoint and as nested data within User and Document resources.
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the Department model into an array for JSON serialization.
     *
     * @param  Request  $request
     * @return array  Contains: id, name, timestamps
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,  // Department name (e.g., "Human Resources", "IT")
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
