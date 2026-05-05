Imports System.Data.SqlClient

Public Class DARol

    Dim objDAC As New DAConexionSQL()

    Public Function CargarRoles()

        Dim cmd As New SqlCommand
        cmd.CommandText = "sp_consultarroles"

        Return objDAC.selectstored(cmd)

    End Function
End Class
