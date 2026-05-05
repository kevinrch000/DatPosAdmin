Public Class ConsultaUsuarios
    Inherits System.Web.UI.Page
    Dim objBL As New BL.BLConsultaUsuarios()

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load

        If HttpContext.Current.Session("objBEUser") Is Nothing Then
            Response.Redirect("/migadmin/LogOn.aspx")
        End If
    End Sub
    <System.Web.Services.WebMethod()> _
    Public Shared Function ConsultasUsuariosPrincipal(codigo As String, estado As String) As List(Of BE.BEConsultaEmpresa)

        Dim lstCom As New List(Of BE.BEConsultaEmpresa)
        Dim listDT As DataTable = New BL.BLConsultaUsuarios().ConsultasUsuariosPrincipal(codigo, estado)

        For Each fila In listDT.Rows
            Dim objBE As New BE.BEConsultaEmpresa()
            objBE.ccod_empresa = fila.ItemArray(0)
            objBE.cdsc_empresa = fila.ItemArray(1)
            objBE.ccod_usuario = fila.ItemArray(2)
            objBE.cdsc_usuario = fila.ItemArray(3)
            objBE.cdir_usuario = fila.ItemArray(4)
            objBE.cdsc_rol = fila.ItemArray(5)
            objBE.cpais_origen = fila.ItemArray(6)
            objBE.cstatus = fila.ItemArray(7)
            objBE.ccelular = fila.ItemArray(8)
            lstCom.Add(objBE)
        Next fila
        Return lstCom
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