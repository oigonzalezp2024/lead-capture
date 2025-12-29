let idPreguntaParaNuevaOpcion = 0;

function detalleform(base64Data) {
    try {
        const jsonString = decodeURIComponent(escape(atob(base64Data)));
        const data = JSON.parse(jsonString);
        
        const tablaBody = document.getElementById('cuerpoTablaOpciones');
        const tituloModal = document.getElementById('txt_pregunta_modal'); 
        
        tablaBody.innerHTML = "";

        if (data.length > 0) {
            idPreguntaParaNuevaOpcion = data[0].lsq_id_question;
            if(tituloModal) tituloModal.innerText = "Opciones de: " + data[0].lsq_question_text;

            data.forEach(item => {
                let optionHash = btoa(unescape(encodeURIComponent(JSON.stringify(item))));
                let fila = `
                    <tr>
                        <td>${item.lqo_id_option}</td>
                        <td>${item.lqo_option_label}</td>
                        <td>${item.lqo_option_value}</td>
                        <td>${item.lqo_next_route_map}</td>
                        <td class="text-center">${item.lqo_visible == 1 ? 'Sí' : 'No'}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-xs" onclick="prepararEdicion('${optionHash}')">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </button>
                        </td>
                    </tr>`;
                tablaBody.innerHTML += fila;
            });
        }
    } catch (e) { console.error("Error en detalleform", e); }
}

// LÓGICA PARA AGREGAR
function abrirModalAgregar() {
    document.getElementById('formNuevaOpcion').reset();
    document.getElementById('add_id_question').value = idPreguntaParaNuevaOpcion;
    $('#modalAgregarOpcion').modal('show');
}

function guardarNuevaOpcion() {
    const datos = {
        id_option: 0,
        id_question: $('#add_id_question').val(),
        option_label: $('#add_option_label').val(),
        option_value: $('#add_option_value').val(),
        next_route_map: $('#add_next_route_map').val(),
        visible: $('#add_visible').val()
    };
    
    $.ajax({
        type: "POST",
        url: "../modelo/lead_question_options_modelo.php?accion=insertar",
        data: datos,
        success: function(r) {
            if(r == 1) { alert("Agregado"); location.reload(); }
            else { alert("Error"); }
        }
    });
}

// LÓGICA PARA EDITAR/BORRAR
function prepararEdicion(optionHash) {
    const opt = JSON.parse(decodeURIComponent(escape(atob(optionHash))));
    $('#edit_id_option').val(opt.lqo_id_option);
    $('#edit_id_question').val(opt.lqo_id_question);
    $('#edit_option_label').val(opt.lqo_option_label);
    $('#edit_option_value').val(opt.lqo_option_value);
    $('#edit_next_route_map').val(opt.lqo_next_route_map);
    $('#edit_visible').val(opt.lqo_visible);
    $('#modalEditarOpcion').modal('show');
}

function ejecutarAccionOpcion(accion) {
    if(accion === 'borrar' && !confirm('¿Eliminar?')) return;

    const datos = {
        id_option: $('#edit_id_option').val(),
        id_question: $('#edit_id_question').val(),
        option_label: $('#edit_option_label').val(),
        option_value: $('#edit_option_value').val(),
        next_route_map: $('#edit_next_route_map').val(),
        visible: $('#edit_visible').val()
    };

    $.ajax({
        type: "POST",
        url: "../modelo/lead_question_options_modelo.php?accion=" + accion,
        data: datos,
        success: function(r) {
            if (r == 1) { alert("Éxito"); location.reload(); }
            else { alert("Error servidor"); }
        }
    });
}

// Función para capturar datos del modal de INSERCIÓN de preguntas
function agregardatos() {
    let datos = {
        id_question: $('#id_question').val(),
        codigo_pregunta: $('#codigo_pregunta').val(),
        route: $('#route').val(),
        question_text: $('#question_text').val(),
        question_type: $('#question_type').val(),
        orden: $('#orden').val(),
        visible: $('#visible').val()
    };

    if(datos.id_question == "" || datos.question_text == "") {
        alert("ID y Texto de pregunta son obligatorios");
        return;
    }

    ejecutarAjaxPregunta(datos, "insertar", "Pregunta agregada con éxito");
}

// Función para capturar datos del modal de EDICIÓN de preguntas
function modificarPregunta() {
    let datos = {
        id_question: $('#id_questionu').val(),
        codigo_pregunta: $('#codigo_preguntau').val(),
        route: $('#routeu').val(),
        question_text: $('#question_textu').val(),
        question_type: $('#question_typeu').val(),
        orden: $('#ordenu').val(),
        visible: $('#visibleu').val()
    };

    ejecutarAjaxPregunta(datos, "modificar", "Pregunta actualizada con éxito");
}

// Función para eliminar pregunta
function eliminarPregunta(id_question) {
    if (!confirm("¿Está seguro de eliminar esta pregunta? Se perderán las opciones asociadas.")) return;

    let datos = { id_question: id_question };
    ejecutarAjaxPregunta(datos, "borrar", "Pregunta eliminada correctamente");
}

/**
 * Función AJAX Unificada para Preguntas
 * Apunta a: modelo/lead_survey_questions_modelo.php
 */
function ejecutarAjaxPregunta(datos, accion, mensajeExito) {
    $.ajax({
        type: "POST",
        url: "../modelo/lead_survey_questions_modelo.php?accion=" + accion,
        data: datos,
        success: function(r) {
            if (r == 1) {
                $('#tabla').load('componentes/vista_lead_survey_questions.php');
                // Si estamos en un modal, lo cerramos
                $('.modal').modal('hide');
                alert(mensajeExito);
            } else {
                alert("Error en la operación: " + r);
            }
        }
    });
}

let listaOpcionesTemporales = [];

function agregarOpcionALista() {
    const label = $('#temp_label').val();
    const value = $('#temp_value').val();

    if (label === "" || value === "") {
        alert("Completa la etiqueta y el valor de la opción");
        return;
    }

    // Agregar al array
    listaOpcionesTemporales.push({
        label: label,
        value: value
    });

    // Limpiar campos temporales
    $('#temp_label').val("");
    $('#temp_value').val("");

    renderizarTablaTemporal();
}

function renderizarTablaTemporal() {
    let html = "";
    listaOpcionesTemporales.forEach((opt, index) => {
        html += `<tr>
                    <td>${opt.label}</td>
                    <td>${opt.value}</td>
                    <td><button class="btn btn-danger btn-xs" onclick="eliminarOpcionTemporal(${index})"><i class="glyphicon glyphicon-trash"></i></button></td>
                 </tr>`;
    });
    $('#tablaOpcionesTemporales tbody').html(html);
}

function eliminarOpcionTemporal(index) {
    listaOpcionesTemporales.splice(index, 1);
    renderizarTablaTemporal();
}

function guardarTodo() {
    const pregunta = {
        id_question: $('#id_question').val(),
        codigo_pregunta: $('#codigo_pregunta').val(),
        question_text: $('#question_text').val(),
        // ... otros campos de la pregunta ...
        opciones: listaOpcionesTemporales // Enviamos el array aquí
    };

    if (pregunta.id_question === "" || pregunta.opciones.length === 0) {
        alert("La pregunta debe tener al menos una opción");
        return;
    }

    $.ajax({
        type: "POST",
        url: "../modelo/lead_survey_questions_modelo.php?accion=insertar_con_opciones",
        data: { data: JSON.stringify(pregunta) }, // Enviamos todo como JSON string
        success: function(r) {
            if (r == 1) {
                alert("Pregunta y opciones guardadas correctamente");
                location.reload();
            } else {
                alert("Error al guardar: " + r);
            }
        }
    });
}

// Variables para persistir los datos de la pregunta actual abierta en la lupa
let datosPreguntaActual = null;

function detalleform(base64Data) {
    try {
        const jsonString = decodeURIComponent(escape(atob(base64Data)));
        const data = JSON.parse(jsonString);
        
        const tablaBody = document.getElementById('cuerpoTablaOpciones');
        const tituloModal = document.getElementById('txt_pregunta_modal'); 
        const infoPregunta = document.getElementById('info_pregunta_detalles');
        
        tablaBody.innerHTML = "";

        if (data.length > 0) {
            // Guardamos la info de la pregunta (tomada del primer registro del join)
            datosPreguntaActual = {
                id: data[0].lsq_id_question,
                codigo: data[0].lsq_codigo_pregunta,
                route: data[0].lsq_route,
                texto: data[0].lsq_question_text,
                tipo: data[0].lsq_question_type,
                orden: data[0].lsq_orden,
                visible: data[0].lsq_visible
            };
            
            // Llenar cabecera del modal
            tituloModal.innerText = datosPreguntaActual.texto;
            infoPregunta.innerHTML = `<strong>Código:</strong> ${datosPreguntaActual.codigo} | <strong>Tipo:</strong> ${datosPreguntaActual.tipo} | <strong>Orden:</strong> ${datosPreguntaActual.orden}`;
            idPreguntaParaNuevaOpcion = datosPreguntaActual.id;

            // Llenar tabla de opciones
            data.forEach(item => {
                let optionHash = btoa(unescape(encodeURIComponent(JSON.stringify(item))));
                let fila = `
                    <tr>
                        <td>${item.lqo_id_option}</td>
                        <td>${item.lqo_option_label}</td>
                        <td>${item.lqo_option_value}</td>
                        <td>${item.lqo_next_route_map}</td>
                        <td class="text-center">${item.lqo_visible == 1 ? 'Sí' : 'No'}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-xs" onclick="prepararEdicion('${optionHash}')">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </button>
                        </td>
                    </tr>`;
                tablaBody.innerHTML += fila;
            });
        }
    } catch (e) { console.error(e); }
}

// Esta función se dispara desde el botón naranja DENTRO del modal de la lupa
function abrirEdicionPreguntaDesdeLupa() {
    if(!datosPreguntaActual) return;

    // Llenar el modal de edición de pregunta (modalEdicion)
    $('#id_questionu').val(datosPreguntaActual.id);
    $('#codigo_preguntau').val(datosPreguntaActual.codigo);
    $('#routeu').val(datosPreguntaActual.route);
    $('#question_textu').val(datosPreguntaActual.texto);
    $('#question_typeu').val(datosPreguntaActual.tipo);
    $('#ordenu').val(datosPreguntaActual.orden);
    $('#visibleu').val(datosPreguntaActual.visible);

    // Opcional: Cerrar el modal de la lupa o dejarlo atrás
    // $('#modalOpciones').modal('hide'); 
    $('#modalEdicion').modal('show');
}

function abrirEdicionPreguntaDesdeLupa() {
    if(!datosPreguntaActual) return;

    // 1. Llenar los campos del modal de edición
    $('#id_questionu').val(datosPreguntaActual.id);
    $('#codigo_preguntau').val(datosPreguntaActual.codigo);
    $('#routeu').val(datosPreguntaActual.route);
    $('#question_textu').val(datosPreguntaActual.texto);
    $('#question_typeu').val(datosPreguntaActual.tipo);
    $('#ordenu').val(datosPreguntaActual.orden);
    $('#visibleu').val(datosPreguntaActual.visible);

    // 2. OCULTAR el modal de la lupa y MOSTRAR el de edición
    $('#modalOpciones').modal('hide');
    
    // Esperamos un momento a que el primer modal se cierre para no romper el scroll del body
    setTimeout(function(){
        $('#modalEdicion').modal('show');
    }, 400);
}

function eliminarPreguntaCompleta() {
    let id = $('#id_questionu').val();
    let codigo = $('#codigo_preguntau').val();

    if (!id) return;

    // Advertencia clara sobre la recursividad
    let confirmacion = confirm(`¿ESTÁ SEGURO?\n\nEsta acción eliminará la pregunta "${codigo}" y TODAS sus opciones de respuesta de forma permanente.`);

    if (confirmacion) {
        $.ajax({
            type: "POST",
            url: "../modelo/lead_survey_questions_modelo.php?accion=borrar_recursivo",
            data: { id_question: id },
            success: function(r) {
                if (r == 1) {
                    alert("Pregunta y sus opciones eliminadas correctamente.");
                    location.reload(); // Recargar para limpiar la tabla principal
                } else {
                    alert("Error en el servidor al intentar eliminar.");
                }
            }
        });
    }
}