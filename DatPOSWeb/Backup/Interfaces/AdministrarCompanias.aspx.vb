Public Class AdministrarCompanias
    Inherits System.Web.UI.Page

    Dim objBL As New BL.BLEmpresa()
    'Dim objBLTipoDocumento As New BL.BLTipoDocumento()
    Dim resp As Boolean

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load

        If HttpContext.Current.Session("objBEUser") Is Nothing Then
            Response.Redirect("/migadmin/LogOn.aspx")
            'Else
            '    'CargarTipoDocumento()
        End If
    End Sub

     

    <System.Web.Services.WebMethod()> _
    Public Shared Function CargarDepartamento(ccod_cia As String)
        Dim lstCom As New List(Of BE.BEUbigeo)
        Dim listDT As DataTable = New BL.BLEmpresa().CargarDepartamento(HttpContext.Current.Session("objBEUsuario"))
        For Each fila In listDT.Rows
            Dim objBEc As New BE.BEUbigeo()
            objBEc.id = fila.ItemArray(0)
            objBEc.name = fila.ItemArray(1)
            lstCom.Add(objBEc)
        Next fila
        Return lstCom
    End Function

    <System.Web.Services.WebMethod()> _
    Public Shared Function CargarProvincia(id_departamento As String)
        Dim lstCom As New List(Of BE.BEUbigeo)
        Dim listDT As DataTable = New BL.BLEmpresa().CargarProvincia(id_departamento)
        For Each fila In listDT.Rows
            Dim objBEc As New BE.BEUbigeo()
            objBEc.id = fila.ItemArray(0)
            objBEc.name = fila.ItemArray(1)
            lstCom.Add(objBEc)
        Next fila
        Return lstCom
    End Function

    <System.Web.Services.WebMethod()> _
    Public Shared Function CargarDistrito(id_provincia As String)
        Dim lstCom As New List(Of BE.BEUbigeo)
        Dim listDT As DataTable = New BL.BLEmpresa().CargarDistrito(id_provincia)
        For Each fila In listDT.Rows
            Dim objBEc As New BE.BEUbigeo()
            objBEc.id = fila.ItemArray(0)
            objBEc.name = fila.ItemArray(1)
            lstCom.Add(objBEc)
        Next fila
        Return lstCom
    End Function

    'Public Sub CargarTipoDocumento()

    '    ddl_td.DataSource = objBLTipoDocumento.CargarTipoDocumento()
    '    ddl_td.DataTextField = "cdescripcion"
    '    ddl_td.DataValueField = "id_tipodocumento"
    '    ddl_td.DataBind()
    'End Sub

    <System.Web.Services.WebMethod()> _
    Public Shared Function ConsultarEmpresas() As List(Of BE.BEEmpresa)

        Dim lstCom As New List(Of BE.BEEmpresa)
        Dim listDT As DataTable = New BL.BLEmpresa().CargarCompanias()

        For Each fila In listDT.Rows
            Dim objBE As New BE.BEEmpresa()
            objBE.id_empresa = fila.ItemArray(0)
            objBE.ccod_empresa = fila.ItemArray(1)
            objBE.cdescripcion = fila.ItemArray(2)
            objBE.cnum_tribu = fila.ItemArray(3)
            objBE.cnombre_servidor = fila.ItemArray(4)
            objBE.cnombre_bd = fila.ItemArray(5)
            objBE.csimbolo_moneda = fila.ItemArray(6)
            objBE.cnombre_moneda = fila.ItemArray(7)
            objBE.ctarifas = fila.ItemArray(8)
            objBE.nusuario_extra = fila.ItemArray(9)
            objBE.ntienda_extra = fila.ItemArray(10)
            objBE.dfch_crea = fila.ItemArray(11)
            lstCom.Add(objBE)
        Next fila

        Return lstCom

    End Function

    '<System.Web.Services.WebMethod(EnableSession:=True)> _
    'Public Shared Function CargarTarifa(codigo As String)

    '    Dim lstCom As New List(Of BE.BEEmpresa)
    '    Dim listDT As DataTable = New BL.BLEmpresa().CargarTarifa()

    '    For Each fila In listDT.Rows
    '        Dim objBE As New BE.BEEmpresa()
    '        objBE.cdescripcion = fila.ItemArray(0) 
    '        lstCom.Add(objBE)
    '    Next fila

    '    Return lstCom

    'End Function

    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function ConsultarEmpresa(codigo As String)

        Dim lstCom As New List(Of BE.BEEmpresa)
        Dim listDT As DataTable = New BL.BLEmpresa().CargarCompania(codigo)

        For Each fila In listDT.Rows
            Dim objBE As New BE.BEEmpresa()
            objBE.id_empresa = fila.ItemArray(0)
            objBE.ccod_empresa = fila.ItemArray(1)
            objBE.cdescripcion = fila.ItemArray(2)
            objBE.cnum_tribu = fila.ItemArray(3)
            objBE.cnombre_servidor = fila.ItemArray(4)
            objBE.cnombre_bd = fila.ItemArray(5)

            'objBE.cpais_origen = fila.ItemArray(6)
            objBE.csimbolo_moneda = fila.ItemArray(6)
            'objBE.cid_tributario = fila.ItemArray(8)
            objBE.cnombre_moneda = fila.ItemArray(7)
            objBE.ctarifas = fila.ItemArray(8)

            objBE.nusuario_extra = fila.ItemArray(9)
            objBE.ntienda_extra = fila.ItemArray(10)
            objBE.cdomicilio = fila.ItemArray(11)
            objBE.curbanizacion = fila.ItemArray(12)
            objBE.cprovincia = fila.ItemArray(13)

            objBE.cdistrito = fila.ItemArray(14)
            objBE.cdepartamento = fila.ItemArray(15)
            objBE.cubigeo = fila.ItemArray(16)
            'objBE.cdsc_facturador = fila.ItemArray(19)
            'objBE.ctip_facturador = fila.ItemArray(20)
            objBE.nenviosunat = fila.ItemArray(17)
            objBE.dfch_sunat = fila.ItemArray(18)
            objBE.ccod_cliente_emis = fila.ItemArray(19)
            If fila.ItemArray(20) IsNot DBNull.Value Then objBE.dfch_vencimiento = fila.ItemArray(20) Else objBE.dfch_vencimiento = ""
            objBE.ctoken = fila.ItemArray(21)
            objBE.ctip_facturador = fila.ItemArray(22)
            lstCom.Add(objBE)
        Next fila

        Return lstCom

    End Function

   


    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function GrabarEmpresa(empresa As List(Of BE.BEEmpresa), operacion As String)

        
        Dim resp As Boolean
        

        Dim objBL As New BL.BLEmpresa()

        If operacion = "nuevo" Then
     
            resp = objBL.InsertarCompania(empresa(0))

        End If

        If operacion = "editar" Then

            resp = objBL.EditarCompania(empresa(0))

        End If

        Return resp


    End Function

    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function EliminarE(elimrempresa As String)

        Dim objBL As New BL.BLEmpresa()
        Dim resp As Boolean

        resp = objBL.EliminarEmpresa(elimrempresa)

        Return resp

    End Function




End Class