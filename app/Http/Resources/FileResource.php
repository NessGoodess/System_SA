<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            //'document_id' => $this->document_id,
            'original_name' => $this->original_name,
            'stored_name' => $this->stored_name,
            'file_path' => $this->file_path,
            'file_url' => $this->file_url,
            'mime_type' => $this->mime_type,
            'file_extension' => $this->file_extension,
            'file_size' => $this->file_size,
            //'hash' => $this->hash,
            'uploaded_by' => $this->uploaded_by,
            'uploaded_at' => $this->uploaded_at,
        ];
    }
}
