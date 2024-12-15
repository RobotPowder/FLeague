<?php
include("conexion.php");

if(isset($_POST['modificar_equipo'])){
    $equipo_id = $_POST['equipo_id'];
    $nuevo_nombre = $_POST['nuevo_nombre'];

    //Modificar el nombre del equipo
    $modificar = "UPDATE equipo SET Equipo_nombre = ? WHERE Equipo_id = ?";
    $stmt = $conn->prepare($modificar);
    $stmt->bind_param("si", $nuevo_nombre, $equipo_id);
    if($stmt->execute()){
        echo "Nombre del equipo modificado correctamente.";
    }else{
        echo "Error al modificar el nombre del equipo: " .$stmt->error;
    }
}else if(isset($_POST['borrar_jugador'])){
    $jugador_id = $_POST['jugador_id'];
    $equipo_id = $_POST['equipo_id']; //ID del equipo

    //Verificar en que equipos esta el jugador
    $consulta = "SELECT EquipoJugador_idEquipo FROM equipo_jugador WHERE EquipoJugador_idJugador = ?";
    $stmt = $conn->prepare($consulta);
    $stmt->bind_param("i", $jugador_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){
        //Mostrar en todos los equipo que esta el jugador
        echo "El jugador pertenece a los siguientes equipos:<br>";
        while($row = $resultado->fetch_assoc()){
            echo "Equipo ID: " . $row['EquipoJugador_idEquipo'] . "<br>";
        }

        //Verificar si el jugador pertenece al equipo el cual se quiere eliminar
        $consulta_especifica = "SELECT * FROM equipo_jugador WHERE EquipoJugador_idJugador = ? AND EquipoJugador_idEquipo = ?";
        $stmt_especifica = $conn->prepare($consulta_especifica);
        $stmt_especifica->bind_param("is", $jugador_id, $equipo_id);
        $stmt_especifica->execute();
        $resultado_especifico = $stmt_especifica->get_result();

        if($resultado_especifico->num_rows > 0){
            //Eliminar al jugador del equipo especifico
            $eliminar = "DELETE FROM equipo_jugador WHERE EquipoJugador_idJugador = ? AND EquipoJugador_idEquipo = ?";
            $stmt_eliminar = $conn->prepare($eliminar);
            $stmt_eliminar->bind_param("is", $jugador_id, $equipo_id);
            if($stmt_eliminar->execute()){
                echo "Jugador eliminado del equipo con ID: $equipo_id correctamente.";
            }else{
                echo "Error al eliminar el jugador del equipo: " .$stmt_eliminar->error;
            }
        }else{
            echo "El jugador no pertenece al equipo con ID: $equipo_id.";
        }
    }else{
        echo "El jugador no pertenece a ningun equipo.";
    }
}else{
    echo "No se eligio ninguna accion.";
}

//Cerrar la conexion
$conn->close();
?>