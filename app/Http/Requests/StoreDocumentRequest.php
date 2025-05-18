<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
        $user = $this->user();

        return [
            'title' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255|unique:documents,reference_number,' . $this->route('document')?->id,
            'category_id' => 'required|integer|exists:categories,id',
            'status_id' => 'required|integer|exists:statuses,id',

            'sender_department_id' => 'required_without:new_sender_department|integer|exists:departments,id',
            'new_sender_department' => 'required_without:sender_department_id|string|max:255',
            'description_new_sender_department' => 'nullable|string|max:255',
            'receiver_department_id' => $user && $user->isAdmin() ? 'required|integer|exists:departments,id' : 'nullable|integer',

            'issue_date' => 'required|date',
            'received_date' => 'nullable|date',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',

            'files' => 'nullable|array|max:10',
            'files.*' => [
                'required',
                'file',
                'mimes:pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx,jpg,jpeg,png,gif,svg,webp,zip,rar,mp3,mp4',
                'max:10240',
                /*function ($attribute, $value, $fail) {
                    if (preg_match('/[^\w\.\-]/', $value->getClientOriginalName())) {
                        $fail('El nombre del archivo contiene caracteres no permitidos.');
                    }
                }*/
            ]
        ];
    }
}
