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
$selectedTemporadaId = $_POST['temporada'] ?? '';

if (empty($selectedLigaId) || empty($selectedTemporadaId)) {
    die("No se ha seleccionado ninguna liga o temporada.");
}

$sql = "SELECT LigaEquipo_idEquipo FROM Liga_Equipo WHERE LigaEquipo_idLiga = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selectedLigaId);
$stmt->execute();
$result = $stmt->get_result();

$equipos = [];
while ($row = $result->fetch_assoc()) {
    $equipos[] = $row['LigaEquipo_idEquipo'];
}

if (!empty($equipos)) {
    $ids_placeholders = implode(',', array_fill(0, count($equipos), '?'));
    $sql_nombres = "SELECT Equipo_id, Equipo_nombre FROM Equipo WHERE Equipo_id IN ($ids_placeholders)";
    $stmt_nombres = $conn->prepare($sql_nombres);
    $types = str_repeat('s', count($equipos));
    $stmt_nombres->bind_param($types, ...$equipos);
    $stmt_nombres->execute();
    $result_nombres = $stmt_nombres->get_result();

    $equipos_con_nombres = [];
    while ($row = $result_nombres->fetch_assoc()) {
        $equipos_con_nombres[] = $row;
    }
}

$stmt->close();
$stmt_nombres->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Equipos</title>
    <style>
        html, body {
            height: 100%; /* Asegura que el body y html ocupen toda la altura del viewport */
            margin: 0; /* Elimina el margen por defecto */
        }
        body {
            background-image: url('Fondo.avif');
            background-size: cover; /* Ajusta la imagen para cubrir todo el fondo */
            background-position: center; /* Centra la imagen */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            color: #fff; /* Color del texto */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center; /* Centra el texto en la página */
        }
        .form-container {
            background: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente para el formulario */
            padding: 20px;
            border-radius: 5px;
            max-width: 600px; /* Ancho máximo del contenedor */
            width: 100%; /* Ocupa el 100% del ancho disponible hasta el máximo */
        }
        h1 {
            margin: 0 0 15px 0; /* Espaciado en la parte inferior del encabezado */
        }
        label {
            display: block;
            margin-bottom: 10px; /* Espaciado en la parte inferior de las etiquetas */
        }
        select {
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc; /* Borde de los campos de selección */
            margin-bottom: 15px; /* Espaciado en la parte inferior de los campos de selección */
        }
        button {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            background-color: rgba(0, 0, 0, 0.7); /* Fondo semi-transparente para el botón */
            color: #fff; /* Color del texto del botón */
            border: none; /* Elimina el borde por defecto */
            cursor: pointer;
        }
        button:hover {
            background-color: rgba(0, 0, 0, 0.9); /* Fondo más oscuro al pasar el mouse */
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Selecciona los Equipos para el Partido</h1>

        <form action="estadisticas.php" method="post">
            <input type="hidden" name="liga" value="<?php echo htmlspecialchars($selectedLigaId); ?>">
            <input type="hidden" name="temporada" value="<?php echo htmlspecialchars($selectedTemporadaId); ?>">

            <label for="equipo_local">Equipo Local:</label>
            <select name="equipo_local" id="equipo_local" required>
                <option value="">Seleccione un equipo</option>
                <?php foreach ($equipos_con_nombres as $equipo): ?>
                    <option value="<?php echo htmlspecialchars($equipo['Equipo_id']); ?>">
                        <?php echo htmlspecialchars($equipo['Equipo_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="equipo_visita">Equipo Visita:</label>
            <select name="equipo_visita" id="equipo_visita" required>
                <option value="">Seleccione un equipo</option>
                <?php foreach ($equipos_con_nombres as $equipo): ?>
                    <option value="<?php echo htmlspecialchars($equipo['Equipo_id']); ?>">
                        <?php echo htmlspecialchars($equipo['Equipo_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>

