Imports System.Data.SqlClient

Public Class DAConsultaEmpresas

    Dim objDAC As New DAConexionSQL()

    Public Function ConsultasEmpresasPrincipal(ccod_empresa As String, ctarifas As String, cpais_origen As String, cstatus As String)

        Dim cmd As New SqlCommand

        If ctarifas = "" Then ctarifas = "T"
        If cpais_origen = "" Then cpais_origen = "T"
        If cstatus = "" Then cstatus = "T"

        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", ccod_empresa))
        cmd.Parameters.Add(New SqlParameter("@ctarifas", ctarifas))
        cmd.Parameters.Add(New SqlParameter("@cpais_origen", cpais_origen))
        cmd.Parameters.Add(New SqlParameter("@cstatus", cstatus))

        cmd.CommandText = "webDatpos_buscarTarifa"

        Return objDAC.selectstored(cmd)

    End Function

End Class