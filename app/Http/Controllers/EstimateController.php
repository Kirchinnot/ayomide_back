<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class EstimateController extends Controller
{
    /**
     * Display a listing of the estimates.
     */
    public function index(): JsonResponse
    {
        $estimates = Estimate::latest()->paginate(20);
        return response()->json($estimates);
    }

    /**
     * Display estimates for the authenticated user.
     */
    public function myEstimates(): JsonResponse
    {
        $estimates = Estimate::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
        return response()->json($estimates);
    }

    /**
     * Get estimates by client email (public endpoint).
     */
    public function byEmail($email): JsonResponse
    {
        $estimates = Estimate::byEmail($email)
            ->select(['id', 'client_name', 'client_email', 'total_price', 'status', 'created_at'])
            ->latest()
            ->get();

        return response()->json($estimates);
    }

    /**
     * Display a specific estimate.
     */
    public function show($id): JsonResponse
    {
        $estimate = Estimate::find($id);

        if (!$estimate) {
            return response()->json(['message' => 'Estimate not found'], 404);
        }

        return response()->json($estimate);
    }

    /**
     * Store a newly created estimate.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.product_name' => 'required|string',
            'items.*.price_m2' => 'required|numeric',
            'items.*.surface' => 'required|numeric|min:0.01',
            'items.*.total_price' => 'required|numeric',
            'total_price' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,sent,accepted,rejected,expired',
            'valid_until' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $estimate = new Estimate($validated);
        
        // Associate with user if authenticated
        if (auth()->check()) {
            $estimate->user_id = auth()->id();
        }

        $estimate->save();

        return response()->json($estimate, 201);
    }

    /**
     * Update the specified estimate.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $estimate = Estimate::find($id);

        if (!$estimate) {
            return response()->json(['message' => 'Estimate not found'], 404);
        }

        // Authorization check
        if ($estimate->user_id && $estimate->user_id !== auth()->id() && !auth()->user()?->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'client_name' => 'string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_address' => 'nullable|string|max:500',
            'items' => 'array|min:1',
            'items.*.product_id' => 'required_with:items|integer',
            'items.*.product_name' => 'required_with:items|string',
            'items.*.price_m2' => 'required_with:items|numeric',
            'items.*.surface' => 'required_with:items|numeric|min:0.01',
            'items.*.total_price' => 'required_with:items|numeric',
            'total_price' => 'numeric|min:0',
            'status' => 'in:draft,sent,accepted,rejected,expired',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $estimate->update($validated);

        return response()->json($estimate);
    }

    /**
     * Change the status of an estimate.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $estimate = Estimate::find($id);

        if (!$estimate) {
            return response()->json(['message' => 'Estimate not found'], 404);
        }

        // Authorization check
        if ($estimate->user_id && $estimate->user_id !== auth()->id() && !auth()->user()?->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
        ]);

        $estimate->update($validated);

        return response()->json($estimate);
    }

    /**
     * Remove the specified estimate.
     */
    public function destroy($id): JsonResponse
    {
        $estimate = Estimate::find($id);

        if (!$estimate) {
            return response()->json(['message' => 'Estimate not found'], 404);
        }

        // Authorization check
        if ($estimate->user_id && $estimate->user_id !== auth()->id() && !auth()->user()?->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $estimate->delete();

        return response()->json(['message' => 'Estimate deleted'], 200);
    }

    /**
     * Get statistics about estimates
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => Estimate::count(),
            'draft' => Estimate::where('status', 'draft')->count(),
            'sent' => Estimate::where('status', 'sent')->count(),
            'accepted' => Estimate::where('status', 'accepted')->count(),
            'rejected' => Estimate::where('status', 'rejected')->count(),
            'total_revenue' => Estimate::where('status', 'accepted')->sum('total_price'),
        ];

        return response()->json($stats);
    }

    /**
     * Duplicate an estimate
     */
    public function duplicate($id): JsonResponse
    {
        $originalEstimate = Estimate::find($id);

        if (!$originalEstimate) {
            return response()->json(['message' => 'Estimate not found'], 404);
        }

        $newEstimate = $originalEstimate->replicate();
        $newEstimate->status = 'draft';
        $newEstimate->created_at = now();
        $newEstimate->updated_at = now();
        $newEstimate->save();

        return response()->json($newEstimate, 201);
    }
}
