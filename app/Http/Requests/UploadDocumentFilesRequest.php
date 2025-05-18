<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files.*' => 'nullable|file|mimes:pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx,jpg,jpeg,png,gif,svg,webp,zip,rar,mp3,mp4,json|max:10240'
        ];
    }
}
