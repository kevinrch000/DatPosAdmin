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

function CompletarCampos(obj){
    

    $("#txtcodigo").val(obj[0].ccod_empresa);
    $("#txtnombre").val(obj[0].cdescripcion);
    $("#txtRuc").val(obj[0].cnum_tribu);
    $("#txtNombreServ").val(obj[0].cnombre_servidor );
    $("#txtBD").val(obj[0].cnombre_bd );
    $("#txtIdTributario").val(obj[0].cid_tributario );
   
    $("#txtAddUsuario").val(obj[0].nusuario_extra);
    $("#txtAddTienda").val(obj[0].ntienda_extra);
  
    (document.getElementById("dl_tarifas")).selectedIndex = 
    [...(document.getElementById("dl_tarifas")).options].findIndex(option => option.value === (obj[0].ctarifas).toString());

    (document.getElementById("txtNombreMoneda")).selectedIndex = 
    [...(document.getElementById("txtNombreMoneda")).options].findIndex(option => option.text === (obj[0].cnombre_moneda).toString());
    $("#tb_direccion").val(obj[0].cdomicilio);
    $("#td_urbanizacion").val(obj[0].curbanizacion);
     
    $("#txtfchInicio").val(obj[0].dfch_sunat);

     (document.getElementById("txt_nenviosunat")).selectedIndex = 
    [...(document.getElementById("txt_nenviosunat")).options].findIndex(option => option.value === (obj[0].nenviosunat).toString());

    (document.getElementById("txtDepartamento")).selectedIndex = 
    [...(document.getElementById("txtDepartamento")).options].findIndex(option => option.text === (obj[0].cdepartamento).toString());

    CargarProvincia();

    (document.getElementById("txtProvincia")).selectedIndex = 
    [...(document.getElementById("txtProvincia")).options].findIndex(option => option.text === (obj[0].cprovincia).toString());

    CargarDistrito();

    (document.getElementById("txtDistrito")).selectedIndex = 
    [...(document.getElementById("txtDistrito")).options].findIndex(option => option.text === (obj[0].cdistrito).toString());
     
     (document.getElementById("TipFact")).selectedIndex = 
    [...(document.getElementById("TipFact")).options].findIndex(option => option.value === (obj[0].ctip_facturador).toString());

     $("#txtccod_cliente_emis").val(obj[0].ccod_cliente_emis); 

     $("#txtUbigeo").val(obj[0].cubigeo); 
     $('#txtfchVencimiento').val(obj[0].dfch_vencimiento);
     $('#txtctoken').val(obj[0].ctoken);
      
   }

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
    $('#hdd_ultimafila').val(fila[0].innerText);
    
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
     $('#hdd_ultimafila').val($('#table_id')[0].rows[1].cells[0].innerText);

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


 
function table_two_click(tbody){
    var fila = tbody.ondblclick.arguments[0].target.parentElement.cells;
    if($('#hdd_numerofilas').val()>0) {
    $.ajax({
        type: "POST",
        url: 'AdministrarCompanias.aspx/ConsultarEmpresa',
        data: '{codigo: "' + fila[0].innerText + '" }',
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
 
var objEmpresa = [
        { 
        "ccod_empresa": $('#txtcodigo').val(),
        "cdescripcion": $('#txtnombre').val(),
        "cnum_tribu": $('#txtRuc').val(),
        "cnombre_bd": $('#txtBD').val(),
        "cnombre_servidor": $('#txtNombreServ').val(), 
        "csimbolo_moneda": $('#txtNombreMoneda').val(),
        "cnombre_moneda": $("#txtNombreMoneda option:selected").text(),  
        "ctarifas": $('#dl_tarifas').val(),
        "nusuario_extra": $('#txtAddUsuario').val(),
        "ntienda_extra": $('#txtAddTienda').val(),
        "cdomicilio": $('#tb_direccion').val(),
        "curbanizacion": $('#td_urbanizacion').val(),
        "cdepartamento": $("#txtDepartamento option:selected").text(),
        "cprovincia": $("#txtProvincia option:selected").text(), 
        "cdistrito": $("#txtDistrito option:selected").text(),    
        "cubigeo": $('#txtUbigeo').val(),
        "nenviosunat": $('#txt_nenviosunat').val(),
        "dfch_sunat": $('#txtfchInicio').val(),
        "ccod_cliente_emis": $('#txtccod_cliente_emis').val(),
        "dfch_vencimiento": $('#txtfchVencimiento').val(),
        "ctoken": $('#txtctoken').val(),
        "ctip_facturador": $('#TipFact').val()
        }
    ]

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
            }

            if (response.d == false) Mensaje('Error', 'No se realizó la operación', 'error');
        },
        error: function (xhr, status, error) {
            alert(error);
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