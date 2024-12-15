<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$selectedComunaId = $_POST['comuna'] ?? '';

if (empty($selectedComunaId)) {
    die("No se ha seleccionado ninguna comuna.");
}

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

$ligas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ligas[] = $row;
    }
}

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
        <form action="listado_temporadas.php" method="post">
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
    </div>

<a href="Pagina_principal.html">Volver</a>
</body>
</html>

