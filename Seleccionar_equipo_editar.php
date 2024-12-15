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

if (empty($liga_id) || empty($temporada_id)) {
    die("No se ha seleccionado una liga o temporada.");
}

// Obtener equipos en la liga y temporada seleccionadas
$sql_equipos = "SELECT Equipo.Equipo_id, Equipo.Equipo_nombre
                FROM Equipo
                INNER JOIN Liga_Equipo ON Equipo.Equipo_id = Liga_Equipo.LigaEquipo_idEquipo
                WHERE Liga_Equipo.LigaEquipo_idLiga = ?";
$stmt = $conn->prepare($sql_equipos);
$stmt->bind_param("s", $liga_id);
$stmt->execute();
$result_equipos = $stmt->get_result();

$equipos_en_liga = [];
while ($row = $result_equipos->fetch_assoc()) {
    $equipos_en_liga[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Equipos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('Fondo.avif');
            background-size: cover;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #00ff00;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #f2f2f2;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        button {
            background-color: #ff6666;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #ff4d4d;
        }
        form {
            margin: 0;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #004080;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #003366;
        }
    </style>
</head>
<body>

    <h2>Equipos en la liga:</h2>
    <ul>
        <?php if (!empty($equipos_en_liga)): ?>
            <?php foreach ($equipos_en_liga as $equipo): ?>
                <li>
                    <?php echo htmlspecialchars($equipo['Equipo_nombre']); ?>
                    <form action="Eliminar_equipo.php" method="post" style="display:inline;">
                        <input type="hidden" name="equipo_id" value="<?php echo htmlspecialchars($equipo['Equipo_id']); ?>">
                        <input type="hidden" name="liga_id" value="<?php echo htmlspecialchars($liga_id); ?>">
                        <input type="hidden" name="temporada_id" value="<?php echo htmlspecialchars($temporada_id); ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay equipos en esta liga y temporada.</p>
        <?php endif; ?>
    </ul>

    <h2>Agregar Equipo</h2>
    <form action="agregar_equipo.php" method="post">
        <input type="hidden" name="liga_id" value="<?php echo htmlspecialchars($liga_id); ?>">
        <input type="hidden" name="temporada_id" value="<?php echo htmlspecialchars($temporada_id); ?>">
        <button type="submit">Agregar Equipo</button>
    </form>

    <a href="Pagina_administrador.html">Volver</a>
</body>
</html>

