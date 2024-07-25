<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\FilesController;
use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserCollectionResource;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage as store;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $name = $request->query('filterText', '');
        $limit = $request->query('limit', 20);

        $user = User::where(function ($query) use ($name) {
            $query->where('fullName', 'like', '%' . $name . '%')
                ->orWhere('email', 'like', '%' . $name . '%');
        })
            ->where('userState_id', '=', 1)
            ->where('id', '<>', auth()->user()->id)
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
        // {
        //     "id": 1,
        //     "fullName": "admin",
        //     "email": "admin@gmail.com",
        //     "photo": null,
        //     "role": {
        //         "id": 1,
        //         "name": "admin"
        //     }
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            $request['id'] = $id;
            $rules = [
                'id' => 'required|numeric|exists:users',
                'fullName' => 'nullable|string',
                'photo' => 'nullable|string'
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("El usuario no se pudo actualizar", 400, $validator);
            }

            $user = User::find($id);

            if (isset($request->fullName)) {
                $user->fullName = $request->fullName;
            }

            if (isset($request->photo)) {
                $imageName = FilesController::saveFile($request->photo, "photos");
                $user->photo = $imageName;
            }

            $user->update();

            return ResponseController::success(new UserResource($user), "Usuario actualizado", 200);
        } catch (Exception $e) {
            Log::error("Actualizar usuario -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    public function changePassword(Request $request, string $id)
    {

        try {
            $request['id'] = $id;
            $request['email'] = Auth::user()->email;
            $request['password'] = $request['currentPassword'];
            $rules = [
                'id' => 'required|numeric|exists:users',
                'password' => 'required|string|max:200|min:8',
                'newPassword' => 'required|string|max:200|min:8',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("El usuario no se pudo actualizar", 400, $validator);
            }

            $user = User::find($id);
            // return ResponseController::error($request->only('email', 'password'), 401);
            if (!Auth::guard('api')->attempt($request->only('email', 'password'))) {
                return ResponseController::error("Credenciales incorrectas", 401);
            }


            $user->password = Hash::make($request->newPassword);

            $user->update();

            return ResponseController::success(new UserResource($user), "Contraseña actualizada", 200);
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
        //
    }
}
