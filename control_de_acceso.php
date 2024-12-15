<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT Admin_contraseña FROM Administrador WHERE Admin_nombre = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $hashed_password = $fila['Admin_contraseña'];

            if (password_verify($password, $hashed_password)) {
                header("Location: Pagina_administrador.html");
                exit();
            } else {
                $mensaje_error = "Contraseña o usuario incorrecto";
            }
        } else {
            $mensaje_error = "Contraseña o usuario incorrecto";
        }

        $stmt->close();
    } else {
        $mensaje_error = "Por favor, ingrese un nombre de usuario y contraseña.";
    }

    $conn->close();
} else {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error de Autenticación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('Fondo.avif');
            background-size: cover;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Error de Autenticación</h1>
        <p><?php echo htmlspecialchars($mensaje_error); ?></p>
        <a href="login.html" class="btn">Volver</a>
    </div>
</body>
</html>

