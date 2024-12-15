<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';

if (isset($_POST['registrar'])) {
    if (strlen(trim($_POST['username'])) > 0 && strlen(trim($_POST['password'])) > 0) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Obtener el siguiente Admin_id
        $stmt = $conn->prepare("SELECT MAX(Admin_id) AS max_id FROM Administrador");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $max_id = $row['max_id'];

        // Generar el siguiente Admin_id
        if ($max_id) {
            $number = (int)substr($max_id, 1) + 1; // Incrementar el número
        } else {
            $number = 1; // Empezar en 1 si no hay registros
        }
        $new_id = 'A' . str_pad($number, 3, '0', STR_PAD_LEFT); // Generar el nuevo ID en formato Axxx

        // Insertar el nuevo administrador
        $stmt = $conn->prepare("INSERT INTO Administrador(Admin_id, Admin_nombre, Admin_contraseña) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("sss", $new_id, $username, $hashed_password);

        if ($stmt->execute()) {
            echo "<p class='success'>¡Se inscribió correctamente!</p>";
        } else {
            echo "<p class='error'>¡Ups! Ocurrió algún error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p class='error'>Por favor, ingrese un nombre de usuario y contraseña.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #004080;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #004080;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #003366;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Administrador</h2>
        <form action="" method="post">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" name="registrar" value="Registrar">
        </form>
<a href="Pagina_administrador.html">Volver</a>
    </div>
</body>
</html>

