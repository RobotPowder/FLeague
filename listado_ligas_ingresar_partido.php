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
        a {
            color: #fff; /* Color del enlace */
            text-decoration: underline; /* Subraya el enlace */
            margin-top: 20px; /* Espacio encima del enlace */
            display: block;
        }
    </style>
</head>
<body>
    <h1>Selecciona una liga</h1>

    <div class="form-container">
        <form action="listado_temporada_ingresar_partido.php" method="post">
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

    <a href="Pagina_administrador.html">Volver</a>
</body>
</html>

