Imports System.Data.SqlClient

Public Class DAEstado

    Dim objDAC As New DAConexionSQL()

    Public Function CargarEstados()

        Dim cmd As New SqlCommand
        cmd.CommandText = "sp_consultaestados"

        Return objDAC.selectstored(cmd)

    End Function
End Class
