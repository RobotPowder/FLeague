<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$liga_id = $_POST['liga_id'] ?? '';
$temporada_id = $_POST['temporada_id'] ?? '';

if (empty($liga_id) || empty($temporada_id)) {
    die("No se ha seleccionado una liga o temporada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo_nombre'])) {
    $equipo_nombre = $_POST['equipo_nombre'] ?? '';

    if (empty($equipo_nombre)) {
        die("El nombre del equipo es requerido.");
    }

    // Obtener el siguiente ID de equipo disponible
    $sql_max_id = "SELECT MAX(SUBSTRING(Equipo_id, 2)) AS max_id FROM Equipo";
    $result = $conn->query($sql_max_id);
    $max_id = $result->fetch_assoc()['max_id'];
    $next_id = str_pad($max_id + 1, 3, '0', STR_PAD_LEFT);
    $equipo_id = 'E' . $next_id;

    // Insertar el nuevo equipo
    $sql_insertar_equipo = "INSERT INTO Equipo (Equipo_id, Equipo_nombre) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insertar_equipo);
    $stmt->bind_param("ss", $equipo_id, $equipo_nombre);
    $stmt->execute();

    // Asociar el equipo a la liga
    $sql_agregar_equipo_liga = "INSERT INTO Liga_Equipo (LigaEquipo_idEquipo, LigaEquipo_idLiga, Puntos_Equipo) VALUES (?, ?, 0)";
    $stmt = $conn->prepare($sql_agregar_equipo_liga);
    $stmt->bind_param("ss", $equipo_id, $liga_id);
    $stmt->execute();

    echo "Equipo agregado exitosamente.";
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Equipo</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            background-image: url('Fondo.avif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .content-container {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 5px;
            max-width: 700px;
            width: 100%;
        }
        h1, h2 {
            margin-bottom: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        form {
            display: inline;
        }
        button {
            margin-top: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            border: none;
            cursor: pointer;
        }
        a {
            color: #fff;
            text-decoration: underline;
            display: block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="content-container">
        <h1>Agregar Equipo a la Liga <?php echo htmlspecialchars($liga_id); ?> y Temporada <?php echo htmlspecialchars($temporada_id); ?></h1>

        <form action="agregar_equipo.php" method="post">
            <input type="hidden" name="liga_id" value="<?php echo htmlspecialchars($liga_id); ?>">
            <input type="hidden" name="temporada_id" value="<?php echo htmlspecialchars($temporada_id); ?>">
            <label for="equipo_nombre">Nombre del Equipo:</label>
            <input type="text" name="equipo_nombre" id="equipo_nombre" required>
            <br>
            <button type="submit">Agregar Equipo</button>
        </form>

        <a href="Pagina_administrador.html">Volver</a>
    </div>
</body>
</html>

