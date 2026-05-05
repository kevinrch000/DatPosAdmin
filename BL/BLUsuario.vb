Public Class BLUsuario
    Dim objDA As New DA.DAUsuario()

    Public Function ConsultarUs()
        Return objDA.ConsultarUsuarios()
    End Function
    Public Function CargarUsuario(cod As String)
        Return objDA.CargarUsuario(cod)
    End Function
    Public Function UsuariosAsociados(ccod_empresa As String)
        Return objDA.UsuariosAsociados(ccod_empresa)
    End Function
    Public Function InsertarUsuario(objBE As BE.BEUsuario, objConex As BE.BEUser)
        Return objDA.InsertarUsuario(objBE, objConex)
    End Function
    Public Function InsertarUsuarioAdmin(objBE As BE.BEUsuario)
        Return objDA.InsertarUsuarioAdmin(objBE)
    End Function
    Public Function EditarUsuarioAdmin(objBE As BE.BEUsuario)
        Return objDA.EditarUsuarioAdmin(objBE)
    End Function
    Public Function EditarUsuario(objBE As BE.BEUsuario, objConex As BE.BEUser)
        Return objDA.EditarUsuario(objBE, objConex)
    End Function
    Public Function EliminarUsuarioAdmin(cod As String, obj As BE.BEUser)
        Return objDA.EliminarUsuarioAdmin(cod, obj)
    End Function
    Public Function EliminarUsuario(usuario As String, ipServidor As String, nomServidor As String, obj As BE.BEUser)
        Return objDA.EliminarUsuario(usuario, ipServidor, nomServidor, obj)
    End Function

    ' --- NEW: Validate child BD exists before any operation ---
    Public Function ValidarBDEmpresa(ccod_empresa As String) As Object
        Return objDA.ValidarBDEmpresa(ccod_empresa)
    End Function

End Class