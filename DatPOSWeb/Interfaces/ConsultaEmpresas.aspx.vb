Public Class ConsultaEmpresas
    Inherits System.Web.UI.Page

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load
        If HttpContext.Current.Session("objBEUser") Is Nothing Then
            Response.Redirect("/migadmin/LogOn.aspx")
        End If
    End Sub
    <System.Web.Services.WebMethod()> _
    Public Shared Function ConsultasEmpresasPrincipal(ccod_empresa As String, ctarifas As String, cpais_origen As String, cstatus As String) As List(Of BE.BEEmpresa)

        Dim lstComr As New List(Of BE.BEEmpresa)
        Dim listDTr As DataTable = New BL.BLConsultaEmpresas().ConsultasEmpresasPrincipal(ccod_empresa, ctarifas, cpais_origen, cstatus)

        For Each fila In listDTr.Rows
            Dim objBEr As New BE.BEEmpresa()
            objBEr.ccod_empresa = fila("ccod_empresa")   ' [0]
            objBEr.cdescripcion = fila("cdsc_empresa")   ' [1]
            objBEr.cdoc = fila("Documento")      ' [2] ← el SP sí la retorna aunque no esté en la tabla directa
            objBEr.cnombre_servidor = fila("cnombre_servidor") ' [3]
            objBEr.cnombre_bd = fila("cnombre_bd")     ' [4]
            objBEr.cpais_origen = fila("Pais")           ' [5]
            objBEr.ctarifas = fila("Tarifa")         ' [6]
            objBEr.cstatus = fila("id_estado")      ' [7]
            lstComr.Add(objBEr)
        Next fila

        Return lstComr

    End Function

    <System.Web.Services.WebMethod(EnableSession:=True)> _
    Public Shared Function ConsultaUsuariosPorEmpresa(empresa As String) As List(Of BE.BEEmpresa)

        Dim lstCom As New List(Of BE.BEEmpresa)
        Dim listDT As DataTable = New BL.BLConsultaUsuarios().ConsultaUsuariosPorEmpresa(empresa)

        For Each fila In listDT.Rows
            Dim objBEM As New BE.BEEmpresa()
            objBEM.ccod_empresa = fila.ItemArray(0)
            objBEM.cdescripcion = fila.ItemArray(1)
            objBEM.countUsuarios = fila.ItemArray(2)
            lstCom.Add(objBEM)
        Next fila
        Return lstCom
    End Function
End Class