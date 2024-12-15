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
            height: 100%; /* Asegura que el body y html ocupen toda la altura del viewport */
            margin: 0; /* Elimina el margen por defecto */
        }
        body {
            background-image: url('Fondo.avif');
            background-size: cover; /* Ajusta la imagen para cubrir todo el fondo */
            background-position: center; /* Centra la imagen */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            color: #fff; /* Color del texto */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center; /* Centra el texto en la página */
        }
        .form-container {
            background: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente para el formulario */
            padding: 20px;
            border-radius: 5px;
            max-width: 500px; /* Ancho máximo del formulario */
            width: 100%; /* Ocupa el 100% del ancho disponible hasta el máximo */
        }
        label {
            display: block;
            margin-bottom: 10px; /* Espacio debajo de las etiquetas */
        }
        select, button {
            margin-top: 10px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            background-color: rgba(0, 0, 0, 0.7); /* Fondo semi-transparente para el botón */
            color: #fff; /* Color del texto del botón */
            border: none; /* Elimina el borde por defecto */
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Selecciona una Comuna</h1>

    <div class="form-container">
        <form action="listado_ligas_ingresar_partido.php" method="post">
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
</body>
</html>

