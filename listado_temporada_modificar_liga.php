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
$selectedComunaId = $_POST['comuna'] ?? '';

if (empty($selectedLigaId)) {
    die("No se ha seleccionado ninguna liga.");
}
$sql_temporadas_ids = "SELECT LigaTemporada_idTemporada 
                       FROM Liga_Temporada 
                       WHERE LigaTemporada_idLiga = ?";
$stmt_temporadas_ids = $conn->prepare($sql_temporadas_ids);
$stmt_temporadas_ids->bind_param("s", $selectedLigaId);
$stmt_temporadas_ids->execute();
$result_temporadas_ids = $stmt_temporadas_ids->get_result();

if (!$result_temporadas_ids) {
    die("Error en la consulta: " . $conn->error);
}

$temporadas_ids = [];
while ($row = $result_temporadas_ids->fetch_assoc()) {
    $temporadas_ids[] = $row['LigaTemporada_idTemporada'];
}

$temporadas = [];
if (!empty($temporadas_ids)) {
    foreach ($temporadas_ids as $temporada_id) {
        $sql_temporadas_names = "SELECT Temporada_id, Temporada_nombre 
                                 FROM Temporada 
                                 WHERE Temporada_id = ?";
        $stmt_temporadas_names = $conn->prepare($sql_temporadas_names);
        $stmt_temporadas_names->bind_param("s", $temporada_id);
        $stmt_temporadas_names->execute();
        $result_temporadas_names = $stmt_temporadas_names->get_result();

        if ($result_temporadas_names->num_rows > 0) {
            while ($row = $result_temporadas_names->fetch_assoc()) {
                $temporadas[] = $row;
            }
        }

        $stmt_temporadas_names->close();
    }
}


$stmt_temporadas_ids->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Temporada</title>
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
            max-width: 500px; 
            width: 100%; 
        }
        label {
            display: block;
            margin-bottom: 10px; 
        }
        select, button {
            margin-top: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            background-color: rgba(0, 0, 0, 0.7); 
            color: #fff; 
            border: none; 
            cursor: pointer;
        }
        a {
            color: #fff; 
            text-decoration: underline; 
            display: block;
            margin-top: 20px; 
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="listado_equipos_modificar_liga.php" method="post">
            <input type="hidden" name="comuna" value="<?php echo htmlspecialchars($selectedComunaId); ?>">
            <input type="hidden" name="liga" value="<?php echo htmlspecialchars($selectedLigaId); ?>">
            <label for="temporada">Elige una temporada:</label>
            <select name="temporada" id="temporada" required>
                <option value="">Seleccione una temporada</option>
                <?php foreach ($temporadas as $temporada): ?>
                    <option value="<?php echo htmlspecialchars($temporada['Temporada_id']); ?>">
                        <?php echo htmlspecialchars($temporada['Temporada_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Enviar</button>
        </form>
    </div>
    <a href="Pagina_administrador.html" style="color: #fff; text-decoration: underline;">Volver</a>
</body>
</html>

