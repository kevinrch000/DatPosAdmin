Public Class BLTipoDocumento

    Dim objDA As New DA.DATipoDocumento()

    Public Function CargarTipoDocumento()
        Return objDA.CargarTipoDocumento()
    End Function

End Class
