 

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script>
<link href="<?= $base ?>/assets/Styles/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"/>
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
<script src="<?= $base ?>/assets/Javascript/ConsultaEmpresas.js" type="text/javascript"></script>

      
    <link href="<?= $base ?>/assets/Styles/bootstrap-float-label.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/disenoBotones.css" rel="stylesheet" type="text/css" />
  
<input id="operacion" type="hidden"/>
<input id="hdd_ultimafila" type="hidden"/>
<input id="hdd_fila" type="hidden" value="0"/>
<input id="hdd_numeromenus" type="hidden" value="1"/>
<input id="hdd_numerofilas" type="hidden"/> 

    <div class="c-content-center" style="padding-top:40px;"  > 
        <div class="tab-content"> 
        <!-- DATOS -->
            <div id="Datos" class="tab-pane in active "  >
           
        <!-- Buscadores --> 
    


         <div class="row"  >
            <div class="col-sm-4" style="padding-top:10px;"   > 
                <div class="input-group"> 
                    <span  class="has-float-label"   >
                    <input id="txtCodEmp" maxlength="5" class="limpiar form-control moderno_tb"   type="text" placeholder=" "/>
                    <label for="txtCodEmp">Codigo de Empresa</label>
                    </span>
                    <a id="A1" class="disabled input-group-addon" data-toggle="modal" data-target="#modalCantudadUsuario" onclick="ModalUsuariosdeEmpresa();" style="border:0px;background-color: #ffffff;">
                  
               <i class="fa fa-search color-buscadores" aria-hidden="true"></i>
                   </a>
                </div>
            </div>
            <div class="col-sm-4" style="padding-top:10px;">
                  <span  class="has-float-label"   >
                    <input id="txtPais" maxlength="50" class="limpiar form-control moderno_tb" type="text" placeholder=" "/>
                    <label for="txtPais">Nombre de País</label>
                  </span>
            </div>
         </div> 
        <div class="row" style="padding-bottom:30px;"  >
            <div class="col-sm-4" style="padding-top:10px;"> 
                 <div class="floating-label">
                      <select class="limpiar form-control moderno_tb floating-select" id="txtTarifa"   onclick="this.setAttribute('value', this.value);" value="">
                            <option value=""></option>
                            <option value="T">Todos</option>
                            <option value="Express">Express</option>
                            <option value="Estandar">Estándar</option>
                            <option value="Premium">Premium</option>
                       </select>
                       <label class="floating-select2">Tipo de Tarifa</label>
                   </div>
            </div>
            <div class="col-sm-4" style="padding-top:10px;"> 
                 <div class="floating-label"> 
                      <select class="limpiar form-control moderno_tb floating-select" id="txtStatus" style="width:100%;" onclick="this.setAttribute('value', this.value);" value="">
                            <option value=""></option>
                            <option value="T">Todos</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                       </select>
                       <label class="floating-select2">Estado</label>
                   </div>
               </div>
           </div> 
          
            <!-- . --> 

             <!-- Modal Visible -->
          <div class="modal" id="modalCantudadUsuario" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"   >
                    <div class="modal-dialog">
                        <div class="modal-content" style="background-color:#ddd;" >
                            <div class="modal-header" style="margin: 10px;" >
                                <h5 class="modal-title">Seleccione empresa</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div> 
                            <div class="modal-body" style="margin: 10px;"  >
                                <table  id="table_visible_BuscarEmpresa" class="display" style="width:100%;"  >
                                 <colgroup>
                                    <col style="width:10%"></col>
                                    <col style="width:30%"></col>
                                    <col style="width:30%"></col> 
                                    <col style="width:30%"></col>
                                </colgroup>
                                    <thead id="thTablaCantidadUsuario" >
                                        <tr>
                                         <th class="text-center" style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                                                  
                                            </th>
                                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                                                Codigo de Empresa
                                            </th >
                                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                                                Nombre de Empresa
                                            </th>
                                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                                                Cantidad de Usuarios
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer" style="margin: 10px;" >
                                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="PasaDatosCodEmpresa();">Seleccionar</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- . -->    

            <!-- Tabla para Visible -->
                <table id="table_visible" class="display" style="width:100%;" >
                        <colgroup>
                                    <col style="width:10%"></col>
                                    <col style="width:20%"></col>
                                    <col style="width:10%"></col>
                                    <col style="width:20%"></col>
                                    <col style="width:10%"></col>
                                    <col style="width:10%"></col>
                                    <col style="width:10%"></col>
                                    <col style="width:10%"></col>
                                </colgroup>
                    <thead id="thTablaVisible">
                        <tr>
                            
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Codigo Empresa
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Nombre Empresa
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Documento
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Nombre Servidor
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Nombre BD
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Pais
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Tarifa
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Estado
                            </th>
                        </tr>
                    </thead>
                
                     <tbody>

                    </tbody>
                </table>


              <!-- Tabla para Exportar Secuandaria Buscar Empresa-->
              <div id="tableExportarBuscarEmpresa" style="display:none;"> 
                <table id="table_secundariaBuscarEmpresa" class="table table-bordered TablaIndex table-striped dataTable no-footer" border="2px" cellspacing="0" width="2000"     >
                <colgroup>
                    <col style="width:15%"></col>
                    <col style="width:40%"></col>
                    <col style="width:15%"></col> 
                </colgroup> 
                    <thead>
                        <tr> 
                        <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                            Codigo de Empresa
                        </th >
                        <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                            Nombre de Empresa
                        </th>
                        <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color:rgb(33, 182, 215);color: White;">
                            Cantidad de Usuarios
                        </th>
                        </tr>
                    </thead>
                
                     <tbody>

                    </tbody>
                </table>
                 

                </div>

            <!-- Tabla para Exportar Principal-->
               <div id="tableExport" style="display:none;" > 
                <table id="table_principal" class="table table-bordered TablaIndex table-striped dataTable no-footer" border="2px"  cellspacing="0" width="2000"    >
                <colgroup>
                    <col style="width:10%"></col>
                    <col style="width:20%"></col>
                    <col style="width:10%"></col>
                    <col style="width:20%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col>
                    <col style="width:10%"></col>
                </colgroup>
                    <thead>
                        <tr>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Codigo Empresa
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Nombre Empresa
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Documento
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Nombre Servidor
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Nombre BD
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Pais
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
                                Tarifa
                            </th>
                            <th style="padding: 6px 5px;text-align: left; border: solid 1px #e8eef4; background-color: #999;">
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
    </div>
