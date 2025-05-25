<?php
// hash_generator.php - Generador de hashes para contraseñas

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Hashes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background: #e9ffe9;
            border: 1px solid #4CAF50;
            border-radius: 4px;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generador de Hashes para Contraseñas</h1>
        <form method="post">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Generar Hash</button>
        </form>

        <?php if (isset($hashed_password)): ?>
        <div class="result">
            <h3>Hash generado:</h3>
            <input type="text" value="<?php echo htmlspecialchars($hashed_password); ?>" readonly onclick="this.select()">
            <p>Copie este valor y péguelo en el archivo admin.json</p>
        </div>
        <?php endif; ?>

        <h3>Instrucciones:</h3>
        <ol>
            <li>Introduzca la contraseña deseada</li>
            <li>Haga clic en "Generar Hash"</li>
            <li>Copie el resultado y péguelo en admin.json</li>
        </ol>
    </div>
</body>
</html>
