<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el id de la comuna seleccionada desde la solicitud POST
$selectedComunaId = $_POST['comuna'] ?? '';

if (empty($selectedComunaId)) {
    die("No se ha seleccionado ninguna comuna.");
}

// Preparar la consulta SQL para obtener las ligas basadas en el Comuna_id
$sql = "SELECT Liga_id, Liga_nombre 
        FROM Liga 
        WHERE Liga_comuna = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selectedComunaId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Crear un array para las ligas
$ligas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ligas[] = $row;
    }
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Liga</title>
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
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label, select, button {
            margin-top: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }
        select {
            width: 100%;
            max-width: 300px; 
        }
        button {
            background-color: rgba(0, 0, 0, 0.7); 
            color: #fff; 
            border: none;
            cursor: pointer;
            margin-top: 20px; /
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

        <form action="listado_temporadas_partido.php" method="post">
            <input type="hidden" name="comuna" value="<?php echo htmlspecialchars($selectedComunaId); ?>">
            <label for="liga">Elige una liga:</label>
            <select name="liga" id="liga" required>
                <option value="">Seleccione una liga</option>
                <?php foreach ($ligas as $liga): ?>
                    <option value="<?php echo htmlspecialchars($liga['Liga_id']); ?>">
                        <?php echo htmlspecialchars($liga['Liga_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Enviar</button>
        </form>

        <a href="Pagina_principal.html">Volver</a>
    </div>
</body>
</html>

