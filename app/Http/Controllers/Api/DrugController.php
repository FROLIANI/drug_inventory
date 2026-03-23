<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugController extends Controller
{
    public function addDrug(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'price_per_unit' => 'required|numeric|min:0',
                'expiry_date' => 'nullable|date',
            ]);

            $drug = Drug::create($request->all());
            DB::commit();

            return response()->json([
                'status' => true,
                'code' => 201,
                'message' => 'Drug added successfully',
                'drug' => $drug,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollback();
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),

            ], 401);

        }
    }

    public function listDrugs(): JsonResponse
    {
        try {

            $drugs = Drug::all();

            return response()->json([
                'status' => true,
                'code' => 200,
                'drugs' => $drugs,
                'message' => 'Drugs data fetched successful',
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function viewDrug($id): JsonResponse
    {
        try {

            $drug = Drug::find($id);

            if (! $drug) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Drug not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Drug fetched successful',
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function editDrug(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $drug = Drug::find($id);

            if (! $drug) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Drug not found',
                ], 404);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => ' sometimes|string',
                'quantity' => 'sometimes|required|integer|min:0',
                'price+per_unit' => 'sometimes|rquired|numeric|min:0',
                'expiry_date' => 'nullable|date',
            ]);

            $drug->update($request->all());
            DB::commit();

            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => 'Drug updated successful',
                'drug' => $drug,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 100);
        }
    }

    public function suspendDrug($id): JsonResponse
    {
        try {
            $drug = Drug::find($id);

            if (! $drug) {
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
                'message' => 'drug susupnded successful',
            ], 200);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => true,
                'code' => 100,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 100);
        }
    }

    public function deleteDrug($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $drug = Drug::find($id);

            if (! $drug) {
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Drug not found',
                ], 401);
            }

            $drug->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Drug deleted successfully',
            ], 200);

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 100);
        }
    }

    public function adjustStock(Request $request, $id): JsonResponse
    {
        try{
            DB::beginTransaction();

            $request->validate([
                'quantity'=> 'required|integer',
            ]);

            $drug = Drug::find($id);

            if(!$drug){
                return response()->json([
                    'status'=>false,
                    'code'=>404,
                    'message'=> 'Drug not found'
                ],404);
            }

            $drug->quantity += $request->quantity;
            if($drug->quantity < 0 )
                $drug->quantity =0 ;

            $drug ->save();
            DB::commit();

            return response()->json([
                'status'=>false,
                'code'=>200,
                'message'=>'Stock ajustested successful',
                'drug' => $drug,
            ],200);

        }

        catch(\Throwable $e){
            DB::rollback();
            Log::error($e);

            return response()->json([
                'status'=>false,
                'code'=>100,
                'message'=>'Something went wrong',
                'error' => $e->getMessage(),
            ],100);
        }
    }

}
