<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DrugController extends Controller
{
    public function addDrug(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'price_per_unit' => 'required|numeric|min:0',
                'expiry_date' => 'nullable|date',
            ]);

            $drug = Drug::create($validated);


            return response()->json([
                'status' => true,
                'code' => 201,
                'message' => 'Drug added successfully',
                'drug' => $drug,
            ], 201);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function listDrugs(): JsonResponse
    {
        try {
            $drugs = Drug::latest()->get();

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Drugs data fetched successfully',
                'drugs' => $drugs,
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function viewDrug($id): JsonResponse
    {
        try {
            $drug = Drug::find($id);

            if (!$drug) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Drug not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Drug fetched successfully',
                'drug' => $drug,
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function editDrug(Request $request, $id): JsonResponse
    {
        try {

            $drug = Drug::find($id);

            if (!$drug) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Drug not found',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'sometimes|required|integer|min:0',
                'price_per_unit' => 'sometimes|required|numeric|min:0',
                'expiry_date' => 'nullable|date',
            ]);

            $drug->update($validated);

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Drug updated successfully',
                'drug' => $drug,
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function suspendDrug($id): JsonResponse
    {
        try {
            $drug = Drug::find($id);

            if (!$drug) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Drug not found',
                ], 404);
            }

            $drug->status = 0;
            $drug->save();

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Drug suspended successfully',
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function deleteDrug($id): JsonResponse
    {
        try {

            $drug = Drug::find($id);

            if (!$drug) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Drug not found',
                ], 404);
            }

            $drug->delete();


            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Drug deleted successfully',
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function adjustStock(Request $request, $id): JsonResponse
    {
        try {

            $validated = $request->validate([
                'quantity' => 'required|integer',
            ]);

            $drug = Drug::find($id);

            if (!$drug) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Drug not found',
                ], 404);
            }

            $drug->quantity += $validated['quantity'];

            if ($drug->quantity < 0) {
                $drug->quantity = 0;
            }

            $drug->save();



            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Stock adjusted successfully',
                'drug' => $drug,
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
