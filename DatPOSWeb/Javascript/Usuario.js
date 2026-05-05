var fecha = new Date();
var hoy = fecha.getDate() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getFullYear() + "-" + fecha.getHours() + ":" + fecha.getMinutes() + ":" + fecha.getSeconds() + ":" + fecha.getMilliseconds();

function mostrarContrasena() {
    //         Mensaje('Advertencia', 'Ingrese Estado de Usuario', 'warning');

    var tipo = document.getElementById("tb_cpassw");

    if (tipo.type == "password") {

        $("#span").hide();
        $("#span2").show();
        tipo.type = "text";
    } else {
        $("#span2").hide();
        $("#span").show();
        tipo.type = "password";
    }
}


function UsuariosAsociados() {

    if ($('#hdd_ultimafila2').val() != '') {

        $('#tableUsuariosAsociados').DataTable().destroy();
        $('#table_secundariaUsuariosAsociados').DataTable().destroy();

        $.ajax({
            type: "POST",
            url: 'AdministrarUsuarios.aspx/UsuariosAsociados',
            data: '{ccod_empresa: "' + $('#hdd_ultimafila2').val() + '" }',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: false,

            success: function (response) {
                var obj = response.d;

                $('#hdd_numerofilas').val(obj.length);
                $('#table_secundariaUsuariosAsociados').DataTable({
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
                        { data: 'ccod_usuario' },
                        { data: 'cdsc_usuario' },
                        { data: 'cdirec' },
                        { data: 'cdsc_rol' },
                        { data: 'ccelular' },
                        { data: 'cmail' },
                        { data: 'cstatus' }],
                    scrollX: "2000px",
                    scrollCollapse: true,
                });
                $('#tableUsuariosAsociados').DataTable({
                    data: obj,
                    columns: [

                        { data: 'ccod_usuario' },
                        { data: 'cdsc_usuario' },
                        { data: 'cdirec' },
                        { data: 'cdsc_rol' },
                        { data: 'ccelular' },
                        { data: 'cmail' },
                        { data: 'cstatus' }
                    ]
                });


            },
            error: function (xhr, status, error) {
                alert(error);
            }
        });
    }
    Desabilitar();
}





function CargarTablaUsuario() {
    $('#table_principal').DataTable().destroy();
    $('#table_id').DataTable().destroy();

    var obj = llenarobjeto('AdministrarUsuarios.aspx/ConsultarUsuarios');
    $('#hdd_numerofilas').val(obj.length);
    $('#table_id').DataTable({
        data: obj,
        columns: [
            { data: 'ccod_usuario' },
            { data: 'cdsc_usuario' },
            { data: 'cdirec' },
            { data: 'id_rol' },
            { data: 'ccod_empresa' },
            { data: 'dfch_crea' },
            { data: 'cstatus' }
        ]
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
            { data: 'ccod_usuario' },
            { data: 'cdsc_usuario' },
            { data: 'cdirec' },
            { data: 'id_rol' },
            { data: 'ccod_empresa' },
            { data: 'dfch_crea' },
            { data: 'cstatus' }],
        scrollX: "2000px",
        scrollCollapse: true,
    });

    $('#table_id').attr("style", "width: -webkit-fill-available;");
}


function PasarCodEmpresa() {
    var fila = $("#table_Empresa input[name=radiob]:checked").closest('tr');

    $('#dl_empresa').val($("#table_Empresa")[0].rows[fila[0].rowIndex].cells[1].innerText);
    for (var i = 0; i < obje.length; i++) {
        if ($("#table_Empresa")[0].rows[fila[0].rowIndex].cells[1].innerText == obje[i].ccod_empresa) {
            $('#tb_nomEmpresa').val(obje[i].cdescripcion);
            $('#ipServidor').val(obje[i].cnombre_servidor);
            $('#nomServidor').val(obje[i].cnombre_bd);
        }
    }

}

var obje = [];

function ModalEmpresa() {

    $('#table_Empresa').DataTable().destroy();
    $('#table_secundariaBuscarEmpresa').DataTable().destroy();

    obje = llenarobjeto('AdministrarUsuarios.aspx/TablaEmpresas');

    $('#table_Empresa').DataTable({
        data: obje,
        "pageLength": 5,
        columns: [
            {
                data: 'cbx',
                render: function (data, type, row) {
                    if (type === 'display') { return '<input type="radio" name="radiob">'; }
                    return data;
                },
                className: "dt-body-center"
            },
            { data: 'ccod_empresa' },
            { data: 'cdescripcion' }
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
        data: obje,
        columns: [
            { data: 'ccod_empresa' },
            { data: 'cdescripcion' }],
        scrollX: "2000px",
        scrollCollapse: true,
    });

}

function Nuevo() {
    $('#tb_ccod_usuario').focus();
    $('.nav-tabs li:eq(0) a').tab('show');

    $(".readonl").prop("readonly", false);
    $(".disabled").prop("disabled", false);
    $(".limpiar").val("");
    $("#operacion").val("nuevo");
    $('.fa_disabled').removeClass("fa_disabled").addClass("fa_enabled");

    $('#btn_p_grabar').removeClass("botones_des").addClass("botones_hab");
    $('#btn_p_back').removeClass("botones_des").addClass("botones_hab");

    $('#btn_p_editar').removeClass("botones_hab").addClass("botones_des");
    $('#btn_p_eliminar').removeClass("botones_hab").addClass("botones_des");
    $('#btn_p_nuevo').removeClass("botones_hab").addClass("botones_des");

    var dl_estado = document.getElementById('dl_estado');
    var dl_rol = document.getElementById('dl_rol');
    if (dl_estado.value == '') {
        $('#blEstado').removeClass("floating-disable").addClass("floating-select2");
        document.getElementById("dl_estado").setAttribute("value", "");
    } else {
        $('#blEstado').removeClass("floating-select2").addClass("floating-disable");
    }
    if (dl_rol.value == '') {
        $('#blRol').removeClass("floating-disable").addClass("floating-select2");
        document.getElementById("dl_rol").setAttribute("value", "");
    } else {
        $('#blRol').removeClass("floating-select2").addClass("floating-disable");
    }
}


function Deshacer() {
    $('.nav-tabs li:eq(' + $('#hdd_numeromenus').val() + ') a').tab('show');
    $('#btn_p_editar').removeClass("botones_hab").addClass("botones_des");
    $('#btn_p_eliminar').removeClass("botones_hab").addClass("botones_des");
    $('#btn_p_grabar').removeClass("botones_hab").addClass("botones_des");

    $('#operacion').val('');
    $("#table_id").prop("style", 'pointer-events: all; opacity: 100%;');
    $('#btn_p_back').removeClass("botones_hab").addClass("botones_des");
    $('#btn_p_nuevo').removeClass("botones_des").addClass("botones_hab");

    inicar_menu_nivel3('Administración de Usuarios', '1_li_administracion', '2_li_Usuarios', '2');
}

function CompletarCamposUsuario(obj) {

    $("#dl_empresa").val(obj[0].ccod_empresa);
    $("#tb_ccod_usuario").val(obj[0].ccod_usuario);
    $("#tb_cdsc_usuario").val(obj[0].cdsc_usuario);
    $("#tb_cpassw").val(obj[0].cpassw);
    $("#tb_cdirec").val(obj[0].cdirec);
    $("#tb_nomEmpresa").val(obj[0].empresa);
    $("#tb_email").val(obj[0].cmail);
    $("#tb_celular").val(obj[0].ccelular);
    $("#tb_telf").val(obj[0].ctelf);
    (document.getElementById("dl_estado")).selectedIndex =
        [...(document.getElementById("dl_estado")).options].findIndex(option => option.value === (obj[0].id_estado).toString());
    (document.getElementById("dl_rol")).selectedIndex =
        [...(document.getElementById("dl_rol")).options].findIndex(option => option.value === (obj[0].id_rol).toString());

    var dl_estado = document.getElementById('dl_estado');
    var dl_rol = document.getElementById('dl_rol');
    if (dl_estado.value == '') {
        $('#blEstado').removeClass("floating-disable").addClass("floating-select2");
        document.getElementById("dl_estado").setAttribute("value", "");
    } else {
        $('#blEstado').removeClass("floating-select2").addClass("floating-disable");
    }
    if (dl_rol.value == '') {
        $('#blRol').removeClass("floating-disable").addClass("floating-select2");
        document.getElementById("dl_rol").setAttribute("value", "");
    } else {
        $('#blRol').removeClass("floating-select2").addClass("floating-disable");
    }
}




function table_one_click(tbody) {

    var fila = tbody.onclick.arguments[0].target.parentElement.cells;
    if ($('#hdd_numerofilas').val() > 0)
        $('#hdd_ultimafila').val(fila[0].innerText);

    $('#hdd_ultimafila2').val(fila[4].innerText);

    $("#table_id tr:nth-child(" + $('#hdd_fila').val() + ")").css('background', '');
    var index = tbody.onclick.arguments[0].target.parentElement.rowIndex;
    $("#table_id tr:nth-child(" + index + ")").css('background', 'silver');
    $('#hdd_fila').val(index);

    $('#lb_codigo').text("Compañia : " + fila[4].innerText);

    $('#lb_nombre').text(fila[5].innerText);


}

$(document).ready(function () {

    document.getElementById("dl_estado").setAttribute("value", "");
    document.getElementById("dl_rol").setAttribute("value", "");

    $("#span2").hide();

    inicar_menu_nivel3('Administración de Usuarios', '1_li_administracion', '2_li_Usuarios', '2');
    traducir_tabla();
    CargarTablaUsuario();

    if ($('#hdd_numerofilas').val() > 0)
        $('#hdd_ultimafila').val($('#table_id')[0].rows[1].cells[0].innerText);
    $('#hdd_ultimafila2').val($('#table_id')[0].rows[1].cells[4].innerText);
    $('#lb_codigo').text("Compañia : " + $('#table_id')[0].rows[1].cells[4].innerText);
    $('#lb_nombre').text($('#table_id')[0].rows[1].cells[5].innerText);

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

    $("#thTablaUsuariosAsociados").contextMenu({
        menuSelector: "#contextMenu",
        menuSelected: function (invokedOn, selectedMenu) {
            //click para exporta a exeltableExportarBuscarEmpresa
            var blob = new Blob([document.getElementById('tableExportarUsuariosAsociados').innerHTML], {
                type: 'application/xml;charset=utf-8', encoding: 'utf-8'
            });
            saveAs(blob, "Reporte del " + hoy + ".xls");
        }
    });
});

function tab_datosclick() {
    if ($('#operacion').val() == '') {
        if ($('#hdd_ultimafila').val() != '') {
            $.ajax({
                type: "POST",
                url: 'AdministrarUsuarios.aspx/ConsultarUsuario',
                data: '{codigo: "' + $('#hdd_ultimafila').val() + '" }',
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                async: false,

                success: function (response) {
                    if (response.d) CompletarCamposUsuario(response.d);
                    else MensajeFinSession();
                },
                error: function (xhr, status, error) {
                    alert(error);
                }
            });


            $('#btn_p_editar').removeClass("botones_des").addClass("botones_hab");
            $('#btn_p_eliminar').removeClass("botones_des").addClass("botones_hab");

        }

        Desabilitar();
    }
}



function table_two_click(tbody) {
    var fila = tbody.ondblclick.arguments[0].target.parentElement.cells;
    if ($('#hdd_numerofilas').val() > 0) {
        $.ajax({
            type: "POST",
            url: 'AdministrarUsuarios.aspx/ConsultarUsuario',
            data: '{codigo: "' + fila[0].innerText + '" }',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: false,

            success: function (response) {
                if (response.d) CompletarCamposUsuario(response.d);
                else MensajeFinSession();
            },
            error: function (xhr, status, error) {
                alert(error);
            }
        });
    }
    $('.nav-tabs li:eq(0) a').tab('show');
    Desabilitar();
    $('#btn_p_editar').removeClass("botones_des").addClass("botones_hab");
    $('#btn_p_eliminar').removeClass("botones_des").addClass("botones_hab");
}

function CargarTablaDetalleAccesos() {
    $("#tblDetalle tbody").html("");

    var obj = llenarobjeto('AdministrarUsuarios.aspx/MenuAccesos');

}




function Guardar() {
    console.log("Guardar ejecutado");
    console.log("empresa:", $('#dl_empresa').val());
    console.log("nomServidor:", $('#nomServidor').val());
    console.log("ipServidor:", $('#ipServidor').val());
    console.log("operacion:", $('#operacion').val());

    //        CargarTablaDetalleAccesos();

    var tb_ccod_usuario = document.getElementById("tb_ccod_usuario");
    var tb_cdsc_usuario = document.getElementById("tb_cdsc_usuario");
    var tb_cpassw = document.getElementById("tb_cpassw");
    var dl_rol = document.getElementById("dl_rol");
    var dl_empresa = document.getElementById("dl_empresa");
    var dl_estado = document.getElementById("dl_estado");
    var tb_nomEmpresa = document.getElementById("tb_nomEmpresa");


    var tb_email = document.getElementById("tb_email");
    var tb_celular = document.getElementById("tb_celular");
    var tb_cdirec = document.getElementById("tb_cdirec");

    if (tb_ccod_usuario.value == "") {
        Mensaje('Advertencia', 'Ingresar codigo de usuario', 'warning');
        return false;
    } else if (tb_cdsc_usuario.value == "") {
        Mensaje('Advertencia', 'Ingresar nombre de usuario', 'warning');
        return false;
    } else if (tb_cpassw.value == "") {
        Mensaje('Advertencia', 'Ingresar contraseña de usuario', 'warning');
        return false;
    } else if (dl_estado.value == "") {
        Mensaje('Advertencia', 'Ingresar estado de usuario', 'warning');
        return false;
    } else if (dl_rol.value == "") {
        Mensaje('Advertencia', 'Ingresar rol de usuario', 'warning');
        return false;
    } else if (dl_empresa.value == "") {
        Mensaje('Advertencia', 'Ingresar codigo de empresa', 'warning');
        return false;
    } else if (tb_nomEmpresa.value == "") {
        Mensaje('Advertencia', 'Ingresar nombre de empresa', 'warning');
        return false;
    }

    var obj_detalle = $('#tblDetalle tr:has(td)').map(function (i, v) {
        var $td = $('td', this);
        return {
            cdsc_menu: $td.eq(0).text(),
            curl: $td.eq(1).text(),
            cli_menu: $td.eq(2).text(),
            cul_menu: $td.eq(3).text(),
            corden: $td.eq(4).text(),
            cstatus: $td.eq(5).text(),
            nivel: $td.eq(6).text(),
            id_menu: $td.eq(7).text()
        }
    }).get();



    var obj = [
        {
            "ccod_usuario": $('#tb_ccod_usuario').val(),
            "cdsc_usuario": $('#tb_cdsc_usuario').val(),
            "cpassw": $('#tb_cpassw').val(),
            "cdirec": $('#tb_cdirec').val(),
            "id_rol": $('#dl_rol').val(),
            "ccod_empresa": $('#dl_empresa').val(),
            "cstatus": $('#dl_estado').val(),
            "cmail": $('#tb_email').val(),
            "ctelf": $('#tb_telf').val(),
            "ccelular": $('#tb_celular').val(),
            "cnombre_servidor": $('#ipServidor').val(),
            "cnombre_bd": $('#nomServidor').val()
        }
    ]


    $.ajax({
        type: "POST",
        url: 'AdministrarUsuarios.aspx/GrabarUsuario',
        data: JSON.stringify({ usuario: obj, operacion: $('#operacion').val() }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,

        success: function (response) {
            console.log("response.d:", response.d);        // <-- agrega esta línea
            console.log("tipo:", typeof response.d);        // <-- y esta
            console.log("d[0]:", response.d[0]);            // <-- y esta
            console.log("d[1]:", response.d[1]); 

            if (response.d == "-1") MensajeFinSession();
            else {
                obj = response.d;

                // --- SHOW ERROR IF CHILD BD DOES NOT EXIST ---
                if (obj[1] == 'ERROR') {
                    Mensaje('Error', obj[2], 'error');
                    return;
                }
                // --- END ERROR HANDLING ---

                if (obj[1] == 'OK') {
                    Mensaje('Correcto', '', 'success');
                    $('#table_id').DataTable().destroy();
                    CargarTablaUsuario();
                    $('.nav-tabs li:eq(1) a').tab('show');
                    Desabilitar();
                    Deshacer();
                    inicar_menu_nivel3('Administrar Usuarios', '1_li_administracion', '2_li_Usuarios', '2');

                    $('#hdd_ultimafila').val($('#table_id')[0].rows[1].cells[0].innerText);
                    $('#hdd_ultimafila2').val($('#table_id')[0].rows[1].cells[4].innerText);
                    $('#lb_codigo').text("Compañia : " + $('#table_id')[0].rows[1].cells[4].innerText);
                    $('#lb_nombre').text($('#table_id')[0].rows[1].cells[5].innerText);
                }
            }

            if (response.d == false) Mensaje('Error', 'No se realizó la operación', 'error');
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });
}


function Eliminar() {

    Swal.fire({
        title: "¿Estas seguro?",
        text: "No podrás revertir el cambio",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, proceder'
    }).then((result) => {
        if (result.value) {

            var obj;

            $.ajax({
                type: "POST",
                url: 'AdministrarUsuarios.aspx/Eliminar',
                data: '{usuario: "' + $('#tb_ccod_usuario').val() + '",ipServidor: "' + $('#ipServidor').val() + '",nomServidor: "' + $('#nomServidor').val() + '" }',
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                async: false,

                success: function (response) {

                    if (response.d == "-1") MensajeFinSession();
                    else {
                        if (response.d == true) {
                            Mensaje('Correcto', '', 'success');
                            $('#table_id').DataTable().destroy();
                            CargarTablaUsuario();
                            $('.nav-tabs li:eq(1) a').tab('show');
                            Desabilitar();
                            Deshacer();
                            inicar_menu_nivel3('Administrar Usuarios', '1_li_administracion', '2_li_Usuarios', '2');

                            $('#hdd_ultimafila').val($('#table_id')[0].rows[1].cells[0].innerText);
                            $('#hdd_ultimafila2').val($('#table_id')[0].rows[1].cells[4].innerText);
                            $('#lb_codigo').text("Compañia : " + $('#table_id')[0].rows[1].cells[4].innerText);
                            $('#lb_nombre').text($('#table_id')[0].rows[1].cells[5].innerText);
                        }
                        if (response.d == false) Mensaje('Error', 'No se realizó la operación', 'error');
                    }

                },
                error: function (xhr, status, error) {
                    alert(error);
                }
            });

        }
    });
}