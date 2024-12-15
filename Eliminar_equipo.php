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
$equipo_id = $_POST['equipo_id'] ?? '';

if (empty($liga_id) || empty($temporada_id) || empty($equipo_id)) {
    die("Datos insuficientes.");
}

// Eliminar el equipo de la liga
$sql = "DELETE FROM Liga_Equipo WHERE LigaEquipo_idLiga = ? AND LigaEquipo_idEquipo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $liga_id, $equipo_id);

if ($stmt->execute()) {
    echo "Equipo eliminado correctamente.";
} else {
    echo "Error al eliminar el equipo: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
<a href="Paginda_administrador.html">Volver</a>

