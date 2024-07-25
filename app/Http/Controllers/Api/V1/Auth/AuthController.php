<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        try {
            $rules = [
                'fullName' => 'required|string|max:100',
                'email' => 'required|string|email|max:80',
                'password' => 'required|string|max:200|min:8',
                'photo' => 'nullable|string',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("No se pudo registrar, revise los datos", 400, $validator);
            }

            $user = User::create([
                'fullName' => $request->fullName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'photo' => $request->photo,
                'role_id' => 3,
                'userState_id' => 1,
            ])->assignRole("client");

            return ResponseController::success([
                'user' => new UserResource($user),
                'token' => $user->createToken('API Token')->plainTextToken,
            ], "Registro éxitoso", 201);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return ResponseController::error("Usuario ya registrado", 400);
            } else {
                return ResponseController::error("Error al intentar registrar, revisa los datos", 400);
            }
        } catch (Exception $e) {
            Log::error("Crear usuario -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];

        $validator = ValidateController::validate($request, $rules);

        if ($validator != null) {
            return ResponseController::error("No se pudo ingresar, revise los datos", 400, $validator);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return ResponseController::error("Credenciales incorrectas", 401);
        }

        $user = User::with('state')
            ->where('email', $request->email)->first();

        return ResponseController::success([
            'user' => new UserResource($user),
            'token' => $user->createToken('API Token')->plainTextToken,
        ]);
    }

    public function getUser()
    {
        return ResponseController::success(new UserResource(auth()->user()));
    }

    public function logout(Request $request)
    {

        $currentAccessToken = $request->user()->currentAccessToken();

        if ($currentAccessToken) {
            $currentAccessToken->delete();
        }

        return ResponseController::success();
    }
}
