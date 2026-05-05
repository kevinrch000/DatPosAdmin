Imports System.Data.SqlClient

Public Class DAUser

    Dim objDAC As New DAConexionSQL()

    Public Function ValidarUsuario(usuario As String, clave As String)

        Dim cmd As New SqlCommand
        cmd.CommandType = CommandType.StoredProcedure
        cmd.Parameters.Add(New SqlParameter("@ccod_usuario", usuario))
        cmd.Parameters.Add(New SqlParameter("@cpassw", clave))
        cmd.CommandText = "webDatpos_validarUsuario"
        Return objDAC.selectstored(cmd)


    End Function
End Class
