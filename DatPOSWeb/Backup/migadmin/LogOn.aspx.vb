
Public Class LogOn
    Inherits System.Web.UI.Page

    Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs) Handles Me.Load
        Session("MensajeError") = ""

    End Sub
   

    Public Sub btnlogin_click() Handles btnlogin.ServerClick
        Dim listDT As DataTable = New BL.BLUser().ValidarUsuario(UserName.Value, Password.Value)
        If (UserName.Value = "") Then
            Session("MensajeError") = "Ingresar Usuario"
        ElseIf (Password.Value = "") Then
            Session("MensajeError") = "Ingresar Contraseña"
        Else  
            If listDT IsNot Nothing AndAlso listDT.Rows.Count > 0 Then 
                Dim objBE As New BE.BEUser() 
                For Each fila In listDT.Rows 
                    objBE.id_usuario = fila.ItemArray(0)
                    objBE.ccod_usuario = fila.ItemArray(1)
                    objBE.cdsc_usuario = fila.ItemArray(2)
                    objBE.id_rol = fila.ItemArray(3)
                    objBE.ccod_empresa = fila.ItemArray(4)
                    objBE.cdsc_empresa = fila.ItemArray(5)
                    objBE.cnombre_bd = fila.ItemArray(6)
                    objBE.cnombre_servidor = fila.ItemArray(7) 
                Next fila 
                Session("objBEUser") = objBE
                Response.Redirect("/Interfaces/Home.aspx") 
            Else
                Session("MensajeError") = "Usuario o Contraseña incorrecta"
            End If
        End If 
    End Sub 
End Class