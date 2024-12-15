<?php
// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'FLeague');

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$equipo_local = $_POST['equipo_local'];
$sql_local = "SELECT Jugador_id, CONCAT(Jugador_nombre, ' ', Jugador_apellido) AS nombre_completo
              FROM Jugador
              WHERE Jugador_id IN (
                  SELECT EquipoJugador_idJugador 
                  FROM Equipo_Jugador 
                  WHERE EquipoJugador_idEquipo = '$equipo_local'
              )";
$result_local = $conn->query($sql_local);

$jugadores_local = [];
if ($result_local->num_rows > 0) {
    while($row = $result_local->fetch_assoc()) {
        $jugadores_local[] = $row;
    }
}

$equipo_visita = $_POST['equipo_visita'];
$sql_visita = "SELECT Jugador_id, CONCAT(Jugador_nombre, ' ', Jugador_apellido) AS nombre_completo
               FROM Jugador
               WHERE Jugador_id IN (
                   SELECT EquipoJugador_idJugador 
                   FROM Equipo_Jugador 
                   WHERE EquipoJugador_idEquipo = '$equipo_visita'
               )";
$result_visita = $conn->query($sql_visita);

$jugadores_visita = [];
if ($result_visita->num_rows > 0) {
    while($row = $result_visita->fetch_assoc()) {
        $jugadores_visita[] = $row;
    }
}

$equipo_local_nombre = "Nombre del Equipo Local"; 
$equipo_visita_nombre = "Nombre del Equipo Visita"; 

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresar Estadísticas del Partido</title>
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
        .form-container {
            background: rgba(0, 0, 0, 0.5); 
            padding: 20px;
            border-radius: 5px;
            max-width: 600px; 
            width: 100%; 
        }
        h1, h2 {
            margin: 0 0 15px 0;
        }
        div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px; 
        }
        input[type="number"] {
            margin-right: 10px; 
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc; 
        }
        button {
            margin-top: 15px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            background-color: rgba(0, 0, 0, 0.7); 
            color: #fff; 
            border: none; 
            cursor: pointer;
        }
        button:hover {
            background-color: rgba(0, 0, 0, 0.9);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Ingresar Estadísticas del Partido</h1>

        <form action="ingresar_partido.php" method="post">
            <input type="hidden" name="liga" value="<?php echo htmlspecialchars($_POST['liga']); ?>">
            <input type="hidden" name="temporada" value="<?php echo htmlspecialchars($_POST['temporada']); ?>">
            <input type="hidden" name="equipo_local" value="<?php echo htmlspecialchars($_POST['equipo_local']); ?>">
            <input type="hidden" name="equipo_visita" value="<?php echo htmlspecialchars($_POST['equipo_visita']); ?>">

            <h2>Equipo Local: <?php echo htmlspecialchars($equipo_local_nombre); ?></h2>
            <?php foreach ($jugadores_local as $jugador): ?>
                <div>
                    <label><?php echo htmlspecialchars($jugador['nombre_completo']); ?> </label>
                    <input type="number" name="goles_<?php echo htmlspecialchars($jugador['Jugador_id']); ?>" placeholder="Goles">
                    <input type="number" name="asistencias_<?php echo htmlspecialchars($jugador['Jugador_id']); ?>" placeholder="Asistencias">
                </div>
            <?php endforeach; ?>

            <h2>Equipo Visita: <?php echo htmlspecialchars($equipo_visita_nombre); ?></h2>
            <?php foreach ($jugadores_visita as $jugador): ?>
                <div>
                    <label><?php echo htmlspecialchars($jugador['nombre_completo']); ?></label>
                    <input type="number" name="goles_<?php echo htmlspecialchars($jugador['Jugador_id']); ?>" placeholder="Goles">
                    <input type="number" name="asistencias_<?php echo htmlspecialchars($jugador['Jugador_id']); ?>" placeholder="Asistencias">
                </div>
            <?php endforeach; ?>

            <h2>Resultado del Partido</h2>
            <label for="goles_local">Goles Equipo Local:</label>
            <input type="number" id="goles_local" name="goles_local" required><br>
            <label for="goles_visita">Goles Equipo Visita:</label>
            <input type="number" id="goles_visita" name="goles_visita" required><br>

            <button type="submit">Guardar Estadísticas</button>
        </form>
    </div>
</body>
</html>

