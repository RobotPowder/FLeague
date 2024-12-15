<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$jugador_id = $_POST['jugador_id'] ?? '';
$equipo_id = $_POST['equipo_id'] ?? '';

if (empty($jugador_id) || empty($equipo_id)) {
    die("No se han proporcionado datos suficientes.");
}

// Primero, intentar eliminar el jugador del equipo
$sql_eliminar_jugador_equipo = "DELETE FROM Equipo_Jugador WHERE EquipoJugador_idJugador = ? AND EquipoJugador_idEquipo = ?";
$stmt = $conn->prepare($sql_eliminar_jugador_equipo);
$stmt->bind_param("ss", $jugador_id, $equipo_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Jugador eliminado del equipo exitosamente.<br>";
} else {
    echo "El jugador no estaba asociado a este equipo o no se pudo eliminar.<br>";
}

// Luego, intentar eliminar el jugador de la tabla Jugador
$sql_eliminar_jugador = "DELETE FROM Jugador WHERE Jugador_id = ?";
$stmt = $conn->prepare($sql_eliminar_jugador);
$stmt->bind_param("s", $jugador_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Jugador eliminado exitosamente.";
} else {
    echo "No se pudo eliminar el jugador o el jugador no existe.";
}

$stmt->close();
$conn->close();

echo "<br><a href='modificar_equipo.php?equipo=" . htmlspecialchars($equipo_id) . "'>Volver a modificar el equipo</a>";
?>

