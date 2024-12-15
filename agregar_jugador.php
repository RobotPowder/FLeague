<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$equipo_id = $_POST['equipo_id'] ?? '';

if (empty($equipo_id)) {
    die("No se ha proporcionado un ID de equipo.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jugador_nombre'])) {
    $jugador_nombre = $_POST['jugador_nombre'] ?? '';
    $jugador_apellido = $_POST['jugador_apellido'] ?? '';

    if (empty($jugador_nombre) || empty($jugador_apellido)) {
        die("El nombre y apellido del jugador son requeridos.");
    }


    $jugador_id = uniqid(); // Genera un ID único para el jugador
    $sql_insertar_jugador = "INSERT INTO Jugador (Jugador_id, Jugador_nombre, Jugador_apellido) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_insertar_jugador);
    $stmt->bind_param("sss", $jugador_id, $jugador_nombre, $jugador_apellido);
    $stmt->execute();


    $sql_agregar_jugador_equipo = "INSERT INTO Equipo_Jugador (EquipoJugador_idJugador, EquipoJugador_idEquipo) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_agregar_jugador_equipo);
    $stmt->bind_param("ss", $jugador_id, $equipo_id);
    $stmt->execute();

    echo "Jugador agregado exitosamente.";
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Jugador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('Fondo.avif');
            background-size: cover;
            color: #00ff00;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #00ff00; 
        }
        form {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            margin: auto;
        }
        label {
            color: #ffffff;
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #00ff00;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #00cc00; 
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #004080;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #003366;
        }
    </style>
</head>
<body>
    <form action="agregar_jugador.php" method="post">
        <input type="hidden" name="equipo_id" value="<?php echo htmlspecialchars($equipo_id); ?>">
        <label for="jugador_nombre">Nombre:</label>
        <input type="text" name="jugador_nombre" id="jugador_nombre" required>
        <label for="jugador_apellido">Apellido:</label>
        <input type="text" name="jugador_apellido" id="jugador_apellido" required>
        <button type="submit">Agregar Jugador</button>
    </form>

    <a href="Pagina_administrador.html?equipo=<?php echo htmlspecialchars($equipo_id); ?>">Volver</a>
</body>
</html>

