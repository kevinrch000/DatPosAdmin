Public Class BLUser


    Dim objeDA As New DA.DAUser()

    Function ValidarUsuario(usuario As String, clave As String)
        Return objeDA.ValidarUsuario(usuario, clave)
    End Function


End Class
