
var fecha = new Date();
var hoy = fecha.getDate() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getFullYear() + "-" + fecha.getHours() + ":" + fecha.getMinutes() + ":" + fecha.getSeconds() + ":" + fecha.getMilliseconds();

function PasaDatosCodEmpresa() {
    var fila = $("#table_visible_BuscarEmpresa input[name=radiob]:checked").closest('tr');
    $('#tb_codEmpresa').val($("#table_visible_BuscarEmpresa")[0].rows[fila[0].rowIndex].cells[1].innerText);
}

function ModalUsuariosdeEmpresa() {
    $('#table_visible_BuscarEmpresa').DataTable().destroy();
    $('#table_secundariaBuscarEmpresa').DataTable().destroy();
   $.ajax({
        type: "POST",
        url: 'ConsultaUsuarios.php?action=ConsultaUsuariosPorEmpresa',
        data: '{empresa: "' + "" + '"}',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (response) {
            var obj = response.d; 
            $('#table_visible_BuscarEmpresa').DataTable({
                data: obj,
                columns: [
         { data: 'cbx',
             render: function (data, type, row) {
                 if (type === 'display') { return '<input type="radio" name="radiob">'; }
                 return data;
             },
             className: "dt-body-center"
         },
                { data: 'ccod_empresa' },
                { data: 'cdescripcion' },
                 { data: 'countUsuarios' }
            ]
            });
            $('#table_secundariaBuscarEmpresa').DataTable({
                "autoWidth": false,
                // "lengthMenu": [100],
                "paging": false,
                "ordering": false,
                "info": false,
                "searching": false,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ entradas",
                    "zeroRecords": "No se encontraron resultados.",
                    "info": "Total de registros : <b>_MAX_</b>",
                    "infoEmpty": "",
                    "infoFiltered": "",
                    "search": "",
                    "searchPlaceholder": " ",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                data: obj,
                columns: [
                    { data: 'ccod_empresa' },
                { data: 'cdescripcion' },
                 { data: 'countUsuarios' }],
                 scrollX: "2000px",
                scrollCollapse: true,
            });
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });
}


function Ejecutar() {
    $('#table_principal').DataTable().destroy();
    $('#table_visible').DataTable().destroy();
    var tb_codEmpresa = document.getElementById("tb_codEmpresa");
    var txtStatus = document.getElementById("txtStatus");
    $.ajax({
        type: "POST",
        url: 'ConsultaUsuarios.php?action=ConsultasUsuariosPrincipal',
        data: '{codigo: "' + tb_codEmpresa.value + '",estado: "' + txtStatus.value + '"  }',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (response) {
            var obj = response.d;
            $('#hdd_numerofilas').val(obj.length);
            $('#table_visible').DataTable({
                data: obj,
                columns: [

                 { data: 'ccod_empresa' },
                    { data: 'cdsc_empresa' },
                    { data: 'ccod_usuario' },
                    { data: 'cdsc_usuario' },
                    { data: 'cdir_usuario' },
                    { data: 'cdsc_rol' },
                    { data: 'cpais_origen' },
                    { data: 'ccelular' },
                    { data: 'cstatus'}]
            });

            $('#table_principal').DataTable({
                "autoWidth": false,
                // "lengthMenu": [100],
                "paging": false,
                "ordering": false,
                "info": false,
                "searching": false,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ entradas",
                    "zeroRecords": "No se encontraron resultados.",
                    "info": "Total de registros : <b>_MAX_</b>",
                    "infoEmpty": "",
                    "infoFiltered": "",
                    "search": "",
                    "searchPlaceholder": " ",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                data: obj,
                columns: [
                { data: 'ccod_empresa' },
                    { data: 'cdsc_empresa' },
                    { data: 'ccod_usuario' },
                    { data: 'cdsc_usuario' },
                    { data: 'cdir_usuario' },
                    { data: 'cdsc_rol' },
                    { data: 'cpais_origen' },
                    { data: 'ccelular' },
                    { data: 'cstatus'}],
                    scrollX: "2000px",
                scrollCollapse: true,
            });
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });

}

function Limpiar() {
    $('#tb_codEmpresa').val('');
    $('#txtStatus').val('');
    $('#table_visible').DataTable().destroy();
    $('#table_principal').DataTable().destroy();
    var table = $('#table_visible').DataTable();
    table.clear().draw();
    document.getElementById("txtStatus").setAttribute("value", "");
}

$(document).ready(function () {

document.getElementById("txtStatus").setAttribute("value", "");
    $('#id_titulo').text("Consulta de Usuarios");
    $('#btn_p_nuevo').hide();
    $('#btn_p_editar').hide();
    $('#btn_p_grabar').hide();
    $('#btn_p_eliminar').hide();
    $('#btn_p_back').hide();
    $('#btn_p_imprimir').hide(); 
    document.getElementById("divColsulta").style.visibility = "visible";
    $('#btn_p_ejecutar').removeClass("botones_des").addClass("botones_hab"); 
    $('#btn_p_limpiar').removeClass("botones_des").addClass("botones_hab");

    traducir_tabla();
    $('#table_visible').DataTable({
        "zeroRecords": "No se encontraron resultados."
    });

    $("#thTablaVisible").contextMenu({
        menuSelector: "#contextMenu",
        menuSelected: function (invokedOn, selectedMenu) {
            //click para exporta a exel
           
            var blob = new Blob([document.getElementById('tableExport').innerHTML], {
                type: 'application/xml;charset=utf-8', encoding: 'utf-8'
            });
            saveAs(blob, "Reporte del " + hoy + ".xls");
            
        }
    });

    $("#thTablaCantidadUsuario").contextMenu({
        menuSelector: "#contextMenu",
        menuSelected: function (invokedOn, selectedMenu) {
            //click para exporta a exeltableExportarBuscarEmpresa
            var blob = new Blob([document.getElementById('tableExportarBuscarEmpresa').innerHTML], {
                type: 'application/xml;charset=utf-8', encoding: 'utf-8'
            });
            saveAs(blob, "Reporte del " + hoy + ".xls");
        }
    });



});
