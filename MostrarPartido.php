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
$equipoLocalId = $_POST['equipo_local'] ?? '';
$equipoVisitanteId = $_POST['equipo_visitante'] ?? '';

if (empty($selectedLigaId) || empty($equipoLocalId) || empty($equipoVisitanteId)) {
    die("Faltan datos para mostrar los partidos.");
}

$sql_local = "SELECT Equipo_nombre FROM Equipo WHERE Equipo_id = ?";
$stmt_local = $conn->prepare($sql_local);
$stmt_local->bind_param("s", $equipoLocalId);
$stmt_local->execute();
$result_local = $stmt_local->get_result();
$equipoLocalNombre = $result_local->fetch_assoc()['Equipo_nombre'];
$stmt_local->close();
$sql_visitante = "SELECT Equipo_nombre FROM Equipo WHERE Equipo_id = ?";
$stmt_visitante = $conn->prepare($sql_visitante);
$stmt_visitante->bind_param("s", $equipoVisitanteId);
$stmt_visitante->execute();
$result_visitante = $stmt_visitante->get_result();
$equipoVisitanteNombre = $result_visitante->fetch_assoc()['Equipo_nombre'];
$stmt_visitante->close();


$sql_partidos = "SELECT Partido_goleslocal, Partido_golesvisita 
                 FROM Partido 
                 WHERE Partido_idLiga = ? AND Partido_idlocal = ? AND Partido_idvisita = ?";
$stmt_partidos = $conn->prepare($sql_partidos);
$stmt_partidos->bind_param("sss", $selectedLigaId, $equipoLocalId, $equipoVisitanteId);
$stmt_partidos->execute();
$result_partidos = $stmt_partidos->get_result();

$partidos = [];
while ($row = $result_partidos->fetch_assoc()) {
    $resultado = $equipoLocalNombre . " " . $row['Partido_goleslocal'] . " - " . $row['Partido_golesvisita'] . " " . $equipoVisitanteNombre;
    $partidos[] = ['resultado' => $resultado];
}

$stmt_partidos->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Partidos entre Equipos</title>
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
        table {
            background-color: rgba(255, 255, 255, 0.8); 
            border-collapse: collapse;
            margin: 20px 0;
            width: 100%;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            color: #000; 
        }
        p {
            background-color: rgba(0, 0, 0, 0.7); 
            padding: 10px;
            border-radius: 5px;
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
        <h1>Partidos entre <?php echo htmlspecialchars($equipoLocalNombre); ?> y <?php echo htmlspecialchars($equipoVisitanteNombre); ?></h1>

        <?php if (!empty($partidos)): ?>
            <table>
                <tr>
                    <th>Resultado</th>
                </tr>
                <?php foreach ($partidos as $partido): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($partido['resultado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No se encontraron partidos entre estos equipos.</p>
        <?php endif; ?>

        <a href="Pagina_principal.html">Volver</a>
    </div>
</body>
</html>

