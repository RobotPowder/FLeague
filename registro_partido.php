<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "FLeague";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el último Partido_id
$sql = "SELECT Partido_id FROM Partido ORDER BY Partido_id DESC LIMIT 1";
$result = $conn->query($sql);
$lastPartidoId = "P000";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastPartidoId = $row['Partido_id'];
}

// Incrementar el ID
$number = (int) substr($lastPartidoId, 1);
$newPartidoId = 'P' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);

// Obtener los datos del formulario
$selectedLigaId = $_POST['liga'] ?? '';
$selectedTemporadaId = $_POST['temporada'] ?? '';
$equipo_local = $_POST['equipo_local'] ?? '';
$equipo_visita = $_POST['equipo_visita'] ?? '';

if (empty($selectedLigaId) || empty($selectedTemporadaId)) {
    die("No se ha seleccionado ninguna liga o temporada.");
}

// Obtener los equipos asociados a la liga seleccionada
$sql = "SELECT LigaEquipo_idEquipo FROM Liga_Equipo WHERE LigaEquipo_idLiga = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selectedLigaId);
$stmt->execute();
$result = $stmt->get_result();

$equipos = [];
while ($row = $result->fetch_assoc()) {
    $equipos[] = $row['LigaEquipo_idEquipo'];
}

// Obtener los nombres de los equipos
$equipos_con_nombres = [];
if (!empty($equipos)) {
    $ids_placeholders = implode(',', array_fill(0, count($equipos), '?'));
    $sql_nombres = "SELECT Equipo_id, Equipo_nombre FROM Equipo WHERE Equipo_id IN ($ids_placeholders)";
    $stmt_nombres = $conn->prepare($sql_nombres);
    $types = str_repeat('s', count($equipos));
    $stmt_nombres->bind_param($types, ...$equipos);
    $stmt_nombres->execute();
    $result_nombres = $stmt_nombres->get_result();

    while ($row = $result_nombres->fetch_assoc()) {
        $equipos_con_nombres[] = $row;
    }
}

// Obtener jugadores de los equipos seleccionados
$jugadores_local = [];
$jugadores_visita = [];

if (!empty($equipo_local)) {
    $sql_jugadores_local = "SELECT Jugador_id, Jugador_nombre, Jugador_apellido 
                            FROM Jugador 
                            INNER JOIN Equipo_Jugador ON Jugador.Jugador_id = Equipo_Jugador.EquipoJugador_idJugador 
                            WHERE Equipo_Jugador.EquipoJugador_idEquipo = ?";
    $stmt_jugadores_local = $conn->prepare($sql_jugadores_local);
    $stmt_jugadores_local->bind_param("s", $equipo_local);
    $stmt_jugadores_local->execute();
    $result_jugadores_local = $stmt_jugadores_local->get_result();

    while ($row = $result_jugadores_local->fetch_assoc()) {
        $jugadores_local[] = $row;
    }
}

if (!empty($equipo_visita)) {
    $sql_jugadores_visita = "SELECT Jugador_id, Jugador_nombre, Jugador_apellido 
                             FROM Jugador 
                             INNER JOIN Equipo_Jugador ON Jugador.Jugador_id = Equipo_Jugador.EquipoJugador_idJugador 
                             WHERE Equipo_Jugador.EquipoJugador_idEquipo = ?";
    $stmt_jugadores_visita = $conn->prepare($sql_jugadores_visita);
    $stmt_jugadores_visita->bind_param("s", $equipo_visita);
    $stmt_jugadores_visita->execute();
    $result_jugadores_visita = $stmt_jugadores_visita->get_result();

    while ($row = $result_jugadores_visita->fetch_assoc()) {
        $jugadores_visita[] = $row;
    }
}

// Insertar datos en la tabla Partido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($equipo_local) && !empty($equipo_visita)) {
    $goles_local = $_POST['goles_local'] ?? 0;
    $goles_visita = $_POST['goles_visita'] ?? 0;

    $sql_partido = "INSERT INTO Partido (Partido_id, Partido_idLiga, Partido_idlocal, Partido_idvisita, Partido_goleslocal, Partido_golesvisita)
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_partido = $conn->prepare($sql_partido);
    $stmt_partido->bind_param("ssssss", $newPartidoId, $selectedLigaId, $equipo_local, $equipo_visita, $goles_local, $goles_visita);
    $stmt_partido->execute();
    $partido_id = $newPartidoId;

    // Insertar datos de los jugadores locales
    foreach ($_POST['goles_jugadores_local'] ?? [] as $jugador_id => $goles_jugador_local) {
        $asistencias_jugador_local = $_POST['asistencias_jugadores_local'][$jugador_id] ?? 0;
        $sql_partido_jugador_local = "INSERT INTO Partido_Jugador (PartJug_idpartido, PartJug_idequipo, PartJug_idjugador, PartJug_golesjugador, PartJug_asisjugador)
                                      VALUES (?, ?, ?, ?, ?)";
        $stmt_partido_jugador_local = $conn->prepare($sql_partido_jugador_local);
        $stmt_partido_jugador_local->bind_param("sssss", $partido_id, $equipo_local, $jugador_id, $goles_jugador_local, $asistencias_jugador_local);
        $stmt_partido_jugador_local->execute();
    }

    // Insertar datos de los jugadores visitantes
    foreach ($_POST['goles_jugadores_visita'] ?? [] as $jugador_id => $goles_jugador_visita) {
        $asistencias_jugador_visita = $_POST['asistencias_jugadores_visita'][$jugador_id] ?? 0;
        $sql_partido_jugador_visita = "INSERT INTO Partido_Jugador (PartJug_idpartido, PartJug_idequipo, PartJug_idjugador, PartJug_golesjugador, PartJug_asisjugador)
                                       VALUES (?, ?, ?, ?, ?)";
        $stmt_partido_jugador_visita = $conn->prepare($sql_partido_jugador_visita);
        $stmt_partido_jugador_visita->bind_param("sssss", $partido_id, $equipo_visita, $jugador_id, $goles_jugador_visita, $asistencias_jugador_visita);
        $stmt_partido_jugador_visita->execute();
    }

    // Actualizar tabla de posiciones
    $puntos_local = 0;
    $puntos_visita = 0;

    if ($goles_local > $goles_visita) {
        $puntos_local = 3;
        $puntos_visita = 0;
    } elseif ($goles_local < $goles_visita) {
        $puntos_local = 0;
        $puntos_visita = 3;
    } else {
        $puntos_local = 1;
        $puntos_visita = 1;
    }

    $sql_actualizar_puntos_local = "UPDATE Liga_Equipo 
                                    SET Puntos_Equipo = Puntos_Equipo + ? 
                                    WHERE LigaEquipo_idLiga = ? AND LigaEquipo_idEquipo = ?";
    $stmt_actualizar_puntos_local = $conn->prepare($sql_actualizar_puntos_local);
    $stmt_actualizar_puntos_local->bind_param("sss", $puntos_local, $selectedLigaId, $equipo_local);
    $stmt_actualizar_puntos_local->execute();

    $sql_actualizar_puntos_visita = "UPDATE Liga_Equipo 
                                     SET Puntos_Equipo = Puntos_Equipo + ? 
                                     WHERE LigaEquipo_idLiga = ? AND LigaEquipo_idEquipo = ?";
    $stmt_actualizar_puntos_visita = $conn->prepare($sql_actualizar_puntos_visita);
    $stmt_actualizar_puntos_visita->bind_param("sss", $puntos_visita, $selectedLigaId, $equipo_visita);
    $stmt_actualizar_puntos_visita->execute();

    echo "Datos del partido registrados exitosamente.";
}

$conn->close();
?>

