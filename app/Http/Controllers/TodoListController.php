<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\Request;
use App\Http\Resources\TodoListResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;   
use Symfony\Component\HttpFoundation\Response;
use App\Models\Logging;

class TodoListController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $todolist = TodoList::latest()->get();
            return TodoListResource::collection($todolist);
        }catch (\Throwable $e) {
            Log::error('Todolist failed'. $e->getMessage());

            Logging::record(auth()->user() ? auth()->user()->id : null,'Todolist failed'. $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch todo list',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|min:3|max:255',
                'desc' => 'required|min:3|max:255',
                'is_done' => 'required|in:0,1'
            ]);

            $todo = TodoList::create($data);

            return (new TodoListResource($todo))
                ->response()
                ->setStatusCode(201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Biarkan Laravel menangani validasi (422) agar format konsisten
            throw $e;
        } catch (\Throwable $e) {
            Logging::record(auth()->user() ? auth()->user()->id : null,'Failed to create todo'. $e->getMessage());

            return response()->json([
                'message' => 'Failed to create todo',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $todolist = TodoList::find($id);

        if($todolist == null){
            Logging::record(auth()->user() ? auth()->user()->id : null,'Todo list not found '. $id);

            return response()->json([
                'message' => 'Todo list not found. ' . $id
            ], 401);
        }

        return new TodoListResource($todolist);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi partial update
        $data = $request->validate([
            'title'   => ['sometimes','required','string','min:3','max:255'],
            'desc'    => ['sometimes','required','string','min:3','max:255'],
            'is_done' => ['sometimes','required','boolean'], // menerima 0/1/true/false/"0"/"1"
        ]);

        // Cari berdasarkan ID saja
        $todo = TodoList::find($id);
        if (! $todo) {
            Logging::record(auth()->user() ? auth()->user()->id : null,'Failed to find todo '. $id);
            return response()->json(['message' => 'Todolist not found. '. $id], 404);
        }

        try {
            $todo->fill($data)->save();     // simpan perubahan
            return new TodoListResource($todo->fresh()); // kembalikan data terbaru
        } catch (\Throwable $e) {
            Logging::record(auth()->user() ? auth()->user()->id : null,'Failed to update todo'. $e->getMessage());

            return response()->json(['message' => 'Todolist update failed'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
   // DELETE /api/todos/{id}
    public function destroy(string $id)
    {
        $todo = TodoList::find($id);
        if (! $todo) {
            Logging::record(auth()->user() ? auth()->user()->id : null,'Failed to find todo '. $id);

            return response()->json(['message' => 'Todolist not found. ' . $id], 404);
        }

        try{
            $todo->delete();
            return response()->json(['message' => 'Todolist delete successfully'], 200);
        }catch (\Throwable $e) {
            Logging::record(auth()->user() ? auth()->user()->id : null,'Failed to delete todo'. $e->getMessage());

            return response()->json(['message' => 'Todolist delete failed' . $e->getMessage()], 500);
        }
    }
}
