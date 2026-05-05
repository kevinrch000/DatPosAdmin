
    
    
     
   <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script>
    <link href="<?= $base ?>/assets/Styles/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"/>
   
   <script src="<?= $base ?>/assets/Javascript/Empresa.js" type="text/javascript"></script>
  
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
    <input id="operacion" type="hidden"/>
   
    <input id="hdd_ultimafila" type="hidden"/>

    <input id="hdd_fila" type="hidden" value="0"/>
	<input id="hdd_numeromenus" type="hidden" value="1"/>
     <input id="hdd_numerofilas" type="hidden"/>
          
    <link href="<?= $base ?>/assets/Styles/bootstrap-float-label.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/disenoBotones.css" rel="stylesheet" type="text/css" />

    <div class="c-content-center">

        <ul class="nav nav-tabs" style="">
            <li onclick="tab_datosclick();" class="active">
            <a data-toggle="tab" class="tabcito" href="#Datos" style="color: #228ac9;
                font-size: 17px;">Datos</a></li>
         <li onclick="tab_listaclick();">
         <a data-toggle="tab" href="#Lista" class="tabcito" style="color: #228ac9; 
             font-size: 17px;">
                Listado</a></li>
        </ul>
        <div class="tab-content">
            <!-- DATOS -->
        
            <div id="Datos" class="tab-pane in active " style="padding: 13px;">

            <h4 style="border-bottom: groove; margin-bottom: 30px; margin-top: 30px; width:60%;">Información General</h4>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtcodigo" class="readonl limpiar form-control moderno_tb" maxlength="5"
                                placeholder=" " readonly />
                            <label for="txtcodigo">
                                Codigo de Empresa</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtnombre" class="disabled limpiar form-control moderno_tb" maxlength="40"
                                placeholder=" " />
                            <label for="txtnombre">
                                Nombre de Empresa</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtRuc" class="disabled limpiar form-control moderno_tb" maxlength="11"
                                placeholder=" " />
                            <label for="txtRuc">
                                Número Documento</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <select class="disabled limpiar form-control moderno_tb floating-select" id="txtNombreMoneda"
                                disabled>
                                <option value="S/">Soles</option>
                                <option value="$">Dolares</option>
                            </select>
                            <label class="floating-select2">
                                Nombre Moneda</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="tb_direccion" class="disabled limpiar form-control moderno_tb" maxlength="100"
                                placeholder=" " />
                            <label for="tb_direccion">
                                Domicilio</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="td_urbanizacion" class="disabled limpiar form-control moderno_tb" maxlength="100"
                                placeholder=" " />
                            <label for="td_urbanizacion">
                                Urbanización</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <select class="disabled limpiar form-control moderno_tb floating-select" onchange="CargarProvincia();"
                                id="txtDepartamento" value="">
                            </select>
                            <label class="floating-select2">
                                Departamento</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <select class="disabled limpiar form-control moderno_tb floating-select" onchange="CargarDistrito();"
                                id="txtProvincia" value="">
                            </select>
                            <label class="floating-select2">
                                Provincia</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <select class="disabled limpiar form-control moderno_tb floating-select" onchange="CargarUbigeo();"
                                id="txtDistrito" value="">
                            </select>
                            <label class="floating-select2">
                                Distrito</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtUbigeo" class="limpiar form-control moderno_tb" disabled maxlength="50"
                                placeholder=" " />
                            <label for="txtUbigeo">
                                Ubigeo</label>
                        </span>
                    </div>
                </div>
                <h4 style="border-bottom: groove; margin-bottom: 30px; margin-top: 60px; width: 60%;">
                    Atributos de la Empresa</h4>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtNombreServ" class="disabled limpiar form-control moderno_tb" maxlength="40"
                                placeholder=" " />
                            <label for="txtNombreServ">
                                Nombre Servidor BD</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtBD" class="disabled limpiar form-control moderno_tb" maxlength="40"
                                placeholder=" " />
                            <label for="txtBD">
                                Nombre BD</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input type="number" id="txtAddUsuario" min="0" placeholder=" " class="disabled limpiar form-control moderno_tb" />
                            <label for="txtAddUsuario">
                                Cantidad de Usuarios</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input type="number" id="txtAddTienda" min="0" class="disabled limpiar form-control moderno_tb"
                                placeholder=" " />
                            <label for="txtAddTienda">
                                Cantidad de Tiendas</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <select class="disabled limpiar form-control moderno_tb floating-select" id="txt_nenviosunat"
                                value="">
                                <option value="0">Deshabilitar</option>
                                <option value="1">Habilitar</option>
                            </select>
                            <label class="floating-select2">
                                Envio a Sunat</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtfchInicio" maxlength="10" type="text" class="disabled limpiar form-control moderno_tb"
                                placeholder=" " />
                            <label class="floating-select2">
                                Fecha de Inicio Fact.</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <select class="disabled limpiar form-control moderno_tb floating-select" id="TipFact"
                                value="">
                                <option value="0">Deshabilitar</option>
                                <option value="1">Habilitar</option>
                            </select>
                            <label class="floating-select2">
                                Opciones de Boleta y Factura</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtccod_cliente_emis" type="text" class="disabled limpiar form-control moderno_tb"
                                placeholder=" " />
                            <label class="floating-select2">
                                Codigo Cliente Emisor</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtfchVencimiento" maxlength="10" type="text" class="disabled limpiar form-control moderno_tb"
                                placeholder=" " />
                            <label class="floating-select2">
                                Fecha de Vencimiento</label>
                        </span>
                    </div>
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <input id="txtctoken" class="disabled limpiar form-control moderno_tb" maxlength="200"
                                placeholder=" " />
                            <label for="txtctoken">
                                Token</label>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="padding-top: 10px;">
                        <span class="has-float-label">
                            <select class="disabled limpiar form-control moderno_tb floating-select" id="dl_tarifas"
                                value="">
                                <option value="Emprende">Emprende</option>
                                <option value="Express">Express</option>
                                <option value="Estandar">Estándar</option>
                                <option value="Premium">Premium</option>
                            </select>
                            <label class="floating-select2">
                                Tarifas</label>
                        </span>
                    </div>
                </div>
                     

               </div>
              <!-- Tabla principal Visible --> 
            <div id="Lista" class="tab-pane tabcito" style="padding: 13px;">
                <nav class="navbar navbar-default" style="margin-bottom: 0px;" >
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse navbar-right" style="margin-right: 4.5%;">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><img src="<?= $base ?>/assets/Styles/img/filtro.png" style="WIDTH:14PX;MARGIN-RIGHT:5PX;" />FILTROS <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" onclick="FilterStatus(2);">Abiertos</a></li>
                                <li><a href="#"onclick="FilterStatus(3);">Cerrados</a></li>
                                <li><a href="#"onclick="FilterStatus(4);">Anulados</a></li>
                                <li><a href="#" onclick="FilterStatus(1);">Mostrar Todos</a></li>
                            </ul>
                            </li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div><!--/.container-fluid -->
            </nav>
                <table id="table_id" class="display" style="width:100%;" >
                  <colgroup>
                    <col style="width:5%"></col>
                    <col style="width:10%"></col>
                    <col style="width:20%"></col>
                    <col style="width:15%"></col>
                    <col style="width:15%"></col> 
                    <col style="width:15%"></col>
                    <col style="width:15%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col>
                </colgroup>
                    <thead  id="thTablaVisible">
                        <tr>
                            <th>
                                item
                            </th>
                            <th>
                                Código
                            </th>
                            <th>
                                Empresa
                            </th>
                            <th>
                                Ruc
                            </th>
                            <th>
                                Nombre Servidor
                            </th>
                            <th>
                                Nombre BD
                            </th>
                             
                            <th>
                                Nombre de moneda
                            </th>
                            <th>
                                Tarifas
                            </th>
                            <th>
                                Fecha Crea
                            </th>
                             
                        </tr>
                    </thead>
                   <tbody  ondblclick="table_two_click(this);" onclick="table_one_click(this);" >

                    </tbody>
                </table>
            </div>
            <!-- Tabla para Exportar Principal-->
               <div id="tableExport" style="display:none;" > 
                <table id="table_principal" class="table table-bordered TablaIndex table-striped dataTable no-footer" border="2px" cellspacing="0" width="2000"   >
                <colgroup>
                   <col style="width:5%"></col>
                    <col style="width:10%"></col>
                    <col style="width:20%"></col>
                    <col style="width:15%"></col>
                    <col style="width:15%"></col> 
                    <col style="width:15%"></col>
                    <col style="width:15%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col>
                </colgroup>
                    <thead>
                       <tr>
                            <th>
                                item
                            </th>
                            <th>
                                Código
                            </th>
                            <th>
                                Empresa
                            </th>
                            <th>
                                Ruc
                            </th>
                            <th>
                                Nombre Servidor
                            </th>
                            <th>
                                Nombre BD
                            </th>
 
                            <th>
                                Nombre de moneda
                            </th>
                            <th>
                                Tarifas
                            </th>
                             <th>
                                Fecha Crea
                            </th>
                        </tr>
                    </thead>
                
                     <tbody>

                    </tbody>
                </table> 
                </div>




        </div>
    </div>
