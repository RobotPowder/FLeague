<?php
include("conexion.php");

//Recoger datos del formulario
$user = $_POST['username'];
$pass = $_POST['password'];

//Verificar al administrador
$consulta = "SELECT Admin_id, Admin_contraseña FROM administrador WHERE Admin_nombre = ?";
$stmt = $conn->prepare($consulta);
$stmt->bind_param("s", $user);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows > 0){
    $row = $resultado->fetch_assoc();
    
    //Verificar la contraseña
    if(password_verify($pass, $row['Admin_contraseña'])){
        $id = $row['Admin_id'];

        //Verificar permisos
        $permisos = "SELECT AdminLiga_permiso FROM Admin_liga WHERE AdminLiga_idadmin = ?";
        $stmtP = $conn->prepare($permisos);
        $stmtP->bind_param("i", $id);
        $stmtP->execute();
        $resultado2 = $stmtP->get_result();

        //Si tiene permisos
        if($resultado2->num_rows > 0){
            $rowP = $resultado2->fetch_assoc();
            $hayP = $rowP['AdminLiga_permiso'] == 1;

            if($hayP){
                MostrarForm();
            }else{
                echo "No tienes permisos.";
                }

        }else{
            echo "No se encontraron permisos para este usuario.";
            }
    }else{
        echo "Contraseña incorrecta.";
        }
}else{
    echo "Nombre de usuario no encontrado.";
    }

// Cerrar la conexion
$conn->close();

function MostrarForm(){
    echo '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administracion de Equipos y Jugadores de la Liga</title>
</head>
    <body>
    <h1>Administrar Equipos y Jugadores de la Liga</h1>
    <h2>Modificar Nombre de Equipo</h2>
        <form action="modificar.php" method="post">
                <label for="equipo_id">ID del Equipo:</label>
            <input type="text" id="equipo_id" name="equipo_id" required><br><br>
                <label for="nuevo_nombre">Nuevo Nombre:</label>
            <input type="text" id="nuevo_nombre" name="nuevo_nombre" required><br><br>
            <input type="submit" name="modificar_equipo" value="Modificar Equipo">
        </form>
    <hr>
    <h2>Eliminar Jugador de un Equipo</h2>
        <form action="modificar.php" method="post">
                <label for="jugador_id">ID del Jugador:</label>
            <input type="text" id="jugador_id" name="jugador_id" required><br><br>
                <label for="equipo_id">ID del Equipo:</label>
            <input type="text" id="equipo_id" name="equipo_id" required><br><br>
            <input type="submit" name="borrar_jugador" value="Eliminar Jugador del Equipo">
        </form>
    </body>
</html>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Permisos</title>
</head>
    <body>
    <form action="login.php" method="post">
        <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" required><br>
        <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="Iniciar Sesión">
        </form>
    </body>
</html>