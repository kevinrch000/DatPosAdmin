 var fecha = new Date();
var hoy = fecha.getDate() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getFullYear() + "-" + fecha.getHours() + ":" + fecha.getMinutes() + ":" + fecha.getSeconds() + ":" + fecha.getMilliseconds();


function Nuevo() {

    $('#txtcodigo').focus();
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
}

//function CargarTarifa() { 
//    $.ajax({
//        type: "POST",
//        url: 'AdministrarCompanias.aspx/CargarTarifa',
//        data: '{codigo: "' + "cod" + '" }',
//        contentType: "application/json; charset=utf-8",
//        dataType: "json",
//        async: false,

//        success: function (response) {
//            var obj1 = response.d;  
//           for (var i = 0; i < obj1.length; i++) {
//               $('#dl_tarifas').append('<option value="' + obj1[i].cdescripcion + '">' + obj1[i].cdescripcion + '</option>');
//           }  
//            },
// 
//        error: function (xhr, status, error) {
//            alert(error);
//        }
//    }); 
//}

function CompletarCampos(obj) {
    if (!obj || !obj[0]) {
        console.warn('El objeto empresa vino vacío o fue undefined');
        return;
    }

    $("#txtcodigo").val(obj[0].ccod_empresa || "");
    $("#txtnombre").val(obj[0].cdescripcion || "");
    $("#txtRuc").val(obj[0].cnum_tribu || "");
    $("#txtNombreServ").val(obj[0].cnombre_servidor || "");
    $("#txtBD").val(obj[0].cnombre_bd || "");

    // Si tu BE ya no tiene cid_tributario, evitamos que se rompa validando si existe
    if (obj[0].cid_tributario !== undefined) {
        $("#txtIdTributario").val(obj[0].cid_tributario);
    }

    $("#txtAddUsuario").val(obj[0].nusuario_extra || 0);
    $("#txtAddTienda").val(obj[0].ntienda_extra || 0);

    if (document.getElementById("dl_tarifas") && obj[0].ctarifas != null) {
        (document.getElementById("dl_tarifas")).selectedIndex =
            [...(document.getElementById("dl_tarifas")).options].findIndex(option => option.value === obj[0].ctarifas.toString());
    }

    if (document.getElementById("txtNombreMoneda") && obj[0].cnombre_moneda != null) {
        (document.getElementById("txtNombreMoneda")).selectedIndex =
            [...(document.getElementById("txtNombreMoneda")).options].findIndex(option => option.text === obj[0].cnombre_moneda.toString());
    }

    $("#tb_direccion").val(obj[0].cdomicilio || "");
    $("#td_urbanizacion").val(obj[0].curbanizacion || "");
    $("#txtfchInicio").val(obj[0].dfch_sunat || "");

    if (document.getElementById("txt_nenviosunat") && obj[0].nenviosunat != null) {
        (document.getElementById("txt_nenviosunat")).selectedIndex =
            [...(document.getElementById("txt_nenviosunat")).options].findIndex(option => option.value === obj[0].nenviosunat.toString());
    }

    if (document.getElementById("txtDepartamento") && obj[0].cdepartamento != null) {
        (document.getElementById("txtDepartamento")).selectedIndex =
            [...(document.getElementById("txtDepartamento")).options].findIndex(option => option.text === obj[0].cdepartamento.toString());
    }

    CargarProvincia();

    if (document.getElementById("txtProvincia") && obj[0].cprovincia != null) {
        (document.getElementById("txtProvincia")).selectedIndex =
            [...(document.getElementById("txtProvincia")).options].findIndex(option => option.text === obj[0].cprovincia.toString());
    }

    CargarDistrito();

    if (document.getElementById("txtDistrito") && obj[0].cdistrito != null) {
        (document.getElementById("txtDistrito")).selectedIndex =
            [...(document.getElementById("txtDistrito")).options].findIndex(option => option.text === obj[0].cdistrito.toString());
    }

    if (document.getElementById("TipFact") && obj[0].ctip_facturador != null) {
        (document.getElementById("TipFact")).selectedIndex =
            [...(document.getElementById("TipFact")).options].findIndex(option => option.value === (obj[0].ctip_facturador).toString());
    }

    $("#txtccod_cliente_emis").val(obj[0].ccod_cliente_emis || "");
    $("#txtUbigeo").val(obj[0].cubigeo || "");
    $('#txtfchVencimiento').val(obj[0].dfch_vencimiento || "");
    $('#txtctoken').val(obj[0].ctoken || "");
}

// Inmediatamente debajo de todo esto debe seguir tu función CargarTabla() original

   function CargarTabla() {

    var obj = llenarobjeto('AdministrarCompanias.aspx/ConsultarEmpresas');
    $('#table_principal').DataTable().destroy();
    $('#table_id').DataTable().destroy();

    $('#hdd_numerofilas').val(obj.length);
    $('#table_id').DataTable({
        data: obj,
        columns: [
                { data: 'id_empresa' },
                { data: 'ccod_empresa' },
                { data: 'cdescripcion' },
                { data: 'cnum_tribu' },
                { data: 'cnombre_servidor' },
                { data: 'cnombre_bd' },
                { data: 'cnombre_moneda' },
                { data: 'ctarifas' },
                { data: 'dfch_crea' }]
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
                 { data: 'id_empresa' },
                { data: 'ccod_empresa' },
                { data: 'cdescripcion' },
                { data: 'cnum_tribu' },
                { data: 'cnombre_servidor' },
                { data: 'cnombre_bd' },
                { data: 'cnombre_moneda' },
                { data: 'ctarifas' },
                { data: 'dfch_crea' }],
                scrollX: "2000px",
                scrollCollapse: true,
   });


    $('#table_id').attr("style", "width: -webkit-fill-available;");
}


function table_one_click(tbody) {
    var fila = tbody.onclick.arguments[0].target.parentElement.cells;
   if($('#hdd_numerofilas').val()>0) 
    $('#hdd_ultimafila').val(fila[1].innerText);
    
    $("#table_id tr:nth-child("+$('#hdd_fila').val()+")").css('background', '');
    var index = tbody.onclick.arguments[0].target.parentElement.rowIndex;
    $("#table_id tr:nth-child("+index+")").css('background', 'silver');
    $('#hdd_fila').val(index);
}

function CargarDepartamento(){
    var listBox = document.getElementById("txtDepartamento");
    listBox.options.length = 0;

    $.ajax({
        type: "POST",
        url: 'AdministrarCompanias.aspx/CargarDepartamento',
        data: '{ccod_cia: "' + "" + '" }',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (response) {
            if(response.d){
                var obj = response.d;
                $('#txtDepartamento').append('<option   value=""> </option>');
                for (var i = 0; i < obj.length; i++) {
                    $('#txtDepartamento').append('<option value="' + obj[i].id + '">' + obj[i].name + '</option>');
                }
            } 
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });
}

function CargarProvincia(){
    if ($('#txtDepartamento').val()==""){
        return;
    }
    $('#txtUbigeo').val("");
    $('#txtDistrito').val(""); 

    var listBox = document.getElementById("txtProvincia");
    listBox.options.length = 0;
    $.ajax({
        type: "POST",
        url: 'AdministrarCompanias.aspx/CargarProvincia',
        data: '{id_departamento: "' + $('#txtDepartamento').val() + '" }',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (response) {
            if(response.d){
                var obj = response.d;
                $('#txtProvincia').append('<option   value=""> </option>');
                for (var i = 0; i < obj.length; i++) {
                    $('#txtProvincia').append('<option value="' + obj[i].id + '">' + obj[i].name + '</option>');
                } 
            }
            else MensajeFinSession();
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });
}
 
function CargarDistrito(){
    if ($('#txtProvincia').val()==""){
        return;
    }
    $('#txtUbigeo').val("");
     

    var listBox = document.getElementById("txtDistrito");
    listBox.options.length = 0;
    $.ajax({
        type: "POST",
        url: 'AdministrarCompanias.aspx/CargarDistrito',
        data: '{id_provincia: "' + $('#txtProvincia').val() + '" }',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (response) {
            if(response.d){
                var obj = response.d;
                $('#txtDistrito').append('<option   value=""> </option>');
                for (var i = 0; i < obj.length; i++) {
                    $('#txtDistrito').append('<option value="' + obj[i].id + '">' + obj[i].name + '</option>');
                }
            }
            else MensajeFinSession();
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });
}

function CargarUbigeo(){
    if ($('#txtDistrito').val()==null){
        return;
    }
     $('#txtUbigeo').val($('#txtDistrito').val())
}


$(document).ready(function () {

CargarDepartamento();
//CargarTarifa();
//document.getElementById("ddl_td").setAttribute("value", ""); 
//document.getElementById("txtNombreMoneda").setAttribute("value", "");
//document.getElementById("TipFact").setAttribute("value", "");

    inicar_menu_nivel3('Administración de Empresas','1_li_administracion', '2_li_empresas', '2');
    traducir_tabla();
    CargarTabla();
     if($('#hdd_numerofilas').val()>0) 
     $('#hdd_ultimafila').val($('#table_id')[0].rows[1].cells[1].innerText);

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

  


});


function tab_datosclick() {
    if($('#operacion').val() == '') {
        if($('#hdd_ultimafila').val() != '') {

            $.ajax({
                type: "POST",
                url: 'AdministrarCompanias.aspx/ConsultarEmpresa',
                data: '{codigo: "' + $('#hdd_ultimafila').val() + '" }',
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                async: false,

                success: function (response) {
                    if (response.d) CompletarCampos(response.d);
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

        // CORRECCIÓN: usamos fila[1] y limpamos espacios con .trim()
        var codEmpresa = fila[1].innerText.trim();

        $.ajax({
            type: "POST",
            url: 'AdministrarCompanias.aspx/ConsultarEmpresa',
            data: '{codigo: "' + codEmpresa + '" }',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: false,

            success: function (response) {
                if (response.d) CompletarCampos(response.d);
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


 

function Guardar() {

    // Extracción de datos con valores por defecto (evita nulos o undefined)
    var ccod = $('#txtcodigo').val() || "";
    var cdesc = $('#txtnombre').val() || "";
    var cnum_tribu = $('#txtRuc').val() || "";
    var cn_bd = $('#txtBD').val() || "";
    var cn_serv = $('#txtNombreServ').val() || "";
    var csimbolo = $('#txtNombreMoneda').val() || "";

    var cnom_mon = "";
    if (document.getElementById("txtNombreMoneda")) {
        cnom_mon = $("#txtNombreMoneda option:selected").text() || "";
    }

    var ctarifas_val = "";
    if (document.getElementById("dl_tarifas")) {
        ctarifas_val = $('#dl_tarifas').val() || "";
    }

    // Parseo INT estricto. Si es "" pasa a 0 para que VB no de error de casting
    var usu_ext = parseInt($('#txtAddUsuario').val()) || 0;
    var tda_ext = parseInt($('#txtAddTienda').val()) || 0;

    var cdom = $('#tb_direccion').val() || "";
    var curb = $('#td_urbanizacion').val() || "";

    var cdpto = "";
    if (document.getElementById("txtDepartamento")) {
        cdpto = $("#txtDepartamento option:selected").text() || "";
    }
    var cprov = "";
    if (document.getElementById("txtProvincia")) {
        cprov = $("#txtProvincia option:selected").text() || "";
    }
    var cdist = "";
    if (document.getElementById("txtDistrito")) {
        cdist = $("#txtDistrito option:selected").text() || "";
    }

    var cubigeo = $('#txtUbigeo').val() || "";

    var enviosunat = "";
    if (document.getElementById("txt_nenviosunat")) {
        enviosunat = $('#txt_nenviosunat').val() || "";
    }

    // Fechas (enviamos blanco si no hay valor para que VB identifique que está vacío)
    var fch_sunat = $('#txtfchInicio').val() || "";
    var fch_vencimiento = $('#txtfchVencimiento').val() || "";

    var ccliente_emis = $('#txtccod_cliente_emis').val() || "";
    var ctoken_val = $('#txtctoken').val() || "";

    var ctip_fact = "";
    if (document.getElementById("TipFact")) {
        ctip_fact = $('#TipFact').val() || "";
    }

    var objEmpresa = [{
        "ccod_empresa": ccod,
        "cdescripcion": cdesc,
        "cnum_tribu": cnum_tribu,
        "cnombre_bd": cn_bd,
        "cnombre_servidor": cn_serv,
        "csimbolo_moneda": csimbolo,
        "cnombre_moneda": cnom_mon,
        "ctarifas": ctarifas_val,
        "nusuario_extra": usu_ext,
        "ntienda_extra": tda_ext,
        "cdomicilio": cdom,
        "curbanizacion": curb,
        "cdepartamento": cdpto,
        "cprovincia": cprov,
        "cdistrito": cdist,
        "cubigeo": cubigeo,
        "nenviosunat": enviosunat,
        "dfch_sunat": fch_sunat,
        "ccod_cliente_emis": ccliente_emis,
        "dfch_vencimiento": fch_vencimiento,
        "ctoken": ctoken_val,
        "ctip_facturador": ctip_fact
    }];

    $.ajax({
        type: "POST",
        url: 'AdministrarCompanias.aspx/GrabarEmpresa',
        data: JSON.stringify({ empresa: objEmpresa, operacion: $('#operacion').val() }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,

        success: function (response) {
            if (response.d == true) {
                Mensaje('Correcto', '', 'success');
                $('#table_id').DataTable().destroy();
                CargarTabla();
                $('.nav-tabs li:eq(1) a').tab('show');
                Desabilitar();
                Deshacer();
            } else {
                Mensaje('Error', 'No se realizó la operación', 'error');
            }
        },
        error: function (xhr, status, error) {
            alert('Error 500 detectado: Revisa los parámetros numéricos y de fechas.');
        }
    });
}
 

function Eliminar(){

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
            url: 'AdministrarCompanias.aspx/EliminarE',
            data: '{elimrempresa: "' + $('#txtcodigo').val() + '" }',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: false,

            success: function (response) {
                    if(response.d==true){
                        Mensaje('Correcto','','success');
                        $('#table_id').DataTable().destroy();
                        CargarTabla();
                        $('.nav-tabs li:eq(3) a').tab('show');
                        Desabilitar();
                        Deshacer();
                        inicar_menu_nivel3('Administrar Compañia','1_li_administracion', '2_li_empresas', '1');
                    }
                    if(response.d==false) Mensaje('Error','No se realizó la operación','error');
                
            },
            error: function (xhr, status, error) {
                alert(error);
            }
        }); 

      }
    });
}

$.datepicker.regional['es'] = {
    closeText: 'Cerrar',
    prevText: '< Ant',
    nextText: 'Sig >',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
    weekHeader: 'Sm',
    dateFormat: 'dd/mm/yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['es']);
$(function () {
    $("#txtfchInicio").datepicker();
    $("#txtfchVencimiento").datepicker();
});


//function Limpiar(){
//    $("#txtcodigo").val("");
//    $("#tb_descripcion").val("");
//    $("#ddl_familia").val("");
//    $("#ddl_um").val("");
//    $("#ddl_estado").val("");
//}