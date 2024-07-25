<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Correo de Cliente</title>
  <style>
    /* Estilos CSS para el correo */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }
    .container {
      max-width: 600px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
      color: #333;
    }
    .info-container {
      margin-top: 20px;
    }
    .info-item {
      margin-bottom: 10px;
    }
    .info-item label {
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>aguas-de-nuqui.com.con</h1>
    <div class="info-container">
      <div class="info-item">
        <label>Mensaje:</label>
        <p>{{ $client["message"] }}</p>
      </div>
      <br>
      <br>
      <hr>
      <div class="info-item">
        <p><strong>Att:</strong> {{ $client["fullName"] }}</p>
      </div>
      <div class="info-item">
        <p><strong>E-mail:</strong> {{ $client["email"] }}</p>
      </div>
      <div class="info-item">
        <p><strong>Tel:</strong> {{ $client["cellPhone"] }}</p>
      </div>
    </div>
  </div>
</body>
</html>


{{-- <!DOCTYPE html>
<html>

<head>
    <title>Informacion Nuqui Tours</title>
    <style>
        .container {
            display: flex;
            margin: 0 auto;
            width: 50rem;
            padding: 1rem;
            border-radius: 15px;
            background-color: ghostwhite;
        }
    </style>
</head>

<body>
    <div class="container">
        <form>
            <div>
                <p> {{ $client["message"] }} </p>
                <p>
                    Att: <strong>{{ $client["fullName"] }}</strong> <br>
                    E-mail: <strong>{{ $client["email"] }}</strong> <br>
                    Tel: <strong>{{ $client["cellPhone"] }}</strong>
                </p>
            </div>
        </form>
    </div>
</body>

</html> --}}
