Imports System.Data.SqlClient
Imports System.Configuration

Public Class DAUsuario

    Dim objDAC As New DAConexionSQL()

    Public Function CargarUsuario(cod As String)
        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_usuario", cod))
        cmd.CommandText = "webDatpos_consultaUsuario"
        Return objDAC.selectstored(cmd)
    End Function

    Public Function UsuariosAsociados(ccod_empresa As String)
        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", ccod_empresa))
        cmd.CommandText = "webDatpos_usuariosAsociados"
        Return objDAC.selectstored(cmd)
    End Function

    Public Function ConsultarUsuarios()
        Dim cmd As New SqlCommand
        cmd.CommandText = "webDatpos_consultausuarios"
        Return objDAC.selectstored(cmd)
    End Function

    Public Function InsertarUsuarioAdmin(objBE As BE.BEUsuario)
        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_usuario", objBE.ccod_usuario))
        cmd.Parameters.Add(New SqlParameter("@cdsc_usuario", objBE.cdsc_usuario))
        cmd.Parameters.Add(New SqlParameter("@cpassw", objBE.cpassw))
        cmd.Parameters.Add(New SqlParameter("@cdirec", objBE.cdirec))
        cmd.Parameters.Add(New SqlParameter("@id_rol", objBE.id_rol))
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", objBE.ccod_empresa))
        cmd.Parameters.Add(New SqlParameter("@cstatus", objBE.cstatus))
        cmd.Parameters.Add(New SqlParameter("@cmail", objBE.cmail))
        cmd.Parameters.Add(New SqlParameter("@ctelf", objBE.ctelf))
        cmd.Parameters.Add(New SqlParameter("@ccelular", objBE.ccelular))
        cmd.CommandText = "webDatpos_insertarUsuarioAdmin"
        Return objDAC.executestored(cmd)
    End Function

    Public Function InsertarUsuario(objBE As BE.BEUsuario, objConex As BE.BEUser)

        Dim id_rol As String
        Dim objreturn As Object = {False, "", "", ""}

        ' --- INICIO DEL CÓDIGO A PRUEBA DE BALAS ---
        ' 1. Get master connection string from Web.config
        Dim masterConn As String = ConfigurationManager.ConnectionStrings("cadenaconexion").ConnectionString

        ' 2. Query real cnombre_bd and cnombre_servidor from DatPosAdmin instead of trusting frontend input
        Dim nombreBdHija As String = ""
        Dim nombreServidor As String = ""

        Using connMaster As New SqlConnection(masterConn)
            connMaster.Open()
            Dim cmdEmp As New SqlCommand("SELECT cnombre_bd, cnombre_servidor FROM Empresas WHERE ccod_empresa = @cod AND id_estado = 1", connMaster)
            cmdEmp.Parameters.Add(New SqlParameter("@cod", objBE.ccod_empresa))
            Dim reader As SqlDataReader = cmdEmp.ExecuteReader()
            If reader.Read() Then
                nombreBdHija = reader("cnombre_bd").ToString().Trim()
                nombreServidor = reader("cnombre_servidor").ToString().Trim()
            End If
            reader.Close()
        End Using

        ' 3. Validate that the company has a BD configured
        If String.IsNullOrEmpty(nombreBdHija) Then
            objreturn(1) = "ERROR"
            objreturn(2) = "La empresa '" & objBE.ccod_empresa & "' no tiene una base de datos configurada."
            Return objreturn
        End If

        ' 4. Validate that the child BD actually exists in the server before connecting
        Using connVerif As New SqlConnection(masterConn)
            connVerif.Open()
            Dim cmdVerif As New SqlCommand("SELECT COUNT(*) FROM sys.databases WHERE name = @nombre", connVerif)
            cmdVerif.Parameters.Add(New SqlParameter("@nombre", nombreBdHija))
            Dim existe As Integer = CInt(cmdVerif.ExecuteScalar())
            If existe = 0 Then
                objreturn(1) = "ERROR"
                objreturn(2) = "La base de datos '" & nombreBdHija & "' no existe en el servidor. Contacte al administrador."
                Return objreturn
            End If
        End Using

        ' 5. Build safe connection string using verified data from DB (not from frontend)
        Dim builder As New SqlConnectionStringBuilder(masterConn)
        If Not String.IsNullOrEmpty(nombreServidor) Then
            builder.DataSource = nombreServidor
        End If
        builder.InitialCatalog = nombreBdHija

        Dim conexionSegura As String = builder.ConnectionString
        ' --- FIN DEL CÓDIGO A PRUEBA DE BALAS ---

        Using connection1 As New SqlConnection(conexionSegura)

            connection1.Open() 'abrimos conexion
            Dim cmd As SqlCommand = New SqlCommand 'creamos el comando y asignamos parametros
            cmd.CommandType = CommandType.StoredProcedure
            cmd.CommandText = "webDatpos_insertarUsuario"
            cmd.Parameters.Add(New SqlParameter("@ccod_cia", objBE.ccod_empresa))
            cmd.Parameters.Add(New SqlParameter("@usu_crea", objConex.ccod_usuario))
            cmd.Parameters.Add(New SqlParameter("@ccod_usuario", objBE.ccod_usuario))
            cmd.Parameters.Add(New SqlParameter("@cdirc_usuario", objBE.cdirec))
            cmd.Parameters.Add(New SqlParameter("@cdsc_usuario", objBE.cdsc_usuario))
            cmd.Parameters.Add(New SqlParameter("@cpassw", objBE.cpassw))
            cmd.Parameters.Add(New SqlParameter("@rol", objBE.id_rol))
            cmd.Parameters.Add(New SqlParameter("@cstatus", objBE.cstatus))
            cmd.Parameters.Add(New SqlParameter("@cmail", objBE.cmail))
            cmd.Parameters.Add(New SqlParameter("@ctelf", objBE.ctelf))
            cmd.Parameters.Add(New SqlParameter("@ccelular", objBE.ccelular))
            cmd.Parameters.Add(New SqlParameter("@ErrorNumber", SqlDbType.NVarChar, 25)).Direction = ParameterDirection.Output
            cmd.Parameters.Add(New SqlParameter("@ErrorMessage", SqlDbType.NVarChar, 200)).Direction = ParameterDirection.Output
            cmd.Parameters.Add(New SqlParameter("@id_rol", SqlDbType.NVarChar, 25)).Direction = ParameterDirection.Output
            cmd.Connection = connection1 'enlazamos el comando con la conexion
            cmd.ExecuteNonQuery()

            objreturn(1) = cmd.Parameters("@ErrorNumber").Value
            objreturn(2) = cmd.Parameters("@ErrorMessage").Value
            id_rol = cmd.Parameters("@id_rol").Value

            'If objreturn(1) = "OK" Then
            '    For c = 0 To menu.Count - 1
            '        Dim cmd3 As SqlCommand = New SqlCommand 'creamos el comando y asignamos parametros
            '        cmd3.CommandType = CommandType.StoredProcedure
            '        cmd3.CommandText = "webDatpos_insertarAcceso"
            '        cmd3.Connection = connection1 'enlazamos el comando con la conexion 
            '        cmd3.Parameters.Add(New SqlParameter("@ccod_cia", objBE.ccod_empresa))
            '        cmd3.Parameters.Add(New SqlParameter("@id_rol", id_rol))
            '        cmd3.Parameters.Add(New SqlParameter("@cstatus", "1"))
            '        cmd3.Parameters.Add(New SqlParameter("@corden", menu(c).corden))
            '        cmd3.Parameters.Add(New SqlParameter("@ccod_usuario", objConex.ccod_usuario))
            '        cmd3.ExecuteNonQuery()
            '    Next
            'End If

            objreturn(0) = True

        End Using

        Return objreturn

    End Function

    Public Function EditarUsuarioAdmin(objBE As BE.BEUsuario)
        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_usuario", objBE.ccod_usuario))
        cmd.Parameters.Add(New SqlParameter("@cdsc_usuario", objBE.cdsc_usuario))
        cmd.Parameters.Add(New SqlParameter("@cpassw", objBE.cpassw))
        cmd.Parameters.Add(New SqlParameter("@cdirec", objBE.cdirec))
        cmd.Parameters.Add(New SqlParameter("@id_rol", objBE.id_rol))
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", objBE.ccod_empresa))
        cmd.Parameters.Add(New SqlParameter("@cstatus", objBE.cstatus))
        cmd.Parameters.Add(New SqlParameter("@cmail", objBE.cmail))
        cmd.Parameters.Add(New SqlParameter("@ctelf", objBE.ctelf))
        cmd.Parameters.Add(New SqlParameter("@ccelular", objBE.ccelular))
        cmd.CommandText = "webDatpos_editarUsuarioAdmin"
        Return objDAC.executestored(cmd)
    End Function

    Public Function EditarUsuario(objBE As BE.BEUsuario, objConex As BE.BEUser)

        Dim objreturn As Object = {False, "", "", ""}

        ' --- INICIO DEL CÓDIGO A PRUEBA DE BALAS ---
        ' 1. Get master connection string from Web.config
        Dim masterConn As String = ConfigurationManager.ConnectionStrings("cadenaconexion").ConnectionString

        ' 2. Query real cnombre_bd and cnombre_servidor from DatPosAdmin instead of trusting frontend input
        Dim nombreBdHija As String = ""
        Dim nombreServidor As String = ""

        Using connMaster As New SqlConnection(masterConn)
            connMaster.Open()
            Dim cmdEmp As New SqlCommand("SELECT cnombre_bd, cnombre_servidor FROM Empresas WHERE ccod_empresa = @cod AND id_estado = 1", connMaster)
            cmdEmp.Parameters.Add(New SqlParameter("@cod", objBE.ccod_empresa))
            Dim reader As SqlDataReader = cmdEmp.ExecuteReader()
            If reader.Read() Then
                nombreBdHija = reader("cnombre_bd").ToString().Trim()
                nombreServidor = reader("cnombre_servidor").ToString().Trim()
            End If
            reader.Close()
        End Using

        ' 3. Validate that the company has a BD configured
        If String.IsNullOrEmpty(nombreBdHija) Then
            objreturn(1) = "ERROR"
            objreturn(2) = "La empresa '" & objBE.ccod_empresa & "' no tiene una base de datos configurada."
            Return objreturn
        End If

        ' 4. Validate that the child BD actually exists in the server before connecting
        Using connVerif As New SqlConnection(masterConn)
            connVerif.Open()
            Dim cmdVerif As New SqlCommand("SELECT COUNT(*) FROM sys.databases WHERE name = @nombre", connVerif)
            cmdVerif.Parameters.Add(New SqlParameter("@nombre", nombreBdHija))
            Dim existe As Integer = CInt(cmdVerif.ExecuteScalar())
            If existe = 0 Then
                objreturn(1) = "ERROR"
                objreturn(2) = "La base de datos '" & nombreBdHija & "' no existe en el servidor. Contacte al administrador."
                Return objreturn
            End If
        End Using

        ' 5. Build safe connection string using verified data from DB (not from frontend)
        Dim builder As New SqlConnectionStringBuilder(masterConn)
        If Not String.IsNullOrEmpty(nombreServidor) Then
            builder.DataSource = nombreServidor
        End If
        builder.InitialCatalog = nombreBdHija

        Dim conexionSegura As String = builder.ConnectionString
        ' --- FIN DEL CÓDIGO A PRUEBA DE BALAS ---

        Using connection1 As New SqlConnection(conexionSegura)

            connection1.Open() 'abrimos conexion
            Dim cmd As SqlCommand = New SqlCommand 'creamos el comando y asignamos parametros
            cmd.CommandType = CommandType.StoredProcedure
            cmd.CommandText = "webDatpos_editarUsuario"
            cmd.Parameters.Add(New SqlParameter("@ccod_cia", objBE.ccod_empresa))
            cmd.Parameters.Add(New SqlParameter("@usu_crea", objConex.ccod_usuario))
            cmd.Parameters.Add(New SqlParameter("@ccod_usuario", objBE.ccod_usuario))
            cmd.Parameters.Add(New SqlParameter("@cdirc_usuario", objBE.cdirec))
            cmd.Parameters.Add(New SqlParameter("@cdsc_usuario", objBE.cdsc_usuario))
            cmd.Parameters.Add(New SqlParameter("@cpassw", objBE.cpassw))
            cmd.Parameters.Add(New SqlParameter("@rol", objBE.id_rol))
            cmd.Parameters.Add(New SqlParameter("@cstatus", objBE.cstatus))
            cmd.Parameters.Add(New SqlParameter("@cmail", objBE.cmail))
            cmd.Parameters.Add(New SqlParameter("@ctelf", objBE.ctelf))
            cmd.Parameters.Add(New SqlParameter("@ccelular", objBE.ccelular))
            cmd.Parameters.Add(New SqlParameter("@ErrorNumber", SqlDbType.NVarChar, 25)).Direction = ParameterDirection.Output
            cmd.Parameters.Add(New SqlParameter("@ErrorMessage", SqlDbType.NVarChar, 200)).Direction = ParameterDirection.Output
            cmd.Connection = connection1 'enlazamos el comando con la conexion
            cmd.ExecuteNonQuery()

            objreturn(1) = cmd.Parameters("@ErrorNumber").Value
            objreturn(2) = cmd.Parameters("@ErrorMessage").Value

            objreturn(0) = True

        End Using

        Return objreturn

    End Function

    Public Function EliminarUsuarioAdmin(cod As String, obj As BE.BEUser)
        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_usuario", cod))
        cmd.CommandText = "webDatpos_eliminarUsuarioAdmin"
        Return objDAC.executestored(cmd)
    End Function

    Public Function EliminarUsuario(usuario As String, ipServidor As String, nomServidor As String, obj As BE.BEUser)

        Dim objreturn As Boolean = False

        ' --- INICIO DEL CÓDIGO A PRUEBA DE BALAS ---
        ' 1. Get master connection string from Web.config
        Dim masterConn As String = ConfigurationManager.ConnectionStrings("cadenaconexion").ConnectionString

        ' 2. Validate that the child BD actually exists in the server before connecting
        Using connVerif As New SqlConnection(masterConn)
            connVerif.Open()
            Dim cmdVerif As New SqlCommand("SELECT COUNT(*) FROM sys.databases WHERE name = @nombre", connVerif)
            cmdVerif.Parameters.Add(New SqlParameter("@nombre", nomServidor))
            Dim existe As Integer = CInt(cmdVerif.ExecuteScalar())
            If existe = 0 Then
                Throw New Exception("La base de datos '" & nomServidor & "' no existe en el servidor. Contacte al administrador.")
            End If
        End Using

        ' 3. Build safe connection string using verified server and BD name
        Dim conexionSegura As String = "data source=" & ipServidor & "; User ID=ADM; Password=ADM; initial catalog=" & nomServidor & "; integrated security=false"
        ' --- FIN DEL CÓDIGO A PRUEBA DE BALAS ---

        Using connection1 As New SqlConnection(conexionSegura)
            connection1.Open() 'abrimos conexion
            Dim cmd As SqlCommand = New SqlCommand 'creamos el comando y asignamos parametros
            cmd.CommandType = CommandType.StoredProcedure
            cmd.CommandText = "webDatpos_eliminarUsuario"
            cmd.Parameters.Add(New SqlParameter("@ccod_usuario", usuario))
            cmd.Connection = connection1 'enlazamos el comando con la conexion
            cmd.ExecuteNonQuery()
            objreturn = True
        End Using

        Return objreturn

    End Function
    Public Function ValidarBDEmpresa(ccod_empresa As String) As Object
        Dim objreturn As Object = {True, ""}

        ' 1. Get master connection string from Web.config
        Dim masterConn As String = ConfigurationManager.ConnectionStrings("cadenaconexion").ConnectionString

        ' 2. Query cnombre_bd from DatPosAdmin for the given empresa
        Dim nombreBdHija As String = ""

        Using connMaster As New SqlConnection(masterConn)
            connMaster.Open()
            Dim cmdEmp As New SqlCommand("SELECT cnombre_bd FROM Empresas WHERE ccod_empresa = @cod AND id_estado = 1", connMaster)
            cmdEmp.Parameters.Add(New SqlParameter("@cod", ccod_empresa))
            Dim reader As SqlDataReader = cmdEmp.ExecuteReader()
            If reader.Read() Then
                nombreBdHija = reader("cnombre_bd").ToString().Trim()
            End If
            reader.Close()
        End Using

        ' 3. Validate empresa exists and has a BD configured
        If String.IsNullOrEmpty(nombreBdHija) Then
            objreturn(0) = False
            objreturn(1) = "La empresa '" & ccod_empresa & "' no tiene una base de datos configurada."
            Return objreturn
        End If

        ' 4. Validate the BD actually exists in the SQL Server
        Using connVerif As New SqlConnection(masterConn)
            connVerif.Open()
            Dim cmdVerif As New SqlCommand("SELECT COUNT(*) FROM sys.databases WHERE name = @nombre", connVerif)
            cmdVerif.Parameters.Add(New SqlParameter("@nombre", nombreBdHija))
            Dim existe As Integer = CInt(cmdVerif.ExecuteScalar())
            If existe = 0 Then
                objreturn(0) = False
                objreturn(1) = "La base de datos '" & nombreBdHija & "' no existe en el servidor. Contacte al administrador."
            End If
        End Using

        Return objreturn
    End Function

    Public Function ConsultarEmpresasConBDValida() As DataTable
        Dim resultado As New DataTable()
        Dim masterConn As String = ConfigurationManager.ConnectionStrings("cadenaconexion").ConnectionString

        Using conn As New SqlConnection(masterConn)
            conn.Open()
            ' Get all active companies
            Dim cmd As New SqlCommand("SELECT id_empresa, ccod_empresa, cdsc_empresa, cnombre_servidor, cnombre_bd FROM Empresas WHERE id_estado = 1", conn)
            Dim reader As SqlDataReader = cmd.ExecuteReader()

            resultado.Columns.Add("id_empresa")
            resultado.Columns.Add("ccod_empresa")
            resultado.Columns.Add("cdsc_empresa")
            resultado.Columns.Add("cnombre_servidor")
            resultado.Columns.Add("cnombre_bd")

            Dim empresas As New List(Of String())
            While reader.Read()
                empresas.Add(New String() {
                    reader("id_empresa").ToString(),
                    reader("ccod_empresa").ToString(),
                    reader("cdsc_empresa").ToString(),
                    reader("cnombre_servidor").ToString(),
                    reader("cnombre_bd").ToString()
                })
            End While
            reader.Close()

            ' Filter only companies whose BD exists in sys.databases
            For Each emp In empresas
                Dim nombreBd As String = emp(4).Trim()
                If Not String.IsNullOrEmpty(nombreBd) Then
                    Dim cmdVerif As New SqlCommand("SELECT COUNT(*) FROM sys.databases WHERE name = @nombre", conn)
                    cmdVerif.Parameters.Add(New SqlParameter("@nombre", nombreBd))
                    Dim existe As Integer = CInt(cmdVerif.ExecuteScalar())
                    If existe > 0 Then
                        resultado.Rows.Add(emp(0), emp(1), emp(2), emp(3), emp(4))
                    End If
                End If
            Next
        End Using

        Return resultado
    End Function

End Class