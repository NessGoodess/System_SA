<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadDocumentFilesRequest;
use App\Jobs\RecordActivities;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Services\DocumentService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentFileController extends Controller
{

    protected $documentService;
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UploadDocumentFilesRequest $request, Document $document): JsonResponse
    {
        try {
            $uploadFiles = $this->documentService->addFilesToDocument(
                $document,
                $request->file('files')
            );

            return response()->json([
                'message' => 'Archivos agregados exitosamente.',
                'document' => $uploadFiles->load('files')
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo agregar los archivos al documento.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document, DocumentFile $file)
    {
        if ($file->document_id !== $document->id) {
        abort(403);
    }

    $path = Storage::disk('private')->path($file->file_path);

    return response()->file($path, [
        'Content-Type' => $file->mime_type,
        'Content-Disposition' => 'inline; filename="'.$file->file_name.'"'
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document, DocumentFile $file): JsonResponse
    {
        DB::beginTransaction();
        try {
            if (!$file) {
                return response()->json(['message' => 'El archivo no existe.'], 404);
            }
            if ($file->document_id !== $document->id) {
                abort(404, 'El archivo no pertenece al documento.');
            }

            $filePath = $file->file_path;
            $fileData = $file->fresh();

            if (Storage::disk('private')->exists($filePath)) {
                Storage::disk('private')->delete($filePath);
            }

            RecordActivities::dispatchSync(
                auth()->user(),
                'Archivo eliminado',
                $fileData,
                'Se ha eliminado el archivo del documento.',
                [
                    'title' => $document->title,
                    'file_id' => $fileData->id,
                ]
            );

            $file->delete();

            DB::commit();

            return response()->json(['message' => 'Archivo eliminado']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo eliminar el archivo.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 400);
        }
    }

    /**
     * Download the specified file.
     */
    public function download(Document $document, DocumentFile $file)
    {
        if ($file->document_id !== $document->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $path = Storage::disk('private')->path($file->file_path);
        return response()->download($path, $file->file_name);

        /*return Storage::disk('private')->download(
            $file->file_path,
            $file->file_name
        );*/
    }
}
