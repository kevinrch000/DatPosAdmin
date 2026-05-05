Public Class BLConsultaUsuarios
    Dim objCOF As New DA.DAConsultaUsuarios()

    Public Function ConsultasUsuariosPrincipal(codigo As String, estado As String)
        Return objCOF.ConsultasUsuariosPrincipal(codigo, estado)
    End Function


    Public Function ConsultaUsuariosPorEmpresa(empresa As String)
        Return objCOF.ConsultaUsuariosPorEmpresa(empresa)
    End Function

    
End Class
