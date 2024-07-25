<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
</head>

<body style="height: 100vh;">
    <br>
    <div style="display: flex; justify-content: center;">
        <img src="http://localhost:8000/images/LogoAguaNuqui.png" height="120" alt="">
    </div>
    <br>
    <div class="container"
        style="background-color: whitesmoke; border-radius: 15px; width: 90%; display: flex; flex-direction: column; padding: 20px;">
        <br>

        <p>
            Asunto: Solicitud de restablecimiento de contraseña para tu cuenta
        </p>

        <p>Estimado/a usuario,</p>

        <p>
            Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Si no has solicitado este cambio,
            te recomendamos tomar medidas adicionales para proteger tu cuenta.

        </p>
        <p>Si reconoces esta solicitud y deseas restablecer tu contraseña, por favor sigue el enlace proporcionado a
            continuación. Una vez dentro, podrás elegir una nueva contraseña segura y acceder a tu cuenta.</p>
        <p>
            {{-- <a href="https://nuquitours.com.co/password/reset/{{ $passwordReset->token }}".{{ $passwordReset->token }}>Nueva
                contraseña</a> --}}
            <a href="http://localhost:3000/password/reset/{{ $passwordReset->token }}".{{ $passwordReset->token }}>Nueva
                contraseña</a>
        </p>

        <p>
            Por favor, ten en cuenta que este enlace expirará en 24 horas. Si no completas el proceso dentro de este
            tiempo, deberás solicitar un nuevo restablecimiento de contraseña.
        </p>

        <p>
            Si no has solicitado este cambio de contraseña, por favor ignora este mensaje. No es necesario que realices
            ninguna acción. Te recomendamos mantener la seguridad de tus credenciales y estar atento/a a cualquier
            actividad inusual en tu cuenta.
        </p>

        <p>
            Gracias por tu atención y comprensión.
        </p>

        <p>Atentamente,</p>

        <p>
            Aguas de Nuquí
        </p>

        <p>Equipo de Soporte</p>
    </div>
</body>

</html>
