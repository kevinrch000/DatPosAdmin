 <%@ Page Language="vb" AutoEventWireup="false" CodeBehind="Home.aspx.vb" Inherits="WebApplication1.Home" MasterPageFile= "/Site.Master"%>


<asp:Content ID="content1" runat="server" ContentPlaceHolderID="contenedormaster">
 

    <link href="../Styles/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <script src="../Scripts/jquery-2.1.1.js" type="text/javascript"></script>
    <%--<script src="../Scripts/bootstrap.js" type="text/javascript"></script>--%>
    <link href="../Styles/css/alertify.core.css" rel="stylesheet" type="text/css" />
    <link href="../Styles/css/alertify.default.css" rel="stylesheet" type="text/css" />
    <script src="../Scripts/alertify.js" type="text/javascript"></script>
    

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
<link href="../css/material-dashboard.css?v=2.1.2" rel="stylesheet">
 




    <script src="../Javascript/Comun.js" type="text/javascript"></script>

    <input id="operacion" type="hidden" runat="server"/>
   
    <input id="hdd_ultimafila" type="hidden"/>

    <input id="hdd_fila" type="hidden" value="0"/>
	<input id="hdd_numeromenus" type="hidden" value="1"/>
     <input id="hdd_numerofilas" type="hidden"/>



    <div class="c-content-center"  >
    <div class="menu idxconsul" style="text-align: center;padding-bottom: 20px;">
                      <img src="../Styles/images/icon/icon_LogoCircle.png" style="width:7%;">
                      <h2>Bienvenido al Portal de Administración</h2>
                      
                 </div>


     <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon" >
                    <i class="material-icons">business</i>
                  </div>
                <p class="card-category">Cantidad de Empresas</p>
                  <h3 id="cantidadTienda" class="card-title">0</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
             
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">supervisor_account</i>
                  </div>
                  <p class="card-category">Cantidad de Usuarios</p>
                  <h3 id="cantidadUsuario" class="card-title">0</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                   
                  </div>
                </div>
              </div>
            </div>
   
           


    </div>
 


    <script type="text/javascript">



        $(document).ready(function () {




            $('#id_titulo').text("Dashboard - DATPOS");
//            CargarUsuar();
//            CargarTa();
            $('#btn_p_nuevo').hide();
            $('#btn_p_editar').hide();
            $('#btn_p_grabar').hide();
            $('#btn_p_eliminar').hide();
            $('#btn_p_back').hide();
            $('#btn_p_imprimir').hide();
            $('#btn_p_exel').hide();

        });
//        function CargarTa() {

//            var obj = llenarobjeto('Home.aspx/CantidadEmpresas');

//            $("#cantidadTienda").text(obj[0].cantidaTienda);


//        }
//        function CargarUsuar() {

//            var obj = llenarobjeto('Home.aspx/CantidadUsuarios');

//            $("#cantidadUsuario").text(obj[0].cantidaUsuarios);


//        }

    </script>

</asp:Content>


