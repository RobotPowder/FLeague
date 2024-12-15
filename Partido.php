<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$selectedLigaId = $_POST['liga'] ?? '';
$selectedComunaId = $_POST['comuna'] ?? '';
$selectedTemporadaId = $_POST['temporada'] ?? '';

if (empty($selectedLigaId) || empty($selectedTemporadaId)) {
    die("No se ha seleccionado una liga o una temporada.");
}

$sql_equipos_ids = "SELECT LigaEquipo_idEquipo 
                    FROM Liga_Equipo 
                    WHERE LigaEquipo_idLiga = ?";
$stmt_equipos_ids = $conn->prepare($sql_equipos_ids);
$stmt_equipos_ids->bind_param("s", $selectedLigaId);
$stmt_equipos_ids->execute();
$result_equipos_ids = $stmt_equipos_ids->get_result();

$equipos_ids = [];
while ($row = $result_equipos_ids->fetch_assoc()) {
    $equipos_ids[] = $row['LigaEquipo_idEquipo'];
}

$equipos = [];
if (!empty($equipos_ids)) {
    $ids_placeholders = implode(',', array_fill(0, count($equipos_ids), '?'));
    $sql_equipos_names = "SELECT Equipo_id, Equipo_nombre 
                          FROM Equipo 
                          WHERE Equipo_id IN ($ids_placeholders)";
    $stmt_equipos_names = $conn->prepare($sql_equipos_names);
    $types = str_repeat('s', count($equipos_ids));
    $stmt_equipos_names->bind_param($types, ...$equipos_ids);
    $stmt_equipos_names->execute();
    $result_equipos_names = $stmt_equipos_names->get_result();

    while ($row = $result_equipos_names->fetch_assoc()) {
        $equipos[] = $row;
    }
}

$stmt_equipos_ids->close();
$stmt_equipos_names->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Equipos</title>
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
        h1 {
            margin-bottom: 20px; 
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label, select, button {
            margin-top: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }
        select {
            width: 100%;
            max-width: 300px;
        }
        button {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff; 
            border: none; 
            cursor: pointer;
            margin-top: 20px;
        }
        a {
            color: #fff; 
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="content-container">

        <form action="MostrarPartido.php" method="post">
            <input type="hidden" name="comuna" value="<?php echo htmlspecialchars($selectedComunaId); ?>">
            <input type="hidden" name="liga" value="<?php echo htmlspecialchars($selectedLigaId); ?>">
            <input type="hidden" name="temporada" value="<?php echo htmlspecialchars($selectedTemporadaId); ?>">

            <label for="equipo_local">Equipo Local:</label>
            <select name="equipo_local" id="equipo_local" required>
                <option value="">Seleccione un equipo</option>
                <?php foreach ($equipos as $equipo): ?>
                    <option value="<?php echo htmlspecialchars($equipo['Equipo_id']); ?>">
                        <?php echo htmlspecialchars($equipo['Equipo_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="equipo_visitante">Equipo Visitante:</label>
            <select name="equipo_visitante" id="equipo_visitante" required>
                <option value="">Seleccione un equipo</option>
                <?php foreach ($equipos as $equipo): ?>
                    <option value="<?php echo htmlspecialchars($equipo['Equipo_id']); ?>">
                        <?php echo htmlspecialchars($equipo['Equipo_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Mostrar Partidos</button>
        </form>

        <a href="Pagina_principal.html">Volver</a>
    </div>
</body>
</html>

