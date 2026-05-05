Imports System.Data.SqlClient
Imports System.Configuration

Public Class DAConexionSQL

    Public Function selectstored(comando As SqlCommand) As DataTable

        Dim DS As New DataTable()
        Dim cadenaConexion As String = ConfigurationManager.ConnectionStrings("cadenaconexion").ConnectionString
        Using conexion As New SqlConnection(cadenaConexion)
            comando.Connection = conexion
            comando.CommandType = CommandType.StoredProcedure

            Dim adapter As New SqlDataAdapter(comando)
            adapter.Fill(DS)
        End Using

        'Dim listDT As DataTable = DS

        'If listDT IsNot Nothing AndAlso listDT.Rows.Count > 0 Then

        '    Dim objBE As New BE.BEUser()

        '    For Each fila In listDT.Rows

        '        objBE.id_usuario = fila.ItemArray(0)
        '        objBE.ccod_usuario = fila.ItemArray(1)
        '        objBE.cdsc_usuario = fila.ItemArray(2)
        '        objBE.id_rol = fila.ItemArray(3)
        '        objBE.ccod_empresa = fila.ItemArray(4)
        '        objBE.cdsc_empresa = fila.ItemArray(5)
        '        objBE.cnombre_bd = fila.ItemArray(6)
        '        objBE.cnombre_servidor = fila.ItemArray(7)

        '    Next fila

        'End If

        Return DS

    End Function


    Public Function executestored_OtraConexion(cmd As SqlCommand, obj As BE.BEUser)

        Dim conn As New SqlConnection("data source=" + obj.cnombre_servidor + ";User ID=ADM; Password=ADM; initial catalog=" + obj.cnombre_bd + "; integrated security=false")
        cmd.Connection = conn
        cmd.CommandType = CommandType.StoredProcedure

        Try
            conn.Open()
            cmd.ExecuteNonQuery()
            conn.Close()
            Return True
        Catch ex As Exception
            If conn.State = ConnectionState.Open Then conn.Close()
            ' ¡Lanzamos el error real para que no sea null en JS!
            Throw New Exception("ERROR EN BD HIJA (EMP01): " & ex.Message)
        End Try

    End Function


    Public Function executestored(cmd As SqlCommand)

        Dim conn As New SqlConnection(ConfigurationManager.ConnectionStrings("cadenaconexion").ConnectionString)
        cmd.Connection = conn
        cmd.CommandType = CommandType.StoredProcedure

        Try
            conn.Open()
            cmd.ExecuteNonQuery()
            conn.Close()
            Return True

        Catch ex As Exception
            If conn.State = ConnectionState.Open Then conn.Close()
            ' ¡Lanzamos el error real para que no sea null en JS!
            Throw New Exception("ERROR EN BD PRINCIPAL (DatPosAdmin): " & ex.Message)
        End Try

    End Function

End Class
