<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class PasswordResetController extends Controller
{
    public function sendEmail(Request $request)
    {

        $rules = ['email' => 'required|string|email|exists:users,email'];

        $validator = ValidateController::validate($request, $rules);

        if ($validator != null) {
            return ResponseController::error("No se pudo enviar el correo", 400, $validator);
        }

        $passwordReset = PasswordResetToken::where("email", $request->email)->first();

        if ($passwordReset) {
            return ResponseController::success(null, "Link enviado, Revisa el correo");
        }

        $passwordReset = new PasswordResetToken($request->all());

        $passwordReset->token = (string) Uuid::uuid4();
        $passwordReset->created_at = now();
        $passwordReset->save();

        Mail::to($request->email)->send(new PasswordResetMail($passwordReset));

        return ResponseController::success(null, "Link enviado al correo");
    }

    public function newPassword(Request $request)
    {
        $rules = [
            'token' => 'required|string|exists:password_reset_tokens,token',
            'password' => 'required|string|max:200|min:8',
        ];

        $validator = ValidateController::validate($request, $rules);

        if ($validator != null) {
            if($validator->getMessageBag("password") != null) return ResponseController::error("Contraseña invalida", 400, $validator);
            return ResponseController::error("Link caducado, vuelve a enviar el link al correo", 400, $validator);
        }

        $passwordReset = PasswordResetToken::where("token", $request->token)->first();

        $user = User::where("email", $passwordReset->email)->first();
        $user->password = Hash::make($request->password);

        $user->update();
        $passwordReset->delete();

        return ResponseController::success(null, "Contraseña establecida correctamente");
    }

    public function validateToken(Request $request, string $token)
    {
        $request['token'] = $token;
        $rules = ['token' => 'required|string|exists:password_reset_tokens,token'];

        $validator = ValidateController::validate($request, $rules);

        if ($validator != null) {
            return ResponseController::error("Link caducado, vuelve a enviar el link al correo", 400, $validator);
        }

        return ResponseController::success(null, "Link activo");
    }
}
