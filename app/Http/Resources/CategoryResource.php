<?php

/**
 * Category API Resource
 *
 * Transforms the Category model into a structured JSON response.
 * API Resources act as a transformation layer between the database model
 * and the JSON output sent to the frontend. This ensures a consistent
 * response format and allows control over which fields are exposed.
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the Category model into an array for JSON serialization.
     *
     * @param  Request  $request
     * @return array  Contains: id, title, description, timestamps
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,             // Category name (e.g., "Policy", "Report")
            'description' => $this->description, // What this category is for
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
