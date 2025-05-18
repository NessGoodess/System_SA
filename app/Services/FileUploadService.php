<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Upload files to the specified disk.
     *
     * @param array $files
     * @param string $disk
     * @return array
     */
    public function upload($files, string $folder = 'documents', string $disk = 'public'): array
    {
        if ($files instanceof UploadedFile) {
            return [$this->processFile($files, $folder, $disk)];
        }

        if (!is_array($files)) {
            throw new \InvalidArgumentException('El parámetro files debe ser un array o una instancia de UploadedFile');
        }
        return collect($files)->map(function ($file) use ($folder, $disk) {
            if (!$file instanceof UploadedFile) {
                throw new \InvalidArgumentException('Instancia de archivo no válida');
            }
            return $this->processFile($file, $folder, $disk);
        })->toArray();
    }

    /**
     * Process individual file upload with validation
     */
    protected function processFile(UploadedFile $file, string $folder, string $disk): array
    {
        $filename = $this->generateFilename($file);
        $path = $file->storeAs($folder, $filename, $disk);

        return [
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $filename,
            'file_path' => $path,
            'file_url' => $this->generateFileUrl($path, $disk),
            'mime_type' => $this->detectMimeType($file),
            'file_size' => $file->getSize(),
            'file_extension' => strtolower($file->getClientOriginalExtension()),
            'uploaded_by' => $this->getAuthenticatedUserId(),
            'uploaded_at' => now()->toDateTimeString(),
            'hash' => hash_file('sha256', $file->path()),
        ];
    }

    /**
     * Generate secure filename with timestamp and random component
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        return sprintf(
            '%s_%s_%s.%s',
            now()->format('YmdHis'),
            $baseName,
            Str::random(8),
            $extension
        );
    }
    /**
     * Generate proper file URL based on disk configuration
     */
    protected function generateFileUrl(string $path, string $disk): string
    {
        if ($disk === 's3') {
            return Storage::disk($disk)->url($path);
        }

        return config('app.url') . '/storage/' . ltrim($path, 'public/');
    }


    /**
     * Detect MIME type with fallback
     */
    protected function detectMimeType(UploadedFile $file): string
    {
        return $file->getMimeType() ?: mime_content_type($file->path()) ?: 'application/octet-stream';
    }
    /**
     * Get authenticated user ID safely
     */
    protected function getAuthenticatedUserId(): ?int
    {
        return auth()->user()->id;
    }
}
