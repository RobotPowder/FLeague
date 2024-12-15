<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT Comuna_id, Comuna_Nombre FROM Comuna";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$comunas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comunas[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Comuna</title>
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
    </style>
</head>
<body>
    <h1>Selecciona una Comuna</h1>

    <div class="form-container">
        <form action="listado_ligas_goleadores.php" method="post">
            <label for="comuna">Elige una comuna:</label>
            <select name="comuna" id="comuna" required>
                <option value="">Seleccione una comuna</option>
                <?php foreach ($comunas as $comuna): ?>
                    <option value="<?php echo htmlspecialchars($comuna['Comuna_id']); ?>">
                        <?php echo htmlspecialchars($comuna['Comuna_Nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Enviar</button>
        </form>
    </div>
<a href="Pagina_principal.html">Volver</a>
</body>
</html>

