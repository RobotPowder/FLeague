<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el id de la liga y la temporada desde la solicitud POST
$selectedLigaId = $_POST['liga'] ?? '';
$selectedTemporadaId = $_POST['temporada'] ?? '';

if (empty($selectedLigaId) || empty($selectedTemporadaId)) {
    die("No se ha seleccionado liga o temporada.");
}

// Preparar la consulta SQL para obtener los equipos clasificados
$sql = "SELECT 
            e.Equipo_nombre, 
            le.Puntos_Equipo
        FROM 
            Equipo e,
            Liga_Equipo le
        WHERE 
            le.LigaEquipo_idEquipo = e.Equipo_id
            AND le.LigaEquipo_idLiga IN (
                SELECT LigaTemporada_idLiga
                FROM Liga_Temporada
                WHERE LigaTemporada_idTemporada = ?
            )
            AND le.LigaEquipo_idLiga = ? 
        ORDER BY 
            le.Puntos_Equipo DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $selectedTemporadaId, $selectedLigaId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Crear un array para los equipos clasificados
$equipos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $equipos[] = $row;
    }
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clasificación de Equipos</title>
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
        .container {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 800px;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #fff;
            text-align: center;
        }
        th {
            background-color: rgba(255, 255, 255, 0.2);
        }
        a {
            color: #fff;
            margin-top: 20px;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">

        <table>
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($equipos)): ?>
                    <?php foreach ($equipos as $equipo): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($equipo['Equipo_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($equipo['Puntos_Equipo']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No hay equipos para mostrar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="Pagina_principal.html">Volver</a>
    </div>
</body>
</html>


