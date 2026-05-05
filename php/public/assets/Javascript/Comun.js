

//Captura click derecho para generar Menu context  
(function ($, window) { 
        $.fn.contextMenu = function (settings) { 
            return this.each(function () { 
                // Open context menu
                $(this).on("contextmenu", function (e) {
                    // return native menu if pressing control
                    if (e.ctrlKey) return; 
                    //open menu
                    var $menu = $(settings.menuSelector)
                    .data("invokedOn", $(e.target))
                    .show()
                    .css({
                        position: "absolute",
                        left: getMenuPosition(e.clientX, 'width', 'scrollLeft'),
                        top: getMenuPosition(e.clientY, 'height', 'scrollTop')
                    })
                    .off('click')
                    .on('click', 'a', function (e) {
                        $menu.hide();
                        var $invokedOn = $menu.data("invokedOn");
                        var $selectedMenu = $(e.target);

                        settings.menuSelected.call(this, $invokedOn, $selectedMenu);
                    });
                    return false;
                });
                //make sure menu closes on any click
                $('body').click(function () {
                    $(settings.menuSelector).hide();
                });
            });
            function getMenuPosition(mouse, direction, scrollDir) {
                var win = $(window)[direction](),
                scroll = $(window)[scrollDir](),
                menu = $(settings.menuSelector)[direction](),
                position = mouse + scroll;
                // opening menu would pass the side of the page
                if (mouse + menu > win && menu < mouse)
                    position -= menu;
                return position;
            }
        };
    })(jQuery, window);









function PerdidaFoco(obj) {
    if($(obj).val().length == 0) $(obj).val("0.00");
    else {
        if(parseFloat($(obj).val()).toFixed(2)<0) $(obj).val($(obj).val()*(-1));
    }
}

function MensajeFinSession(){
    Swal.fire({
        title: "Tiempo de sesión expirado.",
        icon: 'warning',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Salir'
    }).then(
        (result) => {
            if (result.value) {
                window.location.replace("/Account/Login.php");
            }
   });  
}

function tab_listaclick() {

        $('#btn_p_editar').removeClass("botones_hab").addClass("botones_des");
//        $('#btn_p_grabar').removeClass("botones_hab").addClass("botones_des");
        $('#btn_p_eliminar').removeClass("botones_hab").addClass("botones_des");

        if ($('#operacion').val() != '') {
            $("#table_id").prop("style", 'pointer-events: none;opacity: 0.4;');
        }
}


 




function Desabilitar() {
    $(".readonl").prop("readonly", true);
    $(".disabled").prop("disabled", true);
    $("#operacion").val("");
    $('.fa_enabled').removeClass("fa_enabled").addClass("fa_disabled"); 

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

function Editar() {
    $(".disabled").prop("disabled", false);
    $("#operacion").val("editar");
    $('.fa_disabled').removeClass("fa_disabled").addClass("fa_enabled");

    $('#btn_p_editar').removeClass("botones_hab").addClass("botones_des");
    $('#btn_p_grabar').removeClass("botones_des").addClass("botones_hab");
    $('#btn_p_nuevo').removeClass("botones_hab").addClass("botones_des");
    $('#btn_p_back').removeClass("botones_des").addClass("botones_hab");
    $('#btn_p_eliminar').removeClass("botones_hab").addClass("botones_des");
}

function Mensaje(titulo,texto,icono) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: icono,
        confirmButtonText: 'Ok'
    })
}

function llenarobjeto(st_url) {

    var obj;

    $.ajax({
        type: "POST",
        url: st_url,
        data: null,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,

        success: function (response) {
            if (response.d == "-1") MensajeFinSession();
            else obj = response.d;
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });

    return obj;
}

function inicar_menu_nivel3(titulo, menu_nivel1, menu_nivel2, numeromenuactivo) {
  


    $('#id_titulo').text(titulo);
    $('.nav-tabs li:eq(' + numeromenuactivo + ') a').tab('show');
    
    $('#' + menu_nivel1).trigger("click"); 
    $('#' + menu_nivel2).attr("class", "active");
    
    traducir_tabla();
}

function traducir_tabla() {

    $.extend(true, $.fn.dataTable.defaults, {
        "language": {
            "decimal": ",",
            "thousands": ".",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoPostFix": "",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "loadingRecords": "Cargando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "processing": "Procesando...",
            "search": "",
            "searchPlaceholder": "Buscar",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            //only works for built-in buttons, not for custom buttons
            "buttons": {
                "create": "Nuevo",
                "edit": "Cambiar",
                "remove": "Borrar",
                "copy": "Copiar",
                "csv": "fichero CSV",
                "excel": "tabla Excel",
                "pdf": "documento PDF",
                "print": "Imprimir",
                "colvis": "Visibilidad columnas",
                "collection": "Colección",
                "upload": "Seleccione fichero...."
            },
            "select": {
                "rows": {
                    _: '%d filas seleccionadas',
                    0: 'clic fila para seleccionar',
                    1: 'una fila seleccionada'
                }
            }
        }
    });
}