<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentStatusHistoryRequest;
use App\Models\DocumentStatusHistory;
use App\Models\Document;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentStatusHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($document)
    {

        $documentsStatusHistories = DocumentStatusHistory::where('document_id', $document)
            ->with(['status', 'relatedDocument'])
            ->orderBy('created_at', 'desc')
            ->get();

        /*$relatedDocuments = Document::where('parent_id', $document)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();*/

        return response()->json([
            'message' => $documentsStatusHistories->isEmpty()
                ? 'No hay historial de estado para este documento.'
                : 'Historial de estado obtenido.',
            'data' => $documentsStatusHistories,
            /*'related_documents' => $relatedDocuments*/
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentStatusHistoryRequest $request, Document $document)
    {

        // Obtener el ID real del status basado en el key recibido en el request
        $status = Status::where('key', $request->status_id)->firstOrFail();
        $documentStatusHistory = DocumentStatusHistory::create([
            'document_id' => $document->id,
            'status_id' => $status->id,
            'comment' => $request->comment,
            'form' => $request->form,
            'related_document_id' => $request->related_document_id,
        ]);

        // Update the document's current status
        $document->update([
            'status_id' => $status->id,
        ]);

        return response()->json([
            'message' => 'Historial de estado agregado',
            'data' => $documentStatusHistory
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(DocumentStatusHistory $documentStatusHistory)
    {
        $documentStatusHistory = DocumentStatusHistory::with(['document', 'status'])->findOrFail($documentStatusHistory->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentStatusHistory $documentStatusHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentStatusHistory $documentStatusHistory)
    {
        //
    }
}
