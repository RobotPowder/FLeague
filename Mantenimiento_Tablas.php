<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require ('conexion.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $opcion = $_POST["opcion"];

    switch ($opcion) {
        case "crearComuna":
            crearComuna();
            break;

        case "modificarComuna":
            modificarComuna();
            break;

        case "eliminarComuna":
            eliminarComuna();
            break;

        case "crearTemporada":
            crearTemporada();
            break;

        case "modificarTemporada":
            modificarTemporada();
            break;

        case "eliminarTemporada":
            eliminarTemporada();
            break;
        
        case "crearLiga":
            crearLiga();
            break;

        case "modificarLiga":
            modificarLiga();
            break;

        case "eliminarLiga":
            eliminarLiga();
            break;

        default:
            echo "Opción no válida";
            break;
    }
}

function crearComuna() {
    global $conn;
    $Comuna_id = $_POST["Comuna_id"];
    $Comuna_Nombre = $_POST["Comuna_Nombre"];
    echo "$Comuna_Nombre";

    $sql_verificar = "SELECT Comuna_id FROM Comuna WHERE Comuna_id = '$Comuna_id'";
    $result = $conn->query($sql_verificar);

    if ($result->num_rows > 0) {
        echo "Error: Ya existe una comuna con este ID";
    } else {
        $sql = "INSERT INTO Comuna (Comuna_id, Comuna_Nombre) VALUES ('$Comuna_id', '$Comuna_Nombre')";

        if ($conn->query($sql) === TRUE) {
            echo "Comuna creada correctamente";
        } else {
            echo "Error al crear la comuna: " . $conn->error;
        }
    }
}


function modificarComuna() {
    global $conn;
    $Comuna_Nombre = $_POST["Comuna_Nombre"];
    $Comuna_nombre_new = $_POST["Comuna_nombre_new"];

    $sql = "UPDATE Comuna SET Comuna_Nombre = '$Comuna_nombre_new' WHERE Comuna_Nombre = '$Comuna_Nombre'";

    if ($conn->query($sql) === TRUE) {
        echo "Comuna modificada correctamente";
    } else {
        echo "Error al modificar la comuna: " . $conn->error;
    }
}



function eliminarComuna() {
    global $conn;
    $Comuna_id = $_POST["Comuna_id"];


    try {
        $sql = "SELECT Liga_id FROM Liga WHERE Liga_comuna = '$Comuna_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            throw new Exception("No se puede eliminar la comuna, ya que está asociada a una o más ligas.");
        }

        
        $sql = "DELETE FROM Comuna WHERE Comuna_id = '$Comuna_id'";
        $conn->query($sql);

        $conn->commit();
        echo "Comuna eliminada";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error al eliminar la comuna: " . $e->getMessage();
    }
}


function crearLiga() { //Funciona
    global $conn;
    $Liga_id = $_POST["Liga_id"];
    $Liga_nombre = $_POST["Liga_nombre"];
    $Liga_comuna = $_POST["Liga_comuna"];
    echo "$Liga_comuna $Liga_nombre $Liga_id";

   
    $sql_comuna = "SELECT Comuna_id FROM Comuna WHERE Comuna_id = '$Liga_comuna'";
    $result = $conn->query($sql_comuna);

    if ($result->num_rows > 0) {
       
        $sql = "INSERT INTO Liga (Liga_id, Liga_nombre, Liga_comuna) VALUES ('$Liga_id', '$Liga_nombre', '$Liga_comuna')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Liga creada";
        } else {
            echo "Error al crear la liga: " . $conn->error;
        }
    } else {
        echo "Error: La comuna especificada no existe.";
    }
}

function modificarLiga() { //Funciona
    global $conn;
    $Liga_comuna = $_POST["Liga_comuna"];
    $Liga_nombre = $_POST["Liga_nombre"];
    $Liga_nombre_new = $_POST["Liga_nombre_new"];
    

    $sql_comuna = "SELECT Liga_comuna FROM Liga WHERE Liga_comuna = '$Liga_comuna'";
    $result = $conn->query($sql_comuna);

    if ($result->num_rows > 0) {
        $sql = "UPDATE Liga SET Liga_nombre = '$Liga_nombre_new' WHERE Liga_nombre = '$Liga_nombre'";

        if ($conn->query($sql) === TRUE) {
            echo "Liga modificada";
        } else {
            echo "Error al modificar la liga: " . $conn->error;
        }
    }
     else {
        echo "La comuna especificada no existe.";
    }
}


function eliminarLiga() {
    global $conn;
    $Liga_id = $_POST["Liga_id"];

   

    try {
        
        $sql = "DELETE FROM Liga_Equipo WHERE LigaEquipo_idLiga = '$Liga_id'";
        $conn->query($sql);

       
        $sql = "DELETE FROM Liga_Temporada WHERE LigaTemporada_idLiga = '$Liga_id'";
        $conn->query($sql);

        
        $sql = "DELETE FROM Partido WHERE Partido_idLiga = '$Liga_id'";
        $conn->query($sql);

        
        $sql = "DELETE FROM Admin_Liga WHERE AdminLiga_idliga = '$Liga_id'";
        $conn->query($sql);
        $sql = "DELETE FROM Liga WHERE Liga_id = '$Liga_id'";
        $conn->query($sql);

        $conn->commit();
        echo "Liga eliminada";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error al eliminar la liga: " . $e->getMessage();
    }
}

function crearTemporada() {
    global $conn;

   
    $Temporada_id = $conn->real_escape_string($_POST["Temporada_id"]);
    $Temporada_nombre = $conn->real_escape_string($_POST["Temporada_nombre"]);
    $LigaTemporada_idLiga = $conn->real_escape_string($_POST["LigaTemporada_idLiga"]);

 
    $sql_verificar = "SELECT Temporada_id FROM Temporada WHERE Temporada_id = '$Temporada_id'";
    $result = $conn->query($sql_verificar);

    $sql_verificar_liga = "SELECT Liga_id FROM Liga WHERE Liga_id = '$LigaTemporada_idLiga'";
    $result2 = $conn->query($sql_verificar_liga);

    if ($result2->num_rows === 0) {
        echo "Error: No existe esta liga.";
    } else if ($result->num_rows > 0) {
        echo "Error: Ya existe una temporada con este ID";
    } else {
    
        $sql_temporada = "INSERT INTO Temporada (Temporada_id, Temporada_nombre) VALUES ('$Temporada_id', '$Temporada_nombre')";
        if ($conn->query($sql_temporada) === TRUE) {
            $sql_liga_temporada = "INSERT INTO Liga_Temporada (LigaTemporada_idTemporada, LigaTemporada_idLiga) VALUES ('$Temporada_id', '$LigaTemporada_idLiga')";
            if ($conn->query($sql_liga_temporada) === TRUE) {
                echo "Temporada creada correctamente";
            } else {
                echo "Error al asociar la temporada con la liga: " . $conn->error;
            }
        } else {
            echo "Error al crear la temporada: " . $conn->error;
        }
    }
}

function eliminarTemporada() {
    global $conn;
    $Temporada_id = $_POST["Temporada_id"];
    $LigaTemporada_idLiga = $_POST["LigaTemporada_idLiga"];
    


    try {
       
        $sql = "DELETE FROM Liga_Temporada WHERE LigaTemporada_idTemporada = '$Temporada_id' AND LigaTemporada_idLiga = '$LigaTemporada_idLiga'"; //Quizas con un AND
        $conn->query($sql);
        $sql = "DELETE FROM Temporada WHERE Temporada_id = '$Temporada_id'";
        $conn->query($sql);

        $conn->commit();
        echo "Temporada eliminada";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error al eliminar la temporada: " . $e->getMessage();
    }
}

function modificarTemporada() {
    global $conn;
    $Temporada_id = $_POST["Temporada_id"];
    $Temporada_nombre_new = $_POST["Temporada_nombre_new"];


    $sql = "UPDATE Temporada SET Temporada_nombre = '$Temporada_nombre_new' WHERE Temporada_id = '$Temporada_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Temporada modificada correctamente";
    } else {
        echo "Error al modificar la temporada: " . $conn->error;
    }
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ligas, Temporadas y Comunas</title>
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
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            max-width: 800px;
        }
        form {
            margin: 10px 0;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5); 
            border-radius: 5px; 
            width: 100%;
            max-width: 500px;
        }
        form h3 {
            margin-top: 0;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"], button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function mostrarFormularios() {
            var opcion = document.getElementById("opcion").value;

            // Ocultar todos los formularios
            document.getElementById("formCrearComuna").style.display = "none";
            document.getElementById("formModificarComuna").style.display = "none";
            document.getElementById("formEliminarComuna").style.display = "none";
            document.getElementById("formCrearTemporada").style.display = "none";
            document.getElementById("formModificarTemporada").style.display = "none";
            document.getElementById("formEliminarTemporada").style.display = "none";
            document.getElementById("formCrearLiga").style.display = "none";
            document.getElementById("formModificarLiga").style.display = "none";
            document.getElementById("formEliminarLiga").style.display = "none";

            // Mostrar el formulario correspondiente
            if (opcion === "crearComuna") {
                document.getElementById("formCrearComuna").style.display = "block";
            } else if (opcion === "modificarComuna") {
                document.getElementById("formModificarComuna").style.display = "block";
            } else if (opcion === "eliminarComuna") {
                document.getElementById("formEliminarComuna").style.display = "block";
            } else if (opcion === "crearTemporada") {
                document.getElementById("formCrearTemporada").style.display = "block";
            } else if (opcion === "modificarTemporada") {
                document.getElementById("formModificarTemporada").style.display = "block";
            } else if (opcion === "eliminarTemporada") {
                document.getElementById("formEliminarTemporada").style.display = "block";
            } else if (opcion === "crearLiga") {
                document.getElementById("formCrearLiga").style.display = "block";
            } else if (opcion === "modificarLiga") {
                document.getElementById("formModificarLiga").style.display = "block";
            } else if (opcion === "eliminarLiga") {
                document.getElementById("formEliminarLiga").style.display = "block";
            }
        }
    </script>
</head>
<body>
    <h1>Gestión de Ligas, Temporadas y Comunas</h1>
    <form id="formSeleccion" action="" method="POST">
        <label for="opcion">Selecciona una opción:</label>
        <select name="opcion" id="opcion" onchange="mostrarFormularios()">
            <option value="">--Seleccionar--</option>
            <option value="crearComuna">Crear Comuna</option>
            <option value="modificarComuna">Modificar Comuna</option>
            <option value="eliminarComuna">Eliminar Comuna</option>
            <option value="crearTemporada">Crear Temporada</option>
            <option value="modificarTemporada">Modificar Temporada</option>
            <option value="eliminarTemporada">Eliminar Temporada</option>
            <option value="crearLiga">Crear Liga</option>
            <option value="modificarLiga">Modificar Liga</option>
            <option value="eliminarLiga">Eliminar Liga</option>
        </select>
    </form>

    <!-- Formulario para Crear Comuna -->
    <form id="formCrearComuna" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="crearComuna">
        <h3>Crear Comuna</h3>
        <label for="Comuna_id">ID de la Comuna:</label>
        <input type="text" name="Comuna_id" id="Comuna_id" required><br>
        <label for="Comuna_Nombre">Nombre de la Comuna:</label>
        <input type="text" name="Comuna_Nombre" id="Comuna_Nombre" required><br>
        <input type="submit" value="Crear Comuna">
    </form>

    <!-- Formulario para Modificar Comuna -->
    <form id="formModificarComuna" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="modificarComuna">
        <h3>Modificar Comuna</h3>
        <label for="Comuna_Nombre">Nombre actual de la Comuna:</label>
        <input type="text" name="Comuna_Nombre" id="Comuna_Nombre"><br>
        <label for="Comuna_nombre_new">Nombre nuevo para la Comuna:</label>
        <input type="text" name="Comuna_nombre_new" id="Comuna_nombre_new"><br>
        <input type="submit" value="Modificar Comuna">
    </form>

    <!-- Formulario para Eliminar Comuna -->
    <form id="formEliminarComuna" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="eliminarComuna">
        <h3>Eliminar Comuna</h3>
        <label for="Comuna_id">ID de la Comuna:</label>
        <input type="text" name="Comuna_id" id="Comuna_id" required><br>
        <input type="submit" value="Eliminar Comuna">
    </form>

    <!-- Formulario para Crear Temporada -->
    <form id="formCrearTemporada" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="crearTemporada">
        <h3>Crear Temporada</h3>
        <label for="Temporada_id">ID de la Temporada:</label>
        <input type="text" name="Temporada_id" id="Temporada_id" required><br>
        <label for="Temporada_nombre">Nombre de la Temporada:</label>
        <input type="text" name="Temporada_nombre" id="Temporada_nombre" required><br>
        <label for="LigaTemporada_idLiga">ID de la Liga:</label>
        <input type="text" name="LigaTemporada_idLiga" id="LigaTemporada_idLiga" required><br>
        <input type="submit" value="Crear Temporada">
    </form>

    <!-- Formulario para Modificar Temporada -->
    <form id="formModificarTemporada" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="modificarTemporada">
        <h3>Modificar Temporada</h3>
        <label for="Temporada_id">ID de la Temporada:</label>
        <input type="text" name="Temporada_id" id="Temporada_id" required><br>
        <label for="Temporada_nombre_new">Nuevo nombre para la Temporada:</label>
        <input type="text" name="Temporada_nombre_new" id="Temporada_nombre_new" required><br>
        <input type="submit" value="Modificar Temporada">
    </form>

    <!-- Formulario para Eliminar Temporada -->
    <form id="formEliminarTemporada" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="eliminarTemporada">
        <h3>Eliminar Temporada</h3>
        <label for="Temporada_id">ID de la Temporada:</label>
        <input type="text" name="Temporada_id" id="Temporada_id" required><br>
        <label for="LigaTemporada_idLiga">ID de la Liga:</label>
        <input type="text" name="LigaTemporada_idLiga" id="LigaTemporada_idLiga" required><br>
        <input type="submit" value="Eliminar Temporada">
    </form>

    <!-- Formulario para Crear Liga -->
    <form id="formCrearLiga" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="crearLiga">
        <h3>Crear Liga</h3>
        <label for="Liga_id">ID de la Liga:</label>
        <input type="text" name="Liga_id" id="Liga_id" required><br>
        <label for="Liga_nombre">Nombre de la Liga:</label>
        <input type="text" name="Liga_nombre" id="Liga_nombre" required><br>
        <label for="Liga_comuna">ID de la Comuna:</label>
        <input type="text" name="Liga_comuna" id="Liga_comuna" required><br>
        <input type="submit" value="Crear Liga">
    </form>

    <!-- Formulario para Modificar Liga -->
    <form id="formModificarLiga" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="modificarLiga">
        <h3>Modificar Liga</h3>
        <label for="Liga_comuna">ID de la Comuna:</label>
        <input type="text" name="Liga_comuna" id="Liga_comuna" required><br>
        <label for="Liga_nombre">Nombre actual de la Liga:</label>
        <input type="text" name="Liga_nombre" id="Liga_nombre" required><br>
        <label for="Liga_nombre_new">Nuevo nombre para la Liga:</label>
        <input type="text" name="Liga_nombre_new" id="Liga_nombre_new" required><br>
        <input type="submit" value="Modificar Liga">
    </form>

    <!-- Formulario para Eliminar Liga -->
    <form id="formEliminarLiga" action="Mantenimiento_Tablas.php" method="POST" style="display:none;">
        <input type="hidden" name="opcion" value="eliminarLiga">
        <h3>Eliminar Liga</h3>
        <label for="Liga_id">ID de la Liga:</label>
        <input type="text" name="Liga_id" id="Liga_id" required><br>
        <input type="submit" value="Eliminar Liga">
    </form>
<a href="Pagina_administrador.html">Volver</a>
</body>
</html>



