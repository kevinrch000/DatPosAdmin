Public Class BLRol

    Dim objDA As New DA.DARol()

    Public Function CargarRoles()
        Return objDA.CargarRoles()
    End Function

End Class
