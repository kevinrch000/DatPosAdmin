<%@ Page Language="vb" AutoEventWireup="false" CodeBehind="LogOn.aspx.vb" Inherits="WebApplication1.LogOn" %>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
     <title>Portal de Administración - Datpos</title>
    <link rel="shotchut icon"  href="../Styles/img/icon/icon_LogoCircle.png" >
    <link href="../Styles/Site.css" rel="stylesheet" type="text/css" /> 
   
    <link href="../Styles/css/alertify.core.css" rel="stylesheet" type="text/css" />
    <link href="../Styles/css/alertify.default.css" rel="stylesheet" type="text/css" />
   
    <link rel="stylesheet" href="http://cssslider.com/sliders/demo-10/engine1/style.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script>
    <script type="text/javascript" src="http://cssslider.com/sliders/demo-10/engine1/gestures.js"></script>
  

    <script src="../Javascript/Comun.js" type="text/javascript"></script>
     

    <!-- Compiled and minified Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
<!-- Minified JS library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Compiled and minified Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
 



<script language="javascript" type="text/javascript">

</script>
</head>
<body>
    <form id="form1" runat="server">
    <!-- Second navbar for categories -->
    <nav id="navIndex" class="navbar navbar-default" style="background-color: Black;">
      <div class="container" style="width:80%;">
        <!-- Brand and toggle get grouped for better mobile display -->
       <div class="navbar-header">

          <a class="navbar-brand" href="http://www.microsig.com/" target="_blank" style="text-decoration:none;color:White;">
          <img src="/Styles/img/logoMicrosig.png">
          <span id="navBienvenida">
          </span></a>
        </div>

        
  <div class="collapse navbar-collapse" id="navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">


   <div class="dropdown">
  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">DATPOS
  <span class="caret"></span></button>
  <ul class="dropdown-menu">
    <li><a href="http://www.datpos.com/" target="_blank">DATPOS Website</a></li>
    <li><a href="https://34.213.72.183/migcliente/LogOn.aspx" target="_blank">Portal Cliente - DATPOS</a></li>
    <li><a data-toggle="modal" data-target="#myModal">Acerca de</a></li>
  </ul>
</div>

 
 
             
             
          </ul>
        </div>
 
      </div><!-- /.container -->
    </nav>
    

    <div class="container">
  <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalArticuloLabel" aria-hidden="true">
                    <div class="modal-dialog ">
                        <div class="modal-content">
                          
                            <div class="modal-body">
                              
                              <div id="main" style="height:70%;text-align:center;">
           <img src="../Styles/img/icon/icon_LogoCircle.png" style="width:50PX;display: block;margin: 0 auto;margin-top: 90px;">
           
            <p style="margin-top: 4%;">Portal de Administración - DATPOS</p> 
            <p>Versión: 1.20.1</p>
            <p>09/11/2020</p>
            <p>© Copyright 2020 - Todos los Derechos reservados DATPOS</p> 
            <p>Soporte TELF.  (511)  225-7622,   (511) 224-5241</p>
            <p>soporte@microsig.com</p>
            <p>info@microsig.com</p>
            
          
             <p style="margin-top: 46px;"><b>Advertencia:</b> Este programa está protegido por las leyes de derecho de autor y otros tratados internacionales. <br>
             La reproducción o distribución ilícita de este programa o de cualquier parte del mismo está penada por la Ley.</p>

        </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

         <div class="body1"  style="margin-left: -154px;margin-right: 4px;width: 697px;">
        
            <div id="myCarousel" class="carousel slide" data-ride="carousel" style="text-align: center;margin: 39px;">
            <!-- Indicators -->
            <ol class="carousel-indicators">
              <li data-target="#myCarousel" data-slide-to="0" class=""></li>
              <li data-target="#myCarousel" data-slide-to="1" class="active"></li>
              <li data-target="#myCarousel" data-slide-to="2" class=""></li>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner">
              <div class="item">
                <img src="../Styles/images/HOME1.jpg"  style="width:850px;height:400px;">
              </div>

              <div class="item active">
                <img src="../Styles/images/HOME2.jpg"   style="width:850px;height:400px;">
              </div>
            
              <div class="item">
                 <img src="../Styles/images/HOME3.jpg"    style="width:850px;height:400px;">
              </div>
            </div>

            <!-- Left and right controls -->
            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right"></span>
              <span class="sr-only">Next</span>
            </a>
        </div>  
           </div>
       
         
                 <div class="body2 col-md-6 login-border" style="margin-left:4px;margin-right:-354px;width: 657px; "   >
        <div  class="carousel slide" data-ride="carousel" style="margin: 99px;">
                <h2 style="text-align: center; ">
                    Portal de Administración</h2>
                <div class="login-img col-sm-10 col-sm-offset-1"  >
                    <div class="form-group">
                        <img src="../Styles/img/icon/icon_user.png" />
                        <label for="UserName" style="font-size: 18px;">
                            Usuario</label>
                        <input id="UserName"   class="input-log form-control" name="UserName" placeholder="Usuario"  
                            runat="server"/>
                    </div>
    

                    <div class="form-group">
                        <img src="../Styles/img/icon/icon_pass.png" style="margin-right: 39px; font-size: 18px;"/>
                        <label for="Password" style="font-size: 18px;">
                            Contraseña</label>
                        <input id="Password"  maxlength="50" class="input-log form-control" name="Password" placeholder="Contraseña" 
                        type="password" runat="server"   />
                      <button id="btnShowPassword" type="button"   onclick="mostrarContrasena()">
                      <span class="glyphicon glyphicon-eye-open" id="MostraContra" style="color:#228ac9;">
                      </span></button>

                       <div id="divMayus" style="visibility:hidden">Bloq Mayús está activada</div> 
                    </div>
                     
                    <div class="form-group" style="text-align: center;">
                        <span id="msgval" style="color: Red; "><%Response.Write(Session("MensajeError"))%></span><br />
                    </div>
                    <div class="form-group" style="text-align: center;">
                    					<div class="form-group">
						        <input id="btnlogin" type="submit" value="Ingresar"  class="btn btn-primary" onclick="validar()" runat="server">
					        </div>
                        
                    </div>
                </div>
                </div>

   </div>



    <div class="footer">
        <div class="container">
            <div class="media-container-row">
                <div class="col-md-8  col-md-offset-2" style="text-align: center;">
                    <h3 class="pb-3 mbr-fonts-style display-2">
                        SÍGUENOS!
                    </h3>
                    <div class="social-list">
                        <a href="https://twitter.com/microsiglat" class="" target="_blank" style="margin-right: 14px;">
                            <img src="../Styles/img/icon/icon_twitter.png" style="width: 29px;" />
                        </a><a href="https://www.facebook.com/MicrosigLat" target="_blank" class="" style="margin-right: 14px;">
                            <img src="../Styles/img/icon/icon_facebook.png" style="width: 21px;" />
                        </a><a href="https://www.linkedin.com/authwall?trk=bf&amp;trkInfo=AQHdjcfQNv82pwAAAV8rHtHoqa2BAeZJmdFdABJjPj8OaPVNilPuU2o9TWmg6hfZp_XLjqYAsU29hhgq4zb68sWfh_perKIaGBB3nlHh0_9cVib9zn9wiU9opU8kdvYOWTlulS8=&amp;originalReferer=&amp;sessionRedirect=https%3A%2F%2Fwww.linkedin.com%2Fcompany%2F11054167%2F"
                            target="_blank" class="" style="margin-right: 14px;">
                            <img src="../Styles/img/icon/icon_linkedin.png" style="width: 27px;" />
                        </a><a href="http://ow.ly/NZX150gZviJ" target="_blank" class="" style="margin-right: 14px;">
                            <img src="../Styles/img/icon/icon_videos.png" style="width: 34px;" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</body>
 
 <script type="text/javascript">



     document.addEventListener('keydown', function (event) {

         var mayus = event.getModifierState && event.getModifierState('CapsLock');
         document.getElementById("divMayus").style.visibility = "hidden";
         if (mayus) {
             document.getElementById("divMayus").style.visibility = "visible";
         }


     });

     function validar() {
         var Password = document.getElementById("Password");
         if (Password.value == "") {
             Mensaje('Advertencia', 'Ingrese Codigo de Empresa', 'warning');
             return false;
         }
     };

     function mostrarContrasena() {
         //         Mensaje('Advertencia', 'Ingrese Estado de Usuario', 'warning');

         var tipo = document.getElementById("Password");
         var tipo2 = document.getElementById("MostraContra");

         if (tipo.type == "password") {
             tipo2.className = "glyphicon glyphicon-eye-close";
             tipo.type = "text";
         } else {
             tipo2.className = "glyphicon glyphicon-eye-open";

             tipo.type = "password";
         }
     }
</script>

 
</html>
   