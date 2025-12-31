<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
	header("location: ../../");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Clientes</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<?php
	include('librerias.php');
	?>
	<script src="../controlador/funciones_lead_survey_questions.js"></script>
</head>

<body id="body">
	<?php
	include 'header.php';
	?>
	<div class="container">
		<div id="tabla"></div>
	</div>
	<!-- AGENTA AI -->
	<div class="modal fade" id="AImodal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #85a5ff;color: white;">
					<button type="button" class="close" data-dismiss="modal"><span>×</span></button>
					<h4 class="modal-title">Oscar</h4>
				</div>
				<div class="modal-body">
					<p>Esta vista es un panel administrativo de gestión de base de datos diseñado para organizar la lógica de la encuesta adaptativa mediante preguntas comunes (COMMON), preguntas específicas para perfiles no técnicos (RUTA_A) y preguntas para perfiles técnicos (RUTA_C).</p></div>
				<div class="modal-footer">
					
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
	<!-- MODAL PARA INSERTAR REGISTROS -->
	<div class="modal fade" id="modalNuevo" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #5cb85c; color: white;">
					<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
					<h4 class="modal-title">Nueva Pregunta con Opciones</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6">
							<label>Código</label>
							<input type="text" id="codigo_pregunta" class="form-control input-sm">
						</div>
					</div>
					<div class="form-group">
						<label>Texto de la Pregunta</label>
						<textarea id="question_text" rows="2" class="form-control input-sm"></textarea>
					</div>

					<hr>
					<div class="well well-sm" style="background-color: #f9f9f9;">
						<p><strong>Agregar Opciones a esta pregunta:</strong></p>
						<div class="row">
							<div class="col-xs-5">
								<input type="text" id="temp_label" class="form-control input-sm" placeholder="Etiqueta (Ej: Sí)">
							</div>
							<div class="col-xs-4">
								<input type="text" id="temp_value" class="form-control input-sm" placeholder="Valor (Ej: 1)">
							</div>
							<div class="col-xs-3">
								<button type="button" class="btn btn-info btn-sm btn-block" onclick="agregarOpcionALista()">
									<i class="glyphicon glyphicon-plus"></i> Añadir
								</button>
							</div>
						</div>
					</div>

					<table class="table table-condensed table-bordered" id="tablaOpcionesTemporales">
						<thead>
							<tr class="active">
								<th>Etiqueta</th>
								<th>Valor</th>
								<th width="50"></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="guardarTodo()">Guardar Pregunta y Opciones</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
	<!-- MODAL PARA EDICION DE DATOS-->
	<div class="modal fade" id="modalEdicion" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #f0ad4e; color: white;">
					<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
					<h4 class="modal-title">Actualizar Pregunta</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="id_questionu">
					<div class="form-group">
						<label>Código Pregunta</label>
						<input type="text" id="codigo_preguntau" class="form-control input-sm">
					</div>
					<div class="form-group">
						<label>Ruta (Route)</label>
						<input type="text" id="routeu" class="form-control input-sm">
					</div>
					<div class="form-group">
						<label>Texto de la Pregunta</label>
						<textarea id="question_textu" rows="3" class="form-control input-sm"></textarea>
					</div>
					<div class="form-group">
						<label>Tipo de Pregunta</label>
						<input type="text" id="question_typeu" class="form-control input-sm">
					</div>
					<div class="form-group">
						<label>Orden</label>
						<input type="number" id="ordenu" class="form-control input-sm">
					</div>
					<div class="form-group">
						<label>Visible</label>
						<select id="visibleu" class="form-control input-sm">
							<option value="1">Sí</option>
							<option value="0">No</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left" onclick="eliminarPreguntaCompleta()">
						<i class="glyphicon glyphicon-trash"></i>
					</button>

					<button type="button" class="btn btn-warning" id="actualizadatos">Actualizar</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalOpciones" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #5bc0de; color: white;">
					<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
					<h4 class="modal-title">Gestión de Pregunta</h4>
				</div>
				<div class="modal-body">
					<div class="panel panel-info">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<h3 class="panel-title" id="txt_pregunta_modal" style="font-weight: bold;"></h3>
								</div>
								<div class="col-md-3 text-right">
									<button class="btn btn-warning btn-xs" onclick="abrirEdicionPreguntaDesdeLupa()">
										<i class="glyphicon glyphicon-pencil"></i> Editar Pregunta
									</button>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<p id="info_pregunta_detalles" class="text-muted" style="margin:0;"></p>
						</div>
					</div>

					<div class="row" style="margin-bottom: 10px;">
						<div class="col-md-6">
							<h4>Opciones de respuesta</h4>
						</div>
						<div class="col-md-6 text-right">
							<button class="btn btn-success btn-sm" onclick="abrirModalAgregar()">
								<span class="glyphicon glyphicon-plus"></span> Nueva Opción
							</button>
						</div>
					</div>

					<table class="table table-striped table-bordered">
						<thead>
							<tr class="info">
								<th>ID</th>
								<th>Etiqueta</th>
								<th>Valor</th>
								<th>Siguiente</th>
								<th>Visible</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody id="cuerpoTablaOpciones"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAgregarOpcion" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #5cb85c; color: white;">
					<h4 class="modal-title">Nueva Opción</h4>
				</div>
				<div class="modal-body">
					<form id="formNuevaOpcion">
						<input type="hidden" id="add_id_question">
						<label>Etiqueta</label>
						<input type="text" id="add_option_label" class="form-control input-sm">
						<label>Valor</label>
						<input type="text" id="add_option_value" class="form-control input-sm">
						<label>Ruta Siguiente</label>
						<input type="text" id="add_next_route_map" class="form-control input-sm">
						<label>Visible</label>
						<select id="add_visible" class="form-control input-sm">
							<option value="1">Sí</option>
							<option value="0">No</option>
						</select>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="guardarNuevaOpcion()">Guardar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalEditarOpcion" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #f0ad4e; color: white;">
					<h4 class="modal-title">Editar Opción</h4>
				</div>
				<div class="modal-body">
					<form id="formOpcionEdit">
						<input type="hidden" id="edit_id_option">
						<input type="hidden" id="edit_id_question">
						<label>Etiqueta</label>
						<input type="text" id="edit_option_label" class="form-control input-sm">
						<label>Valor</label>
						<input type="text" id="edit_option_value" class="form-control input-sm">
						<label>Ruta Siguiente</label>
						<input type="text" id="edit_next_route_map" class="form-control input-sm">
						<label>Visible</label>
						<select id="edit_visible" class="form-control input-sm">
							<option value="1">Sí</option>
							<option value="0">No</option>
						</select>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" onclick="ejecutarAccionOpcion('modificar')">Actualizar</button>
					<button type="button" class="btn btn-danger" onclick="ejecutarAccionOpcion('borrar')">Eliminar</button>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#AImodal").modal('show');
			// Carga inicial de la tabla
			$('#tabla').load('componentes/vista_lead_survey_questions.php');

			// Listener para el botón "Agregar" del modal de preguntas
			$('#guardarnuevo').click(function() {
				agregardatos();
			});

			// Listener para el botón "Actualizar" del modal de preguntas
			$('#actualizadatos').click(function() {
				modificarPregunta();
			});
		});
	</script>
	<?php
	include './footer.php';
	?>
</body>

</html>