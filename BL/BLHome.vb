Public Class BLHome

    Dim objDA As New DA.DAHome()

    Public Function ConsultarUs()
        Return objDA.ConsultarUsuarios()
    End Function

    Public Function ConsultarUssuario()
        Return objDA.ConsultarUssuario()
    End Function


End Class
