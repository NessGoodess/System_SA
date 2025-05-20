<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Services\DocumentService;
use App\Jobs\RecordActivities;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Document;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{

    protected $documentService;
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Document::with([
            'category:id,name',
            'status:id,name',
            'sender_department:id,name',
            'receiver_department:id,name',
        ]);

        if (!$user->isAdmin()) {
            $query->where('receiver_department_id', $user->department_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = $request->get('per_page', 15);
        $documents = $query->paginate($perPage);

        return response()->json([
            'documents' => $documents->items(),
            'pagination' => [
                'total' => $documents->total(),
                'per_page' => $documents->perPage(),
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'from' => $documents->firstItem(),
                'to' => $documents->lastItem(),
            ],
            'message' => $documents->isEmpty() ? 'No hay registros.' : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        $categories = Category::select('id', 'name')->get();
        $statuses = Status::select('id', 'name')->get();
        $senders_department = Department::senders();
        $receivers_department = Department::receivers();
        return response()->json([
            'categories' => $categories,
            'statuses' => $statuses,
            'senders_department' => $senders_department,
            'receivers_department' => $receivers_department,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        try {
            $document = $this->documentService->createDocument(
                array_merge($request->validated(), ['created_by' => auth()->user()->id]),
                $request->file('files')
            );

            return response()->json([
                'message' => 'Documento creado exitosamente.',
                'document' => $document->load('files')
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo crear el documento.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document): JsonResponse
    {
        $document->load([
            'category:id,name',
            'status:id,name',
            'sender_department:id,name',
            'receiver_department:id,name',
            'user:id,name',
            'files',
        ]);
        $comment = Comment::with([
            'user:id,name',
            'document',
            'replies'
        ])->where(
            'document_id',
            $document->id
        )->get();

        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            RecordActivities::dispatchSync(
                $user,
                'view',
                $document,
                'Se ha visualizado el documento.',
                []
            );
        }

        return response()->json([
            'document' => $document,
            'comments' => $comment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(): JsonResponse
    {
        $categories = Category::select('id', 'name')->get();
        $statuses = Status::select('id', 'name')->get();
        $senders_department = Department::senders();
        $receivers_department = Department::receivers();
        return response()->json([
            'categories' => $categories,
            'statuses' => $statuses,
            'senders_department' => $senders_department,
            'receivers_department' => $receivers_department,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document): JsonResponse
    {
        try {
            $updateDocument = $this->documentService->updateDocument(
                $document,
                $request->validated()
            );

            return response()->json([
                'message' => 'Documento actualizado exitosamente.',
                'document' => $updateDocument->load('files')
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el documento.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): JsonResponse
    {

        RecordActivities::dispatchSync(
            auth()->user(),
            'delete',
            $document,
            'Se ha eliminado el documento.',
            [
                'title' => $document->title,
                'status_id' => $document->status_id,
            ]
        );
        $document->delete();

        return response()->json([
            'message' => 'Documento eliminado.'
        ], 200);
    }

    /**
     * Search documents by filters.
     */
    public function filters(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Document::with([
            'category:id,name',
            'status:id,name',
            'sender_department:id,name',
            'receiver_department:id,name',
        ]);

        $query->when($request->filled('status_id'), fn($q, $status) => $q->where('status_id', $status))
            ->when($request->filled('category_id'), fn($q, $category) => $q->where('category_id', $category))
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->when(!$user->isAdmin(), fn($q) => $q->where('receiver_department_id', $user->department_id));

        $perPage = $request->get('per_page', 15);
        $documents = $query->paginate($perPage);

        return response()->json([
            'documents' => $documents,
            'meta' => [
                'categories' => Category::select('id', 'name')->get(),
                'statuses' => Status::select('id', 'name')->get(),
            ],
            'message' => $documents->isEmpty() ? 'No hay documentos con estos filtros.' : null
        ]);
    }

    /**
     * Search documents by title.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $searchTerm = $request->input('query');

        $documents = Document::where('title', 'like', '%' . $searchTerm . '%')->get();

        if ($documents->isEmpty()) {
            return response()->json([
                'documentsSearch' => [],
                'message' => 'No se encontraron resultados.',
                'searchTerm' => $searchTerm,
            ]);
        }

        return response()->json([
            'documentsSearch' => $documents,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Display the control panel.
     */
    public function controlPanel(): JsonResponse
    {
        $statusCounts = Document::select('status_id', DB::raw('count(*) as total'))
            ->with('status:id,name')
            ->groupBy('status_id')
            ->get();
        $totalDocuments = $statusCounts->sum('total');
        $users = User::orderBy('created_at', 'desc')->take(10)->get();
        $activities = DB::table('activities')
            ->orderBy('created_at', 'desc')
            ->take(9)
            ->get();
        return response()->json([
            'statusCounts' => $statusCounts,
            'users' => $users,
            'activities' => $activities,
            'totalDocuments' => $totalDocuments,
        ]);
    }
}
