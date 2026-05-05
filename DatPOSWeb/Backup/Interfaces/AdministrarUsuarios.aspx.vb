Public Class AdministrarUsuarios
    Inherits System.Web.UI.Page

    Dim objBL As New BL.BLUsuario()
    Dim objBLEmpresa As New BL.BLEmpresa()
    Dim objBLRol As New BL.BLRol()
    Dim objBLEstado As New BL.BLEstado()
    Dim resp As Boolean

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load
        If HttpContext.Current.Session("objBEUser") Is Nothing Then
            Response.Redirect("/migadmin/LogOn.aspx")
        Else
            'CargarRoles() 
        End If
        


    End Sub

    '<System.Web.Services.WebMethod()> _
    'Public Shared Function MenuAccesos() As List(Of BE.BEUsuario)

    '    Dim lstComm As New List(Of BE.BEUsuario)
    '    Dim listDTm As DataTable = New BL.BLUsuario().UsuariosAsociados()

    '    For Each fila In listDTm.Rows
    '        Dim objBEm As New BE.BEUsuario()
    '        objBEm.ccod_usuario = fila.ItemArray(0)
    '        objBEm.cdsc_usuario = fila.ItemArray(1)
    '        objBEm.cdirec = fila.ItemArray(2)
    '        objBEm.cdsc_rol = fila.ItemArray(3)
    '        objBEm.ccelular = fila.ItemArray(4)
    '        objBEm.cmail = fila.ItemArray(5)
    '        objBEm.cstatus = fila.ItemArray(6)
    '        lstComm.Add(objBEm)
    '    Next fila
    '    Return lstComm
    'End Function


    'Public Sub CargarRoles()
    '    dl_rol.DataSource = objBLRol.CargarRoles()
    '    dl_rol.DataTextField = "cdescripcion"
    '    dl_rol.DataValueField = "id_rol"
    '    dl_rol.DataBind()
    'End Sub

    
    <System.Web.Services.WebMethod()> _
    Public Shared Function UsuariosAsociados(ccod_empresa As String) As List(Of BE.BEUsuario)

        Dim lstComm As New List(Of BE.BEUsuario)
        Dim listDTm As DataTable = New BL.BLUsuario().UsuariosAsociados(ccod_empresa)

        For Each fila In listDTm.Rows
            Dim objBEm As New BE.BEUsuario()
            objBEm.ccod_usuario = fila.ItemArray(0)
            objBEm.cdsc_usuario = fila.ItemArray(1)
            objBEm.cdirec = fila.ItemArray(2)
            If fila.ItemArray(3) Is DBNull.Value Then objBEm.cdsc_rol = "" Else objBEm.cdsc_rol = fila.ItemArray(3)
            If fila.ItemArray(4) Is DBNull.Value Then objBEm.ccelular = "" Else objBEm.ccelular = fila.ItemArray(4)
            If fila.ItemArray(5) Is DBNull.Value Then objBEm.cmail = "" Else objBEm.cmail = fila.ItemArray(5)
            objBEm.cstatus = fila.ItemArray(6)
             
            lstComm.Add(objBEm)
        Next fila
        Return lstComm
    End Function



    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function TablaEmpresas() As List(Of BE.BEEmpresa)

        Dim lstCom As New List(Of BE.BEEmpresa)
        Dim listDT As DataTable = New BL.BLEmpresa().CargarCompanias()

        For Each fila In listDT.Rows
            Dim objBEM As New BE.BEEmpresa()
            objBEM.ccod_empresa = fila.ItemArray(1)
            objBEM.cdescripcion = fila.ItemArray(2)
            objBEM.cnombre_servidor = fila.ItemArray(4)
            objBEM.cnombre_bd = fila.ItemArray(5)
            lstCom.Add(objBEM)
        Next fila

        Return lstCom


    End Function

    <System.Web.Services.WebMethod()> _
    Public Shared Function ConsultarUsuarios() As List(Of BE.BEUsuario)

        Dim lstCom As New List(Of BE.BEUsuario)
        Dim listDT As DataTable = New BL.BLUsuario().ConsultarUs()

        For Each fila In listDT.Rows
            Dim objBE As New BE.BEUsuario()
            objBE.id_usuario = fila.ItemArray(0)
            objBE.ccod_usuario = fila.ItemArray(1)
            objBE.cdsc_usuario = fila.ItemArray(2)
            objBE.cpassw = fila.ItemArray(3)
            objBE.cdirec = fila.ItemArray(4)
            objBE.id_rol = fila.ItemArray(5)
            'objBE.cdsc_rol = fila.ItemArray(6)
            objBE.ccod_empresa = fila.ItemArray(6)
            'objBE.empresa = fila.ItemArray(7)
            'objBE.id_estado = fila.ItemArray(9)
            objBE.cstatus = fila.ItemArray(7)
            objBE.dfch_crea = fila.ItemArray(8)
            'objBE.cmail = fila.ItemArray(11)
            'objBE.ctelf = fila.ItemArray(12)
            'objBE.ccelular = fila.ItemArray(13)


            lstCom.Add(objBE)
        Next fila

        Return lstCom
         
    
    End Function

   
    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function ConsultarUsuario(codigo As String)

        Dim lstCom As New List(Of BE.BEUsuario)
        Dim listDT As DataTable = New BL.BLUsuario().CargarUsuario(codigo)

        For Each fila In listDT.Rows
            Dim objBE As New BE.BEUsuario()
            objBE.id_usuario = fila.ItemArray(0)
            objBE.ccod_usuario = fila.ItemArray(1)
            objBE.cdsc_usuario = fila.ItemArray(2)
            objBE.cpassw = fila.ItemArray(3)
            objBE.cdirec = fila.ItemArray(4)
            objBE.id_rol = fila.ItemArray(5)
            objBE.ccod_empresa = fila.ItemArray(6)
            objBE.cstatus = fila.ItemArray(7)
            objBE.dfch_crea = fila.ItemArray(8) 
            objBE.cmail = fila.ItemArray(9)
            objBE.ctelf = fila.ItemArray(10)
            objBE.ccelular = fila.ItemArray(11)
            objBE.empresa = fila.ItemArray(12)

            lstCom.Add(objBE)
        Next fila

        Return lstCom

    End Function

    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function GrabarUsuario(usuario As List(Of BE.BEUsuario), operacion As String)

        If HttpContext.Current.Session("objBEUser") Is Nothing Then
            Return "-1"
        Else
            Dim objBL As New BL.BLUsuario()
            Dim objreturn As Object
            If operacion = "nuevo" Then
                If (objBL.InsertarUsuarioAdmin(usuario(0))) Then
                    objreturn = objBL.InsertarUsuario(usuario(0), HttpContext.Current.Session("objBEUser"))

                End If
            End If

            If operacion = "editar" Then
                If (objBL.EditarUsuarioAdmin(usuario(0))) Then
                    objreturn = objBL.EditarUsuario(usuario(0), HttpContext.Current.Session("objBEUser"))

                End If
            End If
            Return objreturn
        End If


    End Function

    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function Eliminar(usuario As String, ipServidor As String, nomServidor As String)



        Dim resp As Boolean

        Dim objBL As New BL.BLUsuario()

        If (objBL.EliminarUsuarioAdmin(usuario, HttpContext.Current.Session("objBEUser"))) Then
            resp = objBL.EliminarUsuario(usuario, ipServidor, nomServidor, HttpContext.Current.Session("objBEUser"))
        Else
            resp = False
        End If

        Return resp

    End Function

    '<System.Web.Services.WebMethod(EnableSession:=True)> _
    'Public Shared Function EliminarU(elimrusuario As String)

    '    Dim objBL As New BL.BLUsuario()
    '    Dim resp As Boolean

    '    resp = objBL.EliminarUsuario(elimrusuario)

    '    Return resp

    'End Function

End Class