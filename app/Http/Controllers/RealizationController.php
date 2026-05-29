<?php

namespace App\Http\Controllers;

use App\Models\Realization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RealizationController extends Controller
{
    /**
     * Display a listing of the realizations.
     */
    public function index(): JsonResponse
    {
        $realizations = Realization::all();
        return response()->json($realizations);
    }

    /**
     * Display a specific realization by ID.
     */
    public function show($id): JsonResponse
    {
        $realization = Realization::find($id);
        
        if (!$realization) {
            return response()->json(['message' => 'Realization not found'], 404);
        }
        
        return response()->json($realization);
    }

    /**
     * Store a newly created realization.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'location' => 'nullable|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'nullable|string',
            'client' => 'nullable|string',
        ]);

        $realization = Realization::create($validated);
        return response()->json($realization, 201);
    }

    /**
     * Update the specified realization.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $realization = Realization::find($id);
        
        if (!$realization) {
            return response()->json(['message' => 'Realization not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'string|max:255',
            'category' => 'string',
            'location' => 'nullable|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'nullable|string',
            'client' => 'nullable|string',
        ]);

        $realization->update($validated);
        return response()->json($realization);
    }

    /**
     * Remove the specified realization.
     */
    public function destroy($id): JsonResponse
    {
        $realization = Realization::find($id);
        
        if (!$realization) {
            return response()->json(['message' => 'Realization not found'], 404);
        }

        $realization->delete();
        return response()->json(['message' => 'Realization deleted'], 200);
    }
}
