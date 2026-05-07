 
 

   
   <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script>
    <link href="<?= $base ?>/assets/Styles/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"/>
   
   <script src="<?= $base ?>/assets/Javascript/Usuario.js" type="text/javascript"></script>
   <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
    
    <input id="ipServidor" type="hidden"/>
    <input id="nomServidor" type="hidden"/>
      
    <link href="<?= $base ?>/assets/Styles/bootstrap-float-label.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/disenoBotones.css" rel="stylesheet" type="text/css" />

    <input id="operacion" type="hidden"/>
   
    <input id="hdd_ultimafila" type="hidden"/>

    <input id="hdd_fila" type="hidden" value="0"/>
	<input id="hdd_numeromenus" type="hidden" value="1"/>
     <input id="hdd_numerofilas" type="hidden"/> 
      <input id="hdd_ultimafila2" type="hidden"/> 
    <div class="c-content-center">
        
        <!-- Barra de botones -->
        <div style="margin-bottom:15px; padding:8px 0; border-bottom:1px solid #ddd;">
            <button id="btn_p_nuevo" class="btn botones_hab" onclick="Nuevo();" title="Nuevo">
                <i class="material-icons" style="font-size:18px;vertical-align:middle;">add</i> Nuevo
            </button>
            <button id="btn_p_editar" class="btn botones_des" onclick="Editar();" title="Editar">
                <i class="material-icons" style="font-size:18px;vertical-align:middle;">edit</i> Editar
            </button>
            <button id="btn_p_grabar" class="btn botones_des" onclick="Guardar();" title="Guardar">
                <i class="material-icons" style="font-size:18px;vertical-align:middle;">save</i> Guardar
            </button>
            <button id="btn_p_eliminar" class="btn botones_des" onclick="Eliminar();" title="Eliminar">
                <i class="material-icons" style="font-size:18px;vertical-align:middle;">delete</i> Eliminar
            </button>
            <button id="btn_p_limpiar" class="btn botones_hab" onclick="Limpiar();" title="Limpiar campos" style="margin-left:10px;">
                <i class="material-icons" style="font-size:18px;vertical-align:middle;">cleaning_services</i> Limpiar
            </button>
            <button id="btn_p_back" class="btn botones_des" onclick="Deshacer();" title="Deshacer">
                <i class="material-icons" style="font-size:18px;vertical-align:middle;">undo</i> Deshacer
            </button>
        </div>

        <div class="tab-content">
         <ul class="nav nav-tabs" style="">
             <li onclick="tab_datosclick();" class="active">
            <a data-toggle="tab" class="tabcito" href="#Datos" style="color: #228ac9;
                font-size: 17px;">Datos</a></li>
                 
           <li onclick="tab_listaclick();">
           <a data-toggle="tab" href="#Lista" class="tabcito" style="color: #228ac9; font-size: 17px;">
                Lista</a></li>
        </ul>
            <!-- DATOS -->
            <div id="Datos" class="tab-pane in active " style="padding: 13px;">
            <h4 style="border-bottom: groove; margin-bottom: 30px; margin-top: 30px; width:60%;">Información de Usuario</h4>
         


             
            <div class="row" >
                    <div class="col-sm-6" style="padding-top:10px;" >
                      <span  class="has-float-label"   >
                        <input id="tb_ccod_usuario" class="readonl limpiar form-control moderno_tb" maxlength="8"  type="text"  placeholder=" " readonly />
                        <label for="tb_ccod_usuario">Codigo de Usuario</label>
                      </span>
                    </div>
                    <div class="col-sm-6" style="padding-top:10px;" >
                     <span  class="has-float-label" > 
                            <input id="tb_cdsc_usuario" class="disabled limpiar form-control moderno_tb" maxlength="80" type="text"  placeholder=" "/>
                          <label  for="tb_cdsc_usuario">
                            Nombre de Usuario</label>
                    </span>
                    </div>
                </div>

              <div class="row">
                    <div class="col-sm-6" style="padding-top:10px;" >
                         <div class="input-group">
                       <span  class="has-float-label">
                       <input name="tb_cpassw" type="password" id="tb_cpassw" maxlength="50" class="disabled limpiar  form-control moderno_tb" placeholder=" " />
                        <label for="tb_cpassw">Contraseña</label>
                        </span>
                        <a class="disabled input-group-addon" id="span" onclick="mostrarContrasena();" disabled="" style="padding: -6px -12px;background-color: #ffffff;border:0px">
                             <i class="material-icons">visibility_off</i></a>
                       <a class="disabled input-group-addon" id="span2" onclick="mostrarContrasena();" disabled="" style="background-color: rgb(255, 255, 255); border: 0px; display: none;">
                             <i class="material-icons">visibility</i></a>  
                        </div>
                    </div>
                    <div class="col-sm-6" style="padding-top:10px;" >
                        <div class="floating-label"> 
                            <select id="dl_estado" class="disabled limpiar form-control moderno_tb floating-select" oninput="this.setAttribute('value', this.value);" value="">
	                            <option value="1">Activo</option>
	                            <option value="0">Inactivo</option>
                            </select>
                            <label id="blEstado" class="floating-disable">Estado</label>
                          </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6" style="padding-top:10px;">
                     <span  class="has-float-label">
                        <input id="tb_cdirec" class="disabled limpiar form-control moderno_tb"  maxlength="200" placeholder=" "/>
                        <label for="tb_cdirec">Dirección</label>
                          </span>
                    </div>
                    <div class="col-sm-6" style="padding-top:10px;">
                     <div class="floating-label">  
                        <select class="disabled limpiar form-control moderno_tb floating-select" id="dl_rol" oninput="this.setAttribute('value', this.value);" value="">
                        <option value="1">Administrador Master</option>
	                    <option value="2">Administrador Empresa</option>
                        </select> 
                        <label id="blRol" class="floating-disable">Rol</label>
                         </div>
                    </div>
                </div>
               

                <div class="row">
                    <div class="col-sm-6" style="padding-top:10px;">
                    <span  class="has-float-label">
                        <input type="number" id="tb_celular" min="0"  class="disabled limpiar form-control moderno_tb" placeholder=" "    />
                       <label for="tb_celular">Número Celular</label>
                        </span>
                    </div>
                      <div class="col-sm-6" style="padding-top:10px;">
                        <span  class="has-float-label">
                            <input id="tb_telf" class="disabled limpiar form-control moderno_tb"  maxlength="25" placeholder=" "/>
                            <label for="tb_telf">Número Teléfono</label>
                         </span>
                    </div>
                </div>

              
 
                <div class="row">
                  <div class="col-sm-6" style="padding-top:10px;">
                    <span  class="has-float-label">
                       <input id="tb_email" class="disabled limpiar form-control moderno_tb"  maxlength="50" placeholder=" "/>
                       <label for="tb_email">Email</label>
                    </span>
                </div>
               </div>

               
             <h4 style="border-bottom: groove; margin-bottom: 30px; margin-top: 60px; width:60%;">Atributos</h4>
              
            <div class="row" >
                <div class="col-sm-6" style="padding-top:10px;">
                <div class="input-group">
                    <span  class="has-float-label">
                    <input id="dl_empresa" maxlength="5" type="text"  disabled="disabled" class=" limpiar form-control moderno_tb" placeholder=" "  />
                    <label for="dl_empresa">Codigo empresa</label>
                    </span>
                    <a class="disabled input-group-addon" data-toggle="modal" data-target="#modalEmpresa" onclick="ModalEmpresa();" style="background-color: #ffffff;border:0px">
                    
                    <i class="fa fa-search color-buscadores" aria-hidden="true"></i>
                    </a>  
                </div>
                </div>
                <div class="col-sm-6" style="padding-top:10px;">
                    <span  class="has-float-label">
                    <input type="text" id="tb_nomEmpresa" disabled="disabled" class=" limpiar form-control moderno_tb" placeholder=" " maxlength="40" >
                    <label for="tb_nomEmpresa">Nombre empresa</label>
                    </span>
                </div>
            </div>



           <table id="tblDetalle" class="table table-bordered table-striped" style="width:100%;display:none;">

                  <thead id="Thead1">
                    <tr>  
                      <th>Descripcion<br/></th>
                      <th>URL<br/></th>
                      <th>LiMenu<br/></th>
                      <th>UlMenu<br/></th>
                      <th>Orden<br/></th>
                      <th>Estado<br/></th>
                      <th>Nivel<br/></th>
                      <th>Id_menu<br/></th>
                    </tr>
                  </thead>
                  <tbody>
                   
                  </tbody>
                </table>
             
              <div class="modal" id="modalEmpresa" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" >
                    <div class="modal-dialog ">
                        <div class="modal-content" style="background-color:#ddd;">
                            <div class="modal-header"  style="margin: 10px;">
                                <h5 class="modal-title"  >Seleccione empresa</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body"  style="margin: 10px;">
                                <table id="table_Empresa" class="display"  style="width:100%;" >
                                  <colgroup>
                                    <col style="width:10%"></col>
                                    <col style="width:30%"></col>
                                    <col style="width:30%"></col>  
                                </colgroup>
                                    <thead id="thTablaCantidadUsuario">
                                        <tr>
                                         <th class="text-center" style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                                                  
                                            </th>
                                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                                                Codigo de Empresa
                                            </th>
                                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                                                Nombre de Empresa
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer"  style="margin: 10px;">
                                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="PasarCodEmpresa();">Seleccionar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


               <!-- Tabla secundaria usuarios asociados Visible --> 
     

             
              <!-- Tabla principal Visible --> 
            <div id="Lista" class="tab-pane tabcito" style="padding: 13px;">
                 <nav class="navbar navbar-default" style="margin-bottom: 0px;">
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
                    </div>
                </div>
            </nav>
                <table id="table_id" class="display" style="width:100%;" >
                 <colgroup>
                    <col style="width:10%"></col>
                    <col style="width:15%"></col>
                    <col style="width:30%"></col>
                    <col style="width:25%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col> 
                    <col style="width:10%"></col> 
                </colgroup>
                    <thead  id="thTablaVisible">
                        <tr> 
                            <th>
                                Código Usuario
                            </th>
                            <th>
                               Nombre Usuario
                            </th>
                            <th>
                                Dirección
                            </th>
                            <th>
                                Rol
                            </th>
                             <th>
                                Código Empresa
                            </th>
                            <th>
                                Fecha Crea
                            </th>
                            <th>
                                Estado
                            </th>
                        </tr>
                    </thead>
                
                     <tbody ondblclick="table_two_click(this);" onclick="table_one_click(this);" >

                    </tbody>
                </table>
             


            </div>

              
              <!-- Tabla para Exportar Principal-->
               <div id="tableExport" style="display:none;" > 
                <table id="table_principal" class="table table-bordered TablaIndex table-striped dataTable no-footer" border="2px" cellspacing="0" width="2000"   >
                <colgroup>
                      <col style="width:10%"></col>
                    <col style="width:15%"></col>
                    <col style="width:30%"></col>
                    <col style="width:25%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col> 
                    <col style="width:10%"></col> 
                </colgroup>
                    <thead>
                        <tr> 
                            <th>
                                Código Usuario
                            </th>
                            <th>
                               Nombre Usuario
                            </th>

                            <th>
                                Dirección
                            </th>
                            <th>
                                Rol
                            </th>
                             <th>
                                Código Empresa
                            </th>
                            <th>
                                Fecha Crea
                            </th>
                            <th>
                                Estado
                            </th>
                        </tr>
                    </thead>
                
                     <tbody>

                    </tbody>
                </table> 
                </div>

                 <!-- Tabla para Exportar Secuandaria Buscar Empresa-->
              <div id="tableExportarBuscarEmpresa" style="display:none;"> 
                <table id="table_secundariaBuscarEmpresa" class="table table-bordered TablaIndex table-striped dataTable no-footer" border="2px"  cellspacing="0" width="2000"  >
                <colgroup>
                    <col style="width:10%"></col>
                    <col style="width:30%"></col>
                    <col style="width:30%"></col>  
                </colgroup> 
                    <thead>
                        <tr> 
                        <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                            Codigo de Empresa
                        </th >
                        <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                            Nombre de Empresa
                        </th>
                        
                        </tr>
                    </thead>
                
                     <tbody>

                    </tbody>
                </table>
                </div>


                    <!-- Tabla para Exportar Secuandaria usuarios asociados-->
              <div id="tableExportarUsuariosAsociados" style="display:none;" > 
                <table id="table_secundariaUsuariosAsociados" class="table table-bordered TablaIndex table-striped dataTable no-footer" border="2px" cellspacing="0" width="2000"    >
                <colgroup>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col>
                    <col style="width:30%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col>
                    <col style="width:20%"></col> 
                    <col style="width:10%"></col> 
                </colgroup>
                    <thead>
                        <tr> 
                            <th>
                                Código
                            </th>
                            <th>
                               Usuario
                            </th>
                            <th>
                                Dirección
                            </th>
                            <th>
                                Rol
                            </th>
                             <th>
                                Celular
                            </th>
                            <th>
                                Email
                            </th>
                            <th>
                                Estado
                            </th>
                        </tr>
                    </thead>
                
                     <tbody>

                    </tbody>
                </table>
                 

                </div>
          </div> 
           
    </div>
