<?php
include_once '../../modelo/conexion.php';
$conn = conexion();

function datos($conn): array
{
    $sql = 'SELECT 
        lsq.id_question lsq_id_question,
        lsq.codigo_pregunta lsq_codigo_pregunta,
        lsq.route lsq_route,
        lsq.question_text lsq_question_text,
        lsq.question_type lsq_question_type,
        lsq.orden lsq_orden,
        lsq.visible lsq_visible,
        lqo.id_option lqo_id_option,
        lqo.id_question lqo_id_question,
        lqo.option_label lqo_option_label,
        lqo.option_value lqo_option_value,
        lqo.next_route_map lqo_next_route_map,
        lqo.visible lqo_visible
        FROM `lead_question_options` lqo, lead_survey_questions lsq
        WHERE lsq.id_question = lqo.id_question
        ORDER BY lsq.id_question';
    $result = mysqli_query($conn, $sql);
    $datos = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        array_push($datos, $fila);
    }
    return $datos;
}

function transform($datos): array
{
    $data = [];
    foreach ($datos as $registro) {
        $id = $registro['lsq_id_question'];
        if (!isset($data[$id])) {
            $data[$id] = [];
        }
        $data[$id][] = $registro;
    }
    return $data; 
}

// 1. Obtenemos los datos una sola vez
$datos_brutos = datos($conn);
// 2. Transformamos una sola vez ANTES del bucle HTML
$datos_agrupados = transform($datos_brutos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Arreglos</title>
    </head>
<body>
<div class="container">
    <div class="row"><br><br>
        <div class="text-center">
            <h2>lead_survey_questions</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalNuevo">
                Agregar lead_survey_questions <span class="glyphicon glyphicon-plus"></span>
            </button>
        </div>
        <br>
        <table class="table table-hover table-bordered table-responsive">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Ruta</th>
                    <th>Texto</th>
                    <th>Tipo</th>
                    <th>Orden</th>
                    <th>Visible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Usamos el array agrupado para evitar duplicados visuales en la tabla de preguntas
                foreach ($datos_agrupados as $id_pregunta => $opciones) { 
                    // Tomamos la info de la pregunta del primer registro del grupo
                    $pregunta = $opciones[0];
                    
                    // Preparamos los strings para los onclick
                    $string_pregunta = $pregunta['lsq_id_question'] . "||" .
                                     $pregunta['lsq_codigo_pregunta'] . "||" .
                                     $pregunta['lsq_route'] . "||" .
                                     $pregunta['lsq_question_text'] . "||" .
                                     $pregunta['lsq_question_type'] . "||" .
                                     $pregunta['lsq_orden'] . "||" .
                                     $pregunta['lsq_visible'];
                    
                    // El JSON de opciones para el detalle
                    $datos_safe = base64_encode(json_encode($opciones));
                ?>
                    <tr>
                        <td><?php echo $pregunta['lsq_id_question']; ?></td>
                        <td><?php echo $pregunta['lsq_codigo_pregunta']; ?></td>
                        <td><?php echo $pregunta['lsq_route']; ?></td>
                        <td><?php echo $pregunta['lsq_question_text']; ?></td>
                        <td><?php echo $pregunta['lsq_question_type']; ?></td>
                        <td><?php echo $pregunta['lsq_orden']; ?></td>
                        <td><?php echo $pregunta['lsq_visible']; ?></td>
                        <td>
                            <button class="btn btn-info glyphicon glyphicon-search"
                                data-toggle="modal" 
                                data-target="#modalOpciones"
                                onclick="detalleform('<?php echo $datos_safe; ?>')">
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>