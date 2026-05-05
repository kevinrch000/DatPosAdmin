Public Class Home
    Inherits System.Web.UI.Page

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load

        If HttpContext.Current.Session("objBEUser") Is Nothing Then
            Response.Redirect("/migadmin/LogOn.aspx")
        End If

    End Sub


    <System.Web.Services.WebMethod()> _
    Public Shared Function CantidadEmpresas() As List(Of BE.Home)

        Dim lstCom As New List(Of BE.Home)
        Dim listDT As DataTable = New BL.BLHome().ConsultarUs()

        For Each fila In listDT.Rows
            Dim objBE As New BE.Home()
            objBE.cantidaTienda = fila.ItemArray(0)
            lstCom.Add(objBE)
        Next fila

        Return lstCom


    End Function

    <System.Web.Services.WebMethod()> _
    Public Shared Function CantidadUsuarios() As List(Of BE.Home)

        Dim lstCom As New List(Of BE.Home)
        Dim listDT As DataTable = New BL.BLHome().ConsultarUssuario()

        For Each fila In listDT.Rows
            Dim objBE As New BE.Home()
            objBE.cantidaUsuarios = fila.ItemArray(0)
            lstCom.Add(objBE)
        Next fila

        Return lstCom


    End Function
End Class