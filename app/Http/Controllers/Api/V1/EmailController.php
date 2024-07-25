<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\SendEmailsController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EmailResource;
use App\Models\Email;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $name = $request->query('filterText', '');
        $limit = $request->query('limit', 20);

        $email = Email::where(function ($query) use ($name) {
            $query->where('fullName', 'like', '%' . $name . '%')
                ->orWhere('email', 'like', '%' . $name . '%')
                ->orWhere('cellPhone', 'like', '%' . $name . '%')
                ->orWhere('subject', 'like', '%' . $name . '%');
        })
            ->where('emailState_id', '=', 1)
            ->latest()
            ->paginate($limit);

        return ResponseController::success([
            'emails' => EmailResource::collection($email),
            'paginate' => [
                'currentPage' => $email->currentPage(),
                'totalPage' => $email->lastPage(),
                'total' => $email->total(),
                'nextPageUrl' => $email->nextPageUrl(),
                'prevPageUrl' => $email->previousPageUrl(),
            ]
        ]);
    }

    public function all()
    {
        try {
            $emails = Email::with("state")->get();
            return ResponseController::success(EmailResource::collection($emails));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $rules = [
                'fullName' => 'required|string',
                'email' => 'required|email',
                'cellPhone' => 'nullable|string',
                'subject' => 'nullable|string',
                'message' => 'required|string'
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("El email no se pudo enviar", 400, $validator);
            }

            SendEmailsController::sendEmail(["info@aguasnuqui.com.con", "aguasnuqui@gmail.com"], $request);

            $email = new Email($request->all());
            $email->save();

            return ResponseController::success(new EmailResource($email), "Email enviado correctamente", 201);
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
            $email = Email::with("state")
                ->where(array("id" => $id))
                ->first();

            if (!$email) {
                return ResponseController::success("Email no encontrado");
            }

            return ResponseController::success(new EmailResource($email));
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
            $request['emailState_id'] = $request->emailStateId;
            $rules = [
                'id' => 'required|numeric|exists:emails',
                'emailState_id' => 'required|numeric|exists:email_states,id',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("El email no se pudo actualizar", 400, $validator);
            }

            $email = Email::find($id);

            $email->emailState_id = $request->emailState_id;
            $email->update();

            return ResponseController::success(new EmailResource($email), "Estado actualizado", 200);
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
            $email = Email::find($id);

            if ($email == null) {
                return ResponseController::success("El email no existe", 400);
            }

            $email->delete();

            return ResponseController::success(null, "Email eliminado con éxito");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseController::error();
        }
    }

    // public function subscribeEmail(Request $request)
    // {
    //     try {
    //         $rules = [
    //             'email' => 'required|email|max:80',
    //         ];

    //         $validator = ValidateController::validate($request, $rules);

    //         if ($validator != null) {
    //             return ResponseController::error("No se pudo subscribir, verifica el correo", 400, $validator);
    //         }

    //         $subscribedEmail = SubscribedEmail::where(array("email" => $request->email))->first();

    //         if (!$subscribedEmail) {
    //             $subscribedEmail = new SubscribedEmail($request->all());
    //             $subscribedEmail->save();
    //             return ResponseController::success(null, "¡Ya estas subscrito!", 201);
    //         }

    //         return ResponseController::success(null, "¡Ya estas subscrito!", 200);
    //     } catch (Exception $e) {
    //         return ResponseController::error($e->getMessage());
    //     }
    // }
}
