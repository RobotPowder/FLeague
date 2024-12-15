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

// Obtener los IDs de las temporadas asociadas a la liga seleccionada
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

// Crear una consulta separada para cada temporada
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

// Cerrar la conexión
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
    <h1>Selecciona una temporada </h1>

    <div class="form-container">
        <form action="seleccionar_equipos.php" method="post">
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

    <a href="Pagina_administrador.html">Volver</a>
</body>
</html>

