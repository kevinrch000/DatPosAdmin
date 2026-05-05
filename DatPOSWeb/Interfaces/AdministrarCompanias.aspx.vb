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

    <System.Web.Services.WebMethod(EnableSession:=True)>
    Public Shared Function ConsultarEmpresa(codigo As String) As List(Of BE.BEEmpresa)

        Dim lstCom As New List(Of BE.BEEmpresa)
        Dim listDT As DataTable = New BL.BLEmpresa().CargarCompania(codigo)

        For Each fila As DataRow In listDT.Rows
            Dim objBE As New BE.BEEmpresa()

            ' Datos Generales
            objBE.id_empresa = If(IsDBNull(fila("id_empresa")), 0, fila("id_empresa"))
            objBE.ccod_empresa = If(IsDBNull(fila("ccod_empresa")), "", fila("ccod_empresa"))
            objBE.cdescripcion = If(IsDBNull(fila("cdsc_empresa")), "", fila("cdsc_empresa"))
            objBE.cnum_tribu = If(IsDBNull(fila("cnum_tribu")), "", fila("cnum_tribu"))
            objBE.cnombre_servidor = If(IsDBNull(fila("cnombre_servidor")), "", fila("cnombre_servidor"))
            objBE.cnombre_bd = If(IsDBNull(fila("cnombre_bd")), "", fila("cnombre_bd"))

            ' Moneda y Tarifas
            objBE.csimbolo_moneda = If(IsDBNull(fila("csimbolo_moneda")), "", fila("csimbolo_moneda"))
            objBE.cnombre_moneda = If(IsDBNull(fila("cnombre_moneda")), "", fila("cnombre_moneda"))
            objBE.ctarifas = If(IsDBNull(fila("ctarifas")), "", fila("ctarifas"))
            objBE.nusuario_extra = If(IsDBNull(fila("nusuario_extra")), 0, fila("nusuario_extra"))
            objBE.ntienda_extra = If(IsDBNull(fila("ntienda_extra")), 0, fila("ntienda_extra"))

            ' Ubicación
            objBE.cdepartamento = If(IsDBNull(fila("cdepartamento")), "", fila("cdepartamento"))
            objBE.cprovincia = If(IsDBNull(fila("cprovincia")), "", fila("cprovincia"))
            objBE.cdistrito = If(IsDBNull(fila("cdistrito")), "", fila("cdistrito"))
            objBE.curbanizacion = If(IsDBNull(fila("curbanizacion")), "", fila("curbanizacion"))
            objBE.cdomicilio = If(IsDBNull(fila("cdomicilio")), "", fila("cdomicilio"))
            objBE.cubigeo = If(IsDBNull(fila("cubigeo")), "", fila("cubigeo"))

            ' Facturación
            objBE.nenviosunat = If(IsDBNull(fila("nenviosunat")), "", fila("nenviosunat"))

            If IsDBNull(fila("dfch_sunat")) Then
                objBE.dfch_sunat = ""
            Else
                objBE.dfch_sunat = Convert.ToDateTime(fila("dfch_sunat")).ToString("dd/MM/yyyy")
            End If

            objBE.ccod_cliente_emis = If(IsDBNull(fila("ccod_cliente_emis")), "", fila("ccod_cliente_emis"))

            If IsDBNull(fila("dfch_vencimiento")) Then
                objBE.dfch_vencimiento = ""
            Else
                objBE.dfch_vencimiento = Convert.ToDateTime(fila("dfch_vencimiento")).ToString("dd/MM/yyyy")
            End If

            objBE.ctoken = If(IsDBNull(fila("ctoken")), "", fila("ctoken"))
            objBE.ctip_facturador = If(IsDBNull(fila("ctip_facturador")), "", fila("ctip_facturador"))

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