<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$selectedComunaId = $_POST['comuna'] ?? '';
$selectedLigaId = $_POST['liga'] ?? '';
$selectedTemporadaId = $_POST['temporada'] ?? '';

if (empty($selectedTemporadaId)) {
    die("No se ha seleccionado ninguna temporada.");
}

$sql_liga_equipo = "SELECT LigaEquipo_idEquipo FROM Liga_Equipo WHERE LigaEquipo_idLiga = ?";
$stmt = $conn->prepare($sql_liga_equipo);
$stmt->bind_param("s", $selectedLigaId);
$stmt->execute();
$result_liga_equipo = $stmt->get_result();

$equipos_de_liga = [];
while ($row = $result_liga_equipo->fetch_assoc()) {
    $equipos_de_liga[] = $row['LigaEquipo_idEquipo'];
}

$stmt->close();

if (empty($equipos_de_liga)) {
    die("No se encontraron equipos para la liga seleccionada.");
}

$sql_liga_comuna = "SELECT Liga_id FROM Liga WHERE Liga_comuna = ?";
$stmt = $conn->prepare($sql_liga_comuna);
$stmt->bind_param("s", $selectedComunaId);
$stmt->execute();
$result_liga_comuna = $stmt->get_result();

$ligas_de_comuna = [];
while ($row = $result_liga_comuna->fetch_assoc()) {
    $ligas_de_comuna[] = $row['Liga_id'];
}

$stmt->close();

if (!in_array($selectedLigaId, $ligas_de_comuna)) {
    die("La liga seleccionada no pertenece a la comuna seleccionada.");
}

$sql_liga_temporada = "SELECT LigaTemporada_idLiga FROM Liga_Temporada WHERE LigaTemporada_idTemporada = ?";
$stmt = $conn->prepare($sql_liga_temporada);
$stmt->bind_param("s", $selectedTemporadaId);
$stmt->execute();
$result_liga_temporada = $stmt->get_result();

$ligas_de_temporada = [];
while ($row = $result_liga_temporada->fetch_assoc()) {
    $ligas_de_temporada[] = $row['LigaTemporada_idLiga'];
}

$stmt->close();

if (!in_array($selectedLigaId, $ligas_de_temporada)) {
    die("La liga seleccionada no está asociada a la temporada seleccionada.");
}

$equipos_finales = [];
foreach ($equipos_de_liga as $equipo_id) {
    $sql_equipo = "SELECT Equipo_id, Equipo_nombre FROM Equipo WHERE Equipo_id = ?";
    $stmt = $conn->prepare($sql_equipo);
    $stmt->bind_param("s", $equipo_id);
    $stmt->execute();
    $result_equipo = $stmt->get_result();

    while ($row = $result_equipo->fetch_assoc()) {
        $equipos_finales[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Equipo</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 50px;
    background-image: url('Fondo.avif'); 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
    color: #fff; 
    min-height: 100vh; 
    margin: 0; 
}

html {
    height: 100%;
}
        h1 {
            margin-bottom: 30px;
        }
        form {
            display: inline-block;
            text-align: left;
        }
        label, select, button {
            display: block;
            margin: 10px 0;
        }
        select, button {
            width: 100%;
        }
        button {
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            color: #007BFF;
            text-decoration: none;
            font-size: 16px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <form action="editar_equipo.php" method="post">
        <input type="hidden" name="comuna" value="<?php echo htmlspecialchars($selectedComunaId); ?>">
        <input type="hidden" name="liga" value="<?php echo htmlspecialchars($selectedLigaId); ?>">
        <input type="hidden" name="temporada" value="<?php echo htmlspecialchars($selectedTemporadaId); ?>">
        <label for="equipo">Elige un equipo:</label>
        <select name="equipo" id="equipo" required>
            <option value="">Seleccione un equipo</option>
            <?php foreach ($equipos_finales as $equipo): ?>
                <option value="<?php echo htmlspecialchars($equipo['Equipo_id']); ?>">
                    <?php echo htmlspecialchars($equipo['Equipo_nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Modificar Equipo</button>
    </form>

    <a href="Pagina_administrador.html">Volver</a>
</body>
</html>

