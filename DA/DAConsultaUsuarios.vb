Imports System.Data.SqlClient

Public Class DAConsultaUsuarios

    Dim objDAC As New DAConexionSQL()

    Public Function ConsultasUsuariosPrincipal(codigo As String, estado As String)

        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", codigo))
        cmd.Parameters.Add(New SqlParameter("@cstatus", estado))
        cmd.CommandText = "webDatpos_consultaPorCodEmpresa"

        Return objDAC.selectstored(cmd)

    End Function
 

    Public Function ConsultaUsuariosPorEmpresa(empresa As String)

        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", empresa))
        cmd.CommandText = "webDatpos_countUsuariosPorEmpresa"

        Return objDAC.selectstored(cmd)

    End Function


End Class
