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
    public function index()
    {
        $documentStatusHistories = Document::with(['status', 'children.status'])
        ->whereNull('parent_id')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'message' => 'Historial de estados',
            'data' => $documentStatusHistories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentStatusHistoryRequest $request, Document $document)
    {
        $documentStatusHistory = DocumentStatusHistory::create([
            'document_id' => $document->id,
            'status_id' => $request->status_id,
            'comment' => $request->comment,
            'form' => $request->form,
        ]);

        // Update the document's current status
        $document->update([
            'status_id' => $request->status_id,
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
        //
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
