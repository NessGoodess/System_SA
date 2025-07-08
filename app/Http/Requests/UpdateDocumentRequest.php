<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'status_id' => 'sometimes|required|integer|exists:statuses,id',
            'sender_department_id' => 'sometimes|required_without:new_sender_department|integer|exists:departments,id',
            'receiver_department_id' => $user && $user->isAdmin() ? 'required|integer|exists:departments,id' : 'nullable|integer',
            'issue_date' => 'sometimes|required|date',
            'received_date' => 'nullable|date',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
            'new_sender_department' => 'sometimes|required_without:sender_department_id|string|max:255',
            'description_new_sender_department' => 'nullable|string|max:255',
        ];
    }
}
