<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\SendEmailsController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PositionResource;
use App\Models\Position;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $name = $request->query('filterText', '');
            $limit = $request->query('limit', 20);

            $position = Position::where('name', 'like', '%' . $name . '%')
                ->latest()
                ->paginate($limit);

            return ResponseController::success([
                'positions' => PositionResource::collection($position),
                'paginate' => [
                    'currentPage' => $position->currentPage(),
                    'totalPage' => $position->lastPage(),
                    'total' => $position->total(),
                    'nextPageUrl' => $position->nextPageUrl(),
                    'prevPageUrl' => $position->previousPageUrl(),
                ]
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            ResponseController::error();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("El cargo no se pudo registrar, revisa los datos", 400, $validator);
            }

            $position = new Position($request->all());
            $position->save();

            return ResponseController::success(new PositionResource($position), "Registro éxitoso", 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $position = Position::with("state")
                ->where(array("id" => $id))
                ->first();

            if (!$position) {
                return ResponseController::success("Cargo no encontrado");
            }

            return ResponseController::success(new PositionResource($position));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request['id'] = $id;
            $request['positionState_id'] = $request->positionStateId;
            $rules = [
                'id' => 'required|numeric|exists:positions',
                'name' => 'nullable|string',
                'positionState_id' => 'nullable|numeric|exists:position_states,id',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("El cargo no se pudo actualizar", 400, $validator);
            }

            $position = Position::find($id);

            if (isset($request->name)) {
                $position->name = $request->name;
            }

            if (isset($request->positionState_id)) {
                $position->positionState_id = $request->positionState_id;
            }

            $position->update();

            return ResponseController::success(new PositionResource($position), "Cargo actualizado", 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $position = Position::find($id);

            if (!$position || $position->positionState_id == 2) {
                return ResponseController::success(null, "Cargo no encontrado");
            }

            $position->positionState_id = 2;
            $position->save();

            $message = "Cargo eliminado";
            Log::info($message);
            return ResponseController::success(new PositionResource($position), $message);
        } catch (Exception $e) {
            Log::error("Eliminar caargo -> " . $e->getMessage());
            return ResponseController::error();
        }
    }
}
