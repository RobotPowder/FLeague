<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$selectedEquipoId = $_POST['equipo'] ?? '';

if (empty($selectedEquipoId)) {
    die("No se ha seleccionado ningún equipo.");
}

$sql_jugadores = "SELECT Jugador.Jugador_id, Jugador.Jugador_nombre, Jugador.Jugador_apellido 
                 FROM Jugador 
                 INNER JOIN Equipo_Jugador ON Jugador.Jugador_id = Equipo_Jugador.EquipoJugador_idJugador 
                 WHERE Equipo_Jugador.EquipoJugador_idEquipo = ?";
$stmt = $conn->prepare($sql_jugadores);
$stmt->bind_param("s", $selectedEquipoId);
$stmt->execute();
$result_jugadores = $stmt->get_result();

$jugadores_del_equipo = [];
while ($row = $result_jugadores->fetch_assoc()) {
    $jugadores_del_equipo[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Equipo</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 50px;
    background-image: url('Fondo.avif');
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
    color: #fff; 
    min-height: 100vh; 
    margin: 0;
}

html {
    height: 100%;
}
        h1, h2 {
            margin-bottom: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        button {
            padding: 8px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            color: #007BFF;
            text-decoration: none;
            font-size: 16px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2>Jugadores del equipo:</h2>
    <ul>
        <?php if (!empty($jugadores_del_equipo)): ?>
            <?php foreach ($jugadores_del_equipo as $jugador): ?>
                <li>
                    <?php echo htmlspecialchars($jugador['Jugador_nombre'] . ' ' . $jugador['Jugador_apellido']); ?>
                    <form action="eliminar_jugador.php" method="post" style="display:inline;">
                        <input type="hidden" name="jugador_id" value="<?php echo htmlspecialchars($jugador['Jugador_id']); ?>">
                        <input type="hidden" name="equipo_id" value="<?php echo htmlspecialchars($selectedEquipoId); ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay jugadores en este equipo.</p>
        <?php endif; ?>
    </ul>

    <h2>Agregar Jugador</h2>
    <form action="agregar_jugador.php" method="post">
        <input type="hidden" name="equipo_id" value="<?php echo htmlspecialchars($selectedEquipoId); ?>">
        <button type="submit">Agregar Jugador</button>
    </form>

    <a href="Pagina_administrador.html">Volver</a>
</body>
</html>

