<?php

namespace App\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentStatusHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_id' => 'required|exists:statuses,key',
            'comment' => 'nullable|string|max:255',
            'form' => 'required|array',
            'related_document_id' => 'nullable|exists:documents,id',
        ];
    }

    public function withValidator($validator)
    {
        $status = $this->status_id;
        $form = is_array($this->form) ? $this->form : [];

        $validator->after(function ($validator) use ($status, $form) {
            if ($status === 'received') { // Recepcionado
                if (empty($form['received_by'])) {
                    $validator->errors()->add('form.received_by', 'Debe indicar quien recibio el documento');
                }
                if (empty($form['received_date'])) {
                    $validator->errors()->add('form.received_date', 'Debe indicar la fecha de recepcion');
                }
            }

            if ($status === 'in_process') { // En trámite
                if (empty($form['responsible'])) {
                    $validator->errors()->add('form.responsible', 'Debe indicar el responsable del trámite.');
                }
                if (empty($form['department'])) {
                    $validator->errors()->add('form.department', 'Debe indicar el departamento asignado.');
                }
                /*if (empty($form['description'])) {
                    $validator->errors()->add('form.description', 'Debe indicar la descripción del trámite.');
                }*/
            }

            if ($status === 'in_signing') { // En firma
                if (empty($form['sent_to'])) {
                    $validator->errors()->add('form.sent_to', 'Debe indicar a quién se envió para firma.');
                }
                if (empty($form['position'])) {
                    $validator->errors()->add('form.position', 'Debe indicar el cargo del firmante.');
                }
                /*if (empty($form['deadline_date'])) {
                    $validator->errors()->add('form.deadline_date', 'Debe indicar la fecha límite para la firma.');
                }*/
            }

            if ($status === 'in_signed') { // Firmado
                if (empty($form['concluded_by'])) {
                    $validator->errors()->add('form.concluded_by', 'Debe indicar quién firmó el documento.');
                }
                if (empty($form['signing_date'])) {
                    $validator->errors()->add('form.signing_date', 'Debe indicar la fecha de firma.');
                }
                /*if (empty($form['conclusion_notes'])) {
                    $validator->errors()->add('form.conclusion_notes', 'Debe indicar las notas de conclusión.');
                }*/
            }

            if ($status === 'completed') { //Concluido
                if (empty($form['concluded_by'])) {
                    $validator->errors()->add('form.concluded_by', 'Debe indicar quién concluyó el documento.');
                }
                if (empty($form['conclusion_date'])) {
                    $validator->errors()->add('form.conclusion_date', 'Debe indicar la fecha de conclusión.');
                }
                /*if (empty($form['conclusion_notes'])) {
                    $validator->errors()->add('form.conclusion_notes', 'Debe indicar las notas de conclusión.');
                }*/
            }

            if ($status === 'delivered') { //Entregado
                if (empty($form['delivered_by'])) {
                    $validator->errors()->add('form.delivered_by', 'Debe indicar quién entregó el documento.');
                }

                if (empty($form['delivery_to'])) {
                    $validator->errors()->add('form.delivery_to', 'Debe indicar a quién se entregó el documento.');
                }

                if (empty($form['delivery_date'])) {
                    $validator->errors()->add('form.delivery_date', 'Debe indicar la fecha de entrega.');
                }
                /*if (empty($form['delivery_notes'])) {
                    $validator->errors()->add('form.delivery_date', 'Debe dar detalles de la entrega');
                }*/
            }

            if ($status === 'archived') { // Archivado
                if (empty($form['archived_date'])) {
                    $validator->errors()->add('form.archived_date', 'Debe indicar la fecha en que se archiva');
                }
                if (empty($form['archived_by'])) {
                    $validator->errors()->add('form.archived_by', 'Debe indicar quién archivó el documento.');
                }

                /*if (!empty($form['archived_file_location']) && !is_string($form['archived_file_location'])) {
                    $validator->errors()->add('form.archived_file_location', 'La ubicación del archivo debe ser una cadena de texto.');
                }*/
            }

            if ($status === 'cancelled') { // Cancelado
                if (empty($form['cancellation_reason'])) {
                    $validator->errors()->add('form.cancellation_reason', 'Debe indicar el motivo de la cancelación.');
                }
                if (empty($form['cancellation_date'])) {
                    $validator->errors()->add('form.cancellation_date', 'Debe indicar la fecha de cancelación.');
                }
                /*if (empty($form['cancellation_notes']) && !is_string($form['cancellation_notes'])) {
                    $validator->errors()->add('form.cancellation_notes', 'Las Notas deben ser una cadena de texto.');
                }*/
            }
        });
    }
}
