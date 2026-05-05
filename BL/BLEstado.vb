Public Class BLEstado

    Dim objDA As New DA.DAEstado()

    Public Function CargarEstados()
        Return objDA.CargarEstados()
    End Function
End Class
