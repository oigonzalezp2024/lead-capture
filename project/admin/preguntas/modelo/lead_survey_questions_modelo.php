<?php

session_start();

include 'conexion.php';
$conn = conexion();

if (!isset($_SESSION['admin_id'])) {
    exit;
}
$id_user = $_SESSION['admin_id'];

$accion = $_GET['accion'];

if($accion == "insertar"){

    $codigo_pregunta = $_POST['codigo_pregunta'];
    $route = $_POST['route'];
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $orden = $_POST['orden'];
    $visible = $_POST['visible'];

    $sql="INSERT INTO lead_survey_questions(
          id_user, codigo_pregunta, route, question_text, question_type, orden, visible
          )VALUE(
          '$id_user', '$codigo_pregunta', '$route', '$question_text', '$question_type', '$orden', '$visible')";

    echo $consulta = mysqli_query($conn, $sql);
}

elseif($accion == "modificar"){

    $id_question = $_POST['id_question'];
    $codigo_pregunta = $_POST['codigo_pregunta'];
    $route = $_POST['route'];
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $orden = $_POST['orden'];
    $visible = $_POST['visible'];

    $sql="UPDATE lead_survey_questions SET
          id_user = '$id_user',
          codigo_pregunta = '$codigo_pregunta', 
          route = '$route', 
          question_text = '$question_text', 
          question_type = '$question_type', 
          orden = '$orden', 
          visible = '$visible'
          WHERE id_question = '$id_question'";

    echo $consulta = mysqli_query($conn, $sql);
}

elseif($accion == "borrar"){

    $id_question = $_POST['id_question'];

    $sql = "DELETE FROM lead_survey_questions
            WHERE id_question = '$id_question'
            AND id_user = '$id_user'";

    echo $consulta = mysqli_query($conn, $sql);
}

if($accion == "insertar_con_opciones"){
    $data = json_decode($_POST['data'], true);
    
    $cod = $data['codigo_pregunta'];
    $txt = $data['question_text'];
    
    // 1. Insertar Pregunta
    $sqlPregunta = "INSERT INTO lead_survey_questions (id_user, codigo_pregunta, question_text) 
                    VALUES ('$id_user', '$cod', '$txt')";
    $res = mysqli_query($conn, $sqlPregunta);
    $id = mysqli_insert_id($conn);
    
    if($res){
        // 2. Insertar Opciones
        foreach($data['opciones'] as $opt){
            $label = $opt['label'];
            $val = $opt['value'];
            $sqlOpt = "INSERT INTO lead_question_options (id_question, id_user, option_label, option_value, visible) 
                       VALUES ('$id', '$id_user', '$label', '$val', 1)";
            mysqli_query($conn, $sqlOpt);
        }
        echo 1;
    } else {
        echo "Error al insertar pregunta";
    }
}

elseif($accion == "borrar_recursivo"){

    $id_question = $_POST['id_question'];

    // Iniciar transacción para seguridad
    mysqli_begin_transaction($conn);

    try {
        // 1. Eliminar todas las opciones asociadas a esta pregunta
        $sqlOpciones = "DELETE FROM lead_question_options WHERE id_question = '$id_question' AND id_user = '$id_user'";
        mysqli_query($conn, $sqlOpciones);

        // 2. Eliminar la pregunta
        $sqlPregunta = "DELETE FROM lead_survey_questions WHERE id_question = '$id_question' AND id_user = '$id_user'";
        mysqli_query($conn, $sqlPregunta);

        // Si todo salió bien, confirmar cambios
        mysqli_commit($conn);
        echo 1;

    } catch (Exception $e) {
        // Si algo falla, deshacer todo
        mysqli_rollback($conn);
        echo 0;
    }
}
