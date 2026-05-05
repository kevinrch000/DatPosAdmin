Imports System.Data.SqlClient

Public Class DATipoDocumento

    Dim objDAC As New DAConexionSQL()

    Public Function CargarTipoDocumento()

        Dim cmd As New SqlCommand
        cmd.CommandText = "sp_consultatipodocumento"

        Return objDAC.selectstored(cmd)

    End Function
End Class
