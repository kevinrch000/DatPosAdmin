Public Class BLConsultaEmpresas
    Dim objDA As New DA.DAConsultaEmpresas()


    Public Function ConsultasEmpresasPrincipal(ccod_empresa As String, ctarifas As String, cpais_origen As String, cstatus As String)
        Return objDA.ConsultasEmpresasPrincipal(ccod_empresa, ctarifas, cpais_origen, cstatus)
    End Function

End Class
