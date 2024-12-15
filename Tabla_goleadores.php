<?php
$selectedTemporadaId = $_POST['temporada'] ?? '';
$selectedLigaId = $_POST['liga'] ?? '';

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los equipos que pertenecen a la liga seleccionada
$sql_equipos = "SELECT LigaEquipo_idEquipo 
               FROM Liga_Equipo 
               WHERE LigaEquipo_idLiga = ?";
$stmt_equipos = $conn->prepare($sql_equipos);
$stmt_equipos->bind_param("s", $selectedLigaId);
$stmt_equipos->execute();
$result_equipos = $stmt_equipos->get_result();

$equipos = [];
while ($row = $result_equipos->fetch_assoc()) {
    $equipos[] = $row['LigaEquipo_idEquipo'];
}

$jugadores_info = [];
if (!empty($equipos)) {
    foreach ($equipos as $equipo_id) {
        // Obtener los jugadores que pertenecen a cada equipo
        $sql_jugadores = "SELECT EquipoJugador_idJugador 
                          FROM Equipo_Jugador 
                          WHERE EquipoJugador_idEquipo = ?";
        $stmt_jugadores = $conn->prepare($sql_jugadores);
        $stmt_jugadores->bind_param("s", $equipo_id);
        $stmt_jugadores->execute();
        $result_jugadores = $stmt_jugadores->get_result();

        while ($row = $result_jugadores->fetch_assoc()) {
            $jugador_id = $row['EquipoJugador_idJugador'];

            // Obtener el nombre del jugador
            $sql_jugador = "SELECT Jugador_nombre, Jugador_apellido 
                            FROM Jugador 
                            WHERE Jugador_id = ?";
            $stmt_jugador = $conn->prepare($sql_jugador);
            $stmt_jugador->bind_param("s", $jugador_id);
            $stmt_jugador->execute();
            $result_jugador = $stmt_jugador->get_result();
            $jugador_info = $result_jugador->fetch_assoc();
            $jugador_nombre_completo = $jugador_info['Jugador_nombre'] . ' ' . $jugador_info['Jugador_apellido'];

            // Obtener el nombre del equipo
            $sql_equipo_nombre = "SELECT Equipo_nombre 
                                  FROM Equipo 
                                  WHERE Equipo_id = ?";
            $stmt_equipo_nombre = $conn->prepare($sql_equipo_nombre);
            $stmt_equipo_nombre->bind_param("s", $equipo_id);
            $stmt_equipo_nombre->execute();
            $result_equipo_nombre = $stmt_equipo_nombre->get_result();
            $equipo_nombre = $result_equipo_nombre->fetch_assoc()['Equipo_nombre'];

            // Guardar la información del jugador y su equipo
            $jugadores_info[] = [
                'jugador_id' => $jugador_id,
                'jugador_nombre' => $jugador_nombre_completo,
                'equipo_nombre' => $equipo_nombre,
            ];
        }
    }
}

$goleadores = [];
foreach ($jugadores_info as $jugador) {
    // Obtener la suma de goles del jugador en la liga seleccionada
    $sql_goles = "SELECT SUM(PartJug_golesjugador) as goles 
                  FROM Partido_Jugador 
                  WHERE PartJug_idjugador = ? 
                  AND PartJug_idpartido IN (
                      SELECT Partido_id 
                      FROM Partido 
                      WHERE Partido_idLiga = ?
                  )";
    $stmt_goles = $conn->prepare($sql_goles);
    $stmt_goles->bind_param("ss", $jugador['jugador_id'], $selectedLigaId);
    $stmt_goles->execute();
    $result_goles = $stmt_goles->get_result();
    $goles = $result_goles->fetch_assoc()['goles'] ?? 0;

    $goleadores[] = [
        'jugador_nombre' => $jugador['jugador_nombre'],
        'equipo_nombre' => $jugador['equipo_nombre'],
        'goles' => $goles
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Goleadores</title>
    <style>
        body {
            background-image: url('Fondo.avif');
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            margin: 50px auto;
            padding: 20px;
            width: 80%;
            border-radius: 8px;
        }
        h1 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #999;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: white;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #45a049;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="Logo.jpeg" alt="Logo FLeague" class="logo">
        <h1>Tabla de Goleadores</h1>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Jugador</th>
                    <th>Equipo</th>
                    <th>Goles totales</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($goleadores)): ?>
                    <?php foreach ($goleadores as $goleador): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($goleador['jugador_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($goleador['equipo_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($goleador['goles']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No se encontraron goleadores en esta liga.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="Pagina_principal.html">Volver</a>
    </div>
</body>
</html>

