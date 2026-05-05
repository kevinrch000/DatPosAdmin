Public Class Site
    Inherits System.Web.UI.MasterPage

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load
        If Session("objBEUser") Is Nothing Then
            Response.Redirect("/migadmin/LogOn.aspx", False)
            Context.ApplicationInstance.CompleteRequest()
        Else
            id_empresa.InnerText = Session("objBEUser").cdsc_empresa.ToString()
            id_usuario.InnerText = Session("objBEUser").cdsc_usuario.ToString()
        End If

    End Sub

    Public Sub btnclick() Handles btnCerrarSesion.ServerClick

        Session.Clear()
        Response.Redirect("/migadmin/LogOn.aspx")
    End Sub

End Class