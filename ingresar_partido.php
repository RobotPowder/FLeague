<?php
// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'FLeague');

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$liga = $_POST['liga'];
$temporada = $_POST['temporada'];
$equipo_local = $_POST['equipo_local'];
$equipo_visita = $_POST['equipo_visita'];
$goles_local = $_POST['goles_local'];
$goles_visita = $_POST['goles_visita'];

$result = $conn->query("SELECT COUNT(*) AS total FROM Partido");
$row = $result->fetch_assoc();
$partido_id = 'P' . str_pad((string)($row['total'] + 1), 3, '0', STR_PAD_LEFT);

$sql_partido = "INSERT INTO Partido (Partido_id, Partido_idLiga, Partido_idlocal, Partido_idvisita, Partido_goleslocal, Partido_golesvisita)
                VALUES ('$partido_id', '$liga', '$equipo_local', '$equipo_visita', '$goles_local', '$goles_visita')";
if (!$conn->query($sql_partido)) {
    die("Error al insertar el partido: " . $conn->error);
}

// Obtener los jugadores del equipo local
$sql_local = "SELECT j.Jugador_id
              FROM Jugador j
              INNER JOIN Equipo_Jugador ej ON j.Jugador_id = ej.EquipoJugador_idJugador
              WHERE ej.EquipoJugador_idEquipo = '$equipo_local'";
$result_local = $conn->query($sql_local);

$jugadores_local = [];
if ($result_local->num_rows > 0) {
    while ($row = $result_local->fetch_assoc()) {
        $jugadores_local[] = $row['Jugador_id'];
    }
}

// Obtener los jugadores del equipo visita
$sql_visita = "SELECT j.Jugador_id
               FROM Jugador j
               INNER JOIN Equipo_Jugador ej ON j.Jugador_id = ej.EquipoJugador_idJugador
               WHERE ej.EquipoJugador_idEquipo = '$equipo_visita'";
$result_visita = $conn->query($sql_visita);

$jugadores_visita = [];
if ($result_visita->num_rows > 0) {
    while ($row = $result_visita->fetch_assoc()) {
        $jugadores_visita[] = $row['Jugador_id'];
    }
}

foreach ($_POST as $key => $value) {
    if (strpos($key, 'goles_') === 0 && $value !== '') {
        $jugador_id = substr($key, 6);
        $goles = $value;
        $asistencias = $_POST['asistencias_' . $jugador_id] ?? 0;
        if (in_array($jugador_id, $jugadores_local)) {
            $sql_jugador = "INSERT INTO Partido_Jugador (PartJug_idpartido, PartJug_idequipo, PartJug_idjugador, PartJug_golesjugador, PartJug_asisjugador)
                            VALUES ('$partido_id', '$equipo_local', '$jugador_id', '$goles', '$asistencias')";
            if (!$conn->query($sql_jugador)) {
                die("Error al insertar los datos del jugador local: " . $conn->error);
            }
        }
    }
}

foreach ($_POST as $key => $value) {
    if (strpos($key, 'goles_') === 0 && $value !== '') {
        $jugador_id = substr($key, 6);
        $goles = $value;
        $asistencias = $_POST['asistencias_' . $jugador_id] ?? 0;
        if (in_array($jugador_id, $jugadores_visita)) {
            $sql_jugador = "INSERT INTO Partido_Jugador (PartJug_idpartido, PartJug_idequipo, PartJug_idjugador, PartJug_golesjugador, PartJug_asisjugador)
                            VALUES ('$partido_id', '$equipo_visita', '$jugador_id', '$goles', '$asistencias')";
            if (!$conn->query($sql_jugador)) {
                die("Error al insertar los datos del jugador visitante: " . $conn->error);
            }
        }
    }
}

if ((int)$goles_local > (int)$goles_visita) {
    $sql_update_local = "UPDATE Liga_Equipo SET Puntos_Equipo = Puntos_Equipo + 3 WHERE LigaEquipo_idEquipo = '$equipo_local' AND LigaEquipo_idLiga = '$liga'";
    $sql_update_visita = "UPDATE Liga_Equipo SET Puntos_Equipo = Puntos_Equipo WHERE LigaEquipo_idEquipo = '$equipo_visita' AND LigaEquipo_idLiga = '$liga'";
} elseif ((int)$goles_local < (int)$goles_visita) {
    $sql_update_local = "UPDATE Liga_Equipo SET Puntos_Equipo = Puntos_Equipo WHERE LigaEquipo_idEquipo = '$equipo_local' AND LigaEquipo_idLiga = '$liga'";
    $sql_update_visita = "UPDATE Liga_Equipo SET Puntos_Equipo = Puntos_Equipo + 3 WHERE LigaEquipo_idEquipo = '$equipo_visita' AND LigaEquipo_idLiga = '$liga'";
} else {
    $sql_update_local = "UPDATE Liga_Equipo SET Puntos_Equipo = Puntos_Equipo + 1 WHERE LigaEquipo_idEquipo = '$equipo_local' AND LigaEquipo_idLiga = '$liga'";
    $sql_update_visita = "UPDATE Liga_Equipo SET Puntos_Equipo = Puntos_Equipo + 1 WHERE LigaEquipo_idEquipo = '$equipo_visita' AND LigaEquipo_idLiga = '$liga'";
}


if (!$conn->query($sql_update_local)) {
    die("Error al actualizar puntos para el equipo local: " . $conn->error);
}
if (!$conn->query($sql_update_visita)) {
    die("Error al actualizar puntos para el equipo visitante: " . $conn->error);
} 

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación</title>
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
        .confirmation-container {
            background: rgba(0, 0, 0, 0.5); 
            padding: 20px;
            border-radius: 5px;
            max-width: 500px; 
            width: 100%;
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
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h1>Los datos del partido se guardaron correctamente.</h1>
        <form action="Pagina_administrador.html" method="get">
            <button type="submit">Volver</button>
        </form>
    </div>
</body>
</html>

