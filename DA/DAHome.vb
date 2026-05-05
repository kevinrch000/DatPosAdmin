Imports System.Data.SqlClient


Public Class DAHome

    Dim objDAC As New DAConexionSQL()

    Public Function ConsultarUsuarios()

        Dim cmd As New SqlCommand
        cmd.CommandText = "webDatpos_contadorEmpresa"

        Return objDAC.selectstored(cmd)

    End Function

    Public Function ConsultarUssuario()

        Dim cmd As New SqlCommand
        cmd.CommandText = "webDatpos_contadorUsuario"

        Return objDAC.selectstored(cmd)

    End Function


End Class
