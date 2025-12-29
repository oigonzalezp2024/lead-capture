<?php
include 'conexion.php';
$conn = conexion();

$accion = $_GET['accion'];

if($accion == "insertar"){

    $id_option = $_POST['id_option'];
    $id_question = $_POST['id_question'];
    $option_label = $_POST['option_label'];
    $option_value = $_POST['option_value'];
    $next_route_map = $_POST['next_route_map'];
    $visible = $_POST['visible'];

    $sql="INSERT INTO lead_question_options(
          id_option, id_question, option_label, option_value, next_route_map, visible
          )VALUE(
          '$id_option', '$id_question', '$option_label', '$option_value', '$next_route_map', '$visible')";

    echo $consulta = mysqli_query($conn, $sql);
}

elseif($accion == "modificar"){

    $id_option = $_POST['id_option'];
    $id_question = $_POST['id_question'];
    $option_label = $_POST['option_label'];
    $option_value = $_POST['option_value'];
    $next_route_map = $_POST['next_route_map'];
    $visible = $_POST['visible'];

    $sql="UPDATE lead_question_options SET
          id_question = '$id_question', 
          option_label = '$option_label', 
          option_value = '$option_value', 
          next_route_map = '$next_route_map', 
          visible = '$visible'
          WHERE id_option = '$id_option'";

    echo $consulta = mysqli_query($conn, $sql);
}

elseif($accion == "borrar"){

    $id_option = $_POST['id_option'];

    $sql = "DELETE FROM lead_question_options
            WHERE id_option = '$id_option'";

    echo $consulta = mysqli_query($conn, $sql);
}


?>