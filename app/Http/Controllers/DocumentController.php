<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $documents = Document::with([
            'category:id,name',
            'status:id,name',
            'sender_department:id,name',
            'receiver_department:id,name',
        ])->get();

        return response()->json($documents);

        /*

        $statusName = $request->query('status'); // Recibir el nombre del estado desde el cliente

        // Si se proporciona un estado, obtener su ID
        $statusId = null;
        if ($statusName) {
            $status = Status::where('name', $statusName)->first();
            $statusId = $status ? $status->id : null;
        }

        // Filtrar documentos por status_id si se proporciona
        $documents = Document::with('status')
            ->when($statusId, function ($query, $statusId) {
            return $query->where('status_id', $statusId);
            })
            ->paginate(5);

        // Obtener los estados disponibles
        $statuses = Status::all(['id', 'name']);

        return response()->json([
            'documents' => $documents,
            'statuses' => $statuses,
            'filters' => [
            'status' => $statusName
            ]
        ]);
        */

        /*
    // Obtener los parámetros de la solicitud
    $startRow = $request->query('startRow', 0);  // Por defecto, empieza desde la fila 0
    $endRow = $request->query('endRow', 10);    // Por defecto, 10 filas por página
    $status = $request->query('status', 'todos');  // Por defecto, "todos"
    $startDate = $request->query('dateRange.start', null);  // Fecha de inicio (opcional)
    $endDate = $request->query('dateRange.end', null);      // Fecha de fin (opcional)

    // Iniciar la consulta
    $query = Document::query();

    // Filtrar por 'status' si se ha proporcionado
    if ($status && $status !== 'todos') {
        $query->whereHas('status', function ($q) use ($status) {
            $q->where('name', $status);
        });
    }

    // Filtrar por rango de fechas si se han proporcionado
    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)]);
    }

    // Paginación
    $totalRows = $query->count(); // Número total de registros
    $data = $query->with(['category:id,name', 'status:id,name'])->skip($startRow)->take($endRow - $startRow)->get();

    // Retornar los resultados con paginación
    return response()->json([
        'rows' => $data,
        'totalRows' => $totalRows,
    ]);
*/
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
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'category' => 'required|integer',
            'status' => 'required|integer',
            'sender_department' => 'required|integer',
            'receiver_department' => 'nullable|integer',
            'issue_date' => 'required|date',
            'received_date' => 'nullable|date',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $document = new Document();
        $document->title = $request->input('title');
        $document->reference_number = $request->input('reference_number');
        $document->category_id = $request->input('category');
        $document->status_id = $request->input('status');
        $document->sender_department_id = $request->input('sender_department');
        $document->receiver_department_id = $request->input('receiver_department');
        $document->issue_date = $request->input('issue_date');
        $document->received_date = $request->input('received_date');
        $document->created_by = auth()->id();
        $document->description = $request->input('description');
        $document->priority = $request->input('priority');
        //$document->is_public = $request->input('isPublic');
        $document->save();

        RecordActivities::dispatch(
            auth()->user,
            'create',
            $document,
            'Se ha creado un nuevo documento.',
            [
                'title' => $document->title,
                'status_id' => $document->status_id,
            ]
        );

        return response()->json([
            'message' => 'Documento creado con éxito.',
            'document' => $document
        ], 201);
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
            'user:id,name'
        ]);
        $comment = Comment::with(['user:id,name', 'document', 'replies'])->where('document_id', $document->id)->get();

        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            RecordActivities::dispatch(
                $user,
                'view',
                $document,
                'Se ha visualizado el documento.',
                [
                    'title' => $document->title,
                    'status_id' => $document->status_id,
                ]
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
    public function update(Request $request, Document $document): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'category' => 'required|integer',
            'status' => 'required|integer',
            'sender_department' => 'required|integer',
            'receiver_department' => 'nullable|integer',
            'issue_date' => 'required|date',
            'received_date' => 'nullable|date',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $document->title = $request->input('title');
        $document->reference_number = $request->input('reference_number');
        $document->category_id = $request->input('category');
        $document->status_id = $request->input('status');
        $document->sender_department_id = $request->input('sender_department');
        $document->receiver_department_id = $request->input('receiver_department');
        $document->issue_date = $request->input('issue_date');
        $document->received_date = $request->input('received_date');
        $document->description = $request->input('description');
        $document->priority = $request->input('priority');
        //$document->is_public = $request->input('isPublic');
        $document->save();

        RecordActivities::dispatch(
            auth()->user(),
            'update',
            $document,
            'Se ha actualizado el documento.',
            [
                'title' => $document->title,
                'status_id' => $document->status_id,
            ]
        );

        return response()->json(['message' => 'Documento actualizado con éxito.', 'document' => $document]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): JsonResponse
    {
        $document->delete();

        RecordActivities::dispatch(
            auth()->user(),
            'delete',
            $document,
            'Se ha eliminado el documento.',
            [
                'title' => $document->title,
                'status_id' => $document->status_id,
            ]
        );

        return response()->json(['message' => 'Documento eliminado con éxito.']);
    }

    /**
     * Search documents by filters.
     */
    public function filters(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'category', 'start_date', 'end_date']);

        $documents = Document::query()
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status_id', $status);
            })
            ->when($filters['category'] ?? null, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($filters['start_date'] ?? null, function ($query, $start_date) use ($filters) {
                $query->whereBetween('created_at', [$start_date, $filters['end_date']]);
            })
            ->with('category', 'status')
            ->get();

        return response()->json([
            'documents' => $documents,
            'categories' => Category::select('id', 'name')->get(),
            'statuses' => Status::select('id', 'name')->get(),
            'filters' => $filters,
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
