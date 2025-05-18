<?php

namespace App\Services;

use App\Jobs\RecordActivities;
use App\Models\Department;
use App\Repositories\DocumentRepository;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DocumentService
{

    protected $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Create a new document and attach files if provided.
     *
     * @param array $data
     * @param mixed $files
     * @return Document
     */
    public function createDocument(array $data, $files = null)
    {
        return DB::transaction(function () use ($data, $files) {

            $data['sender_department_id'] = $this->resolveSenderDepartment($data);
            $data['receiver_department_id'] = $this->resolveReceivertDepartment($data);


            // Process the document data
            $document = $this->documentRepository->create($data);

            // Handle file uploads if any
            if ($files) {
                $this->attachFiles($document, $files);
            }

            if (!auth()->user()->isAdmin()) {
                $this->logDocumentActivity($document, 'Documento creado');
            }

            return $document;
        });
    }

    /**
     * Update an existing document and attach files if provided.
     *
     * @param Document $document
     * @param array $data
     * @return Document
     */
    public function updateDocument(Document $document,array $data): Document
    {
        return DB::transaction(function () use ($document, $data) {

            $data['sender_department_id'] = $this->resolveSenderDepartment($data);
            $data['receiver_department_id'] = $this->resolveReceivertDepartment($data);

            $document->update($data);

            if (!auth()->user()->isAdmin()) {
                $this->logDocumentActivity($document, 'Documento actualizado');
            }

            return $document;
        });
    }

    /**
     * Add files to an existing document.
     *
     * @param Document $document
     * @param array $files
     * @return Document
     */
    public function addFilesToDocument(Document $document, array $files): Document
    {
        return DB::transaction(function () use ($document, $files) {

            $this->attachFiles($document, $files);

            if (!auth()->user()->isAdmin()) {
                $this->logDocumentActivity($document, 'Archivos añadidos');
            }

            return $document;
        });
    }

    private function resolveSenderDepartment($data): int
    {
        if (!empty($data['sender_department_id'])) {
            return $data['sender_department_id'];
        }

        if (!empty($data['new_sender_department'])) {

            $departmentName = trim($data['new_sender_department']);

            $existingDepartment = Department::where('name', $departmentName)
                ->where('type', 'sender')
                ->first();

            if ($existingDepartment) {
                return $existingDepartment->id;
            }

            return Department::create([
                'name' => $departmentName,
                'description' => $data['description_new_sender_department'] ?? $data['new_sender_department'],
                'type' => 'sender',
            ])->id;
        }


        throw new \InvalidArgumentException('Se requires elegir un departamento emisor o crear uno nuevo');
    }

    function resolveReceivertDepartment($data)
    {
        return auth()->user()->isAdmin()
            ? $data['receiver_department_id']
            : auth()->user()->department_id;
    }

    private function attachFiles($document, $files)
    {
        $uploadedFiles = app(FileUploadService::class)->upload($files);
        $document->files()->createMany($uploadedFiles);
    }

    private function logDocumentActivity($document, $action)
    {
        // Log the document activity
        RecordActivities::dispatchSync(
            auth()->user(),
            $action,
            $document,
            $action . ': ' . $document->title,
            $document->only(['title', 'status_id'])
        );
    }
}
