<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\FilesController;
use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserCollectionResource;
use App\Http\Resources\V1\UserResource;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $name = $request->query('filterText', '');
        $limit = $request->query('limit', 20);

        $user = User::with("employee")
            ->where(function ($query) use ($name) {
                $query->where('fullName', 'like', '%' . $name . '%')
                    ->orWhere('email', 'like', '%' . $name . '%')
                    ->orWhereHas('employee', function ($query) use ($name) {
                        $query->where('document', 'like', '%' . $name . '%')
                            ->orWhere('cellPhone', 'like', '%' . $name . '%');
                    });
            })
            ->where('role_id', '=', 2) // collaborator
            ->latest()
            ->paginate($limit);

        return ResponseController::success([
            'users' => UserCollectionResource::collection($user),
            'paginate' => [
                'currentPage' => $user->currentPage(),
                'totalPage' => $user->lastPage(),
                'total' => $user->total(),
                'nextPageUrl' => $user->nextPageUrl(),
                'prevPageUrl' => $user->previousPageUrl(),
            ]
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'fullName' => 'required|string|max:100',
                'email' => 'required|string|email|max:80',
                'document' => 'required|string|max:200',
                'photo' => 'required|string',
                'cellPhone' => 'nullable|string',
                'position_id' => 'required|numeric',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("No se pudo registrar, revise los datos", 400, $validator);
            }

            $imageName = FilesController::saveFile($request->photo, "photos");

            $employee = Employee::create([
                'document' => $request->document,
                'cellPhone' => $request->cellPhone,
                'position_id' => 1,
            ]);

            $user = User::create([
                'fullName' => $request->fullName,
                'email' => $request->email,
                'password' => Hash::make($request->document),
                'photo' => $imageName,
                'employee_id' => $employee->id,
                'role_id' => 2,
                'userState_id' => 1,
            ]);

            return ResponseController::success([
                'user' => new UserResource($user),
            ], "Registro éxitoso", 201);
        } catch (QueryException $e) {
            Log::error("Registrar empleado -> " . $e->getMessage());
            if ($e->getCode() === '23000') {
                return ResponseController::error("Usuario ya registrado", 400);
            } else {
                return ResponseController::error("Error al intentar registrar, revisa los datos", 400);
            }
        } catch (Exception $e) {
            Log::error("Registrar empleado -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where("id", "=", $id)
            ->where("role_id", "=", 2)
            ->first();

        if ($user) {
            return ResponseController::success(new UserResource($user));
        }

        return ResponseController::success("Empleado no encontrado");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $rules = [
                'fullName' => 'nullable|string|max:100',
                'email' => 'nullable|string|email|max:80',
                'document' => 'nullable|string|max:200',
                'photo' => 'nullable|string',
                'cellPhone' => 'nullable|string',
                'employee_id' => 'required|numeric',
                'position_id' => 'nullable|numeric',
                'userState_id' => 'nullable|numeric|exists:user_states,id',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("No se pudo actualizar, revise los datos", 400, $validator);
            }

            $employee = Employee::find($request->employee_id);

            if (isset($request->document)) {
                $employee->document = $request->document;
            }

            if (isset($request->cellPhone)) {
                $employee->cellPhone = $request->cellPhone;
            }

            if (isset($request->position_id)) {
                $employee->position_id = $request->position_id;
            }

            $user = User::find($id);

            if (isset($request->fullName)) {
                $user->fullName = $request->fullName;
            }

            if (isset($request->photo)) {
                $imageName = FilesController::saveFile($request->photo, "photos");
                $user->photo = $imageName;
            }

            if (isset($request->userState_id)) {
                $user->userState_id = $request->userState_id;
            }

            $employee->update();
            $user->update();
            Log::info("Empleado actualizado");
            return ResponseController::success(new UserResource($user), "Empleado actualizado", 200);
        } catch (QueryException $e) {
            Log::error("Actualizar empleado -> " . $e->getMessage());
            if ($e->getCode() === '23000') {
                return ResponseController::error("Correo ya registrado", 400);
            } else {
                return ResponseController::error("Error al intentar registrar, revisa los datos", 400);
            }
        } catch (Exception $e) {
            Log::error("Actualizar empleado -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return ResponseController::success(null, "Usuario no encontrado");
            }

            $user->userState_id = 2;
            $user->save();

            Log::info("Empleado eliminado");
            return ResponseController::success(new UserResource($user), "Empleado eliminado");
        } catch (Exception $e) {
            Log::error("Eliminar empleado -> " . $e->getMessage());
            return ResponseController::error();
        }
    }
}
