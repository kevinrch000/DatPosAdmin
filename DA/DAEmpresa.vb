Imports System.Data.SqlClient
Imports System.Globalization

Public Class DAEmpresa
    Dim objDAC As New DAConexionSQL()


    Public Function CargarDepartamento(ccod_cia As String)
        Dim cmd As New SqlCommand
        cmd.CommandText = "webDatpos_cargarDepartamentos"
        Return objDAC.selectstored(cmd)
    End Function

    Public Function CargarProvincia(id_departamento As String)
        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@id_departamento", id_departamento))
        cmd.CommandText = "webDatpos_cargarProvincias"
        Return objDAC.selectstored(cmd)
    End Function

    Public Function CargarDistrito(id_provincia As String)
        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@id_provincia", id_provincia))
        cmd.CommandText = "webDatpos_cargarDistritos"
        Return objDAC.selectstored(cmd)
    End Function


    Public Function CargarCompanias()

        Dim cmd As New SqlCommand
        cmd.CommandText = "webDatpos_consultarEmpresas"

        Return objDAC.selectstored(cmd)

    End Function

    'Public Function CargarTarifa()

    '    Dim cmd As New SqlCommand
    '    cmd.CommandText = "webDatpos_CargarTarifa"
    '    Return objDAC.selectstored(cmd)

    'End Function


    Public Function CargarCompania(cod As String)

        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", cod))
        cmd.CommandText = "webDatpos_consultarEmpresa"

        Return objDAC.selectstored(cmd)

    End Function

    Public Function InsertarCompania(objBE As BE.BEEmpresa)

        Dim cmd As New SqlCommand

        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", objBE.ccod_empresa))
        cmd.Parameters.Add(New SqlParameter("@cdsc_empresa", objBE.cdescripcion))
        cmd.Parameters.Add(New SqlParameter("@cnum_tribu", objBE.cnum_tribu))
        cmd.Parameters.Add(New SqlParameter("@cnombre_bd", objBE.cnombre_bd))
        cmd.Parameters.Add(New SqlParameter("@cpais_origen", objBE.cpais_origen))
        cmd.Parameters.Add(New SqlParameter("@cnombre_servidor", objBE.cnombre_servidor))
        cmd.Parameters.Add(New SqlParameter("@csimbolo_moneda", objBE.csimbolo_moneda))
        cmd.Parameters.Add(New SqlParameter("@cnombre_moneda", objBE.cnombre_moneda))
        cmd.Parameters.Add(New SqlParameter("@ctarifas", objBE.ctarifas))
        cmd.Parameters.Add(New SqlParameter("@nusuario_extra", objBE.nusuario_extra))
        cmd.Parameters.Add(New SqlParameter("@ntienda_extra", objBE.ntienda_extra))
        cmd.Parameters.Add(New SqlParameter("@cdepartamento", objBE.cdepartamento))
        cmd.Parameters.Add(New SqlParameter("@cdistrito", objBE.cdistrito))
        cmd.Parameters.Add(New SqlParameter("@cprovincia", objBE.cprovincia))
        cmd.Parameters.Add(New SqlParameter("@curbanizacion", objBE.curbanizacion))
        cmd.Parameters.Add(New SqlParameter("@cdomicilio", objBE.cdomicilio))
        cmd.Parameters.Add(New SqlParameter("@cubigeo", objBE.cubigeo))
        cmd.Parameters.Add(New SqlParameter("@nenviosunat", objBE.nenviosunat))

        Dim valorSunat As Object
        If String.IsNullOrWhiteSpace(objBE.dfch_sunat) Then
            valorSunat = DBNull.Value
        Else
            valorSunat = DateTime.ParseExact(objBE.dfch_sunat, "dd/MM/yyyy", CultureInfo.GetCultureInfo("es-ES"))
        End If
        Dim pSunat As New SqlParameter("@dfch_sunat", SqlDbType.DateTime)
        pSunat.Value = valorSunat
        cmd.Parameters.Add(pSunat)

        cmd.Parameters.Add(New SqlParameter("@ccod_cliente_emis", objBE.ccod_cliente_emis))

        Dim valorVenc As Object
        If String.IsNullOrWhiteSpace(objBE.dfch_vencimiento) Then
            valorVenc = DBNull.Value
        Else
            valorVenc = DateTime.ParseExact(objBE.dfch_vencimiento, "dd/MM/yyyy", CultureInfo.GetCultureInfo("es-ES"))
        End If
        Dim pVenc As New SqlParameter("@dfch_vencimiento", SqlDbType.DateTime)
        pVenc.Value = valorVenc
        cmd.Parameters.Add(pVenc)

        cmd.Parameters.Add(New SqlParameter("@ctoken", objBE.ctoken))
        cmd.Parameters.Add(New SqlParameter("@ctip_facturador", objBE.ctip_facturador))
        cmd.CommandText = "webDatpos_insertarEmpresas"

        Return objDAC.executestored(cmd)

    End Function

    Public Function EditarCompania(objBE As BE.BEEmpresa)

        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", objBE.ccod_empresa))
        cmd.Parameters.Add(New SqlParameter("@cdescripcion", objBE.cdescripcion))
        cmd.Parameters.Add(New SqlParameter("@cnum_tribu", objBE.cnum_tribu))
        'cmd.Parameters.Add(New SqlParameter("@cdoc", objBE.cdoc))
        cmd.Parameters.Add(New SqlParameter("@cnombre_bd", objBE.cnombre_bd))

        cmd.Parameters.Add(New SqlParameter("@cnombre_servidor", objBE.cnombre_servidor))
        'cmd.Parameters.Add(New SqlParameter("@cpais_origen", objBE.cpais_origen))'
        cmd.Parameters.Add(New SqlParameter("@csimbolo_moneda", objBE.csimbolo_moneda))
        cmd.Parameters.Add(New SqlParameter("@cnombre_moneda", objBE.cnombre_moneda))

        cmd.Parameters.Add(New SqlParameter("@ctarifas", objBE.ctarifas))
        'cmd.Parameters.Add(New SqlParameter("@ilogo", objBE.ilogo))
        cmd.Parameters.Add(New SqlParameter("@nusuario_extra", objBE.nusuario_extra))
        cmd.Parameters.Add(New SqlParameter("@ntienda_extra", objBE.ntienda_extra))
        'cmd.Parameters.Add(New SqlParameter("@cdsc_facturador", objBE.cdsc_facturador))

        cmd.Parameters.Add(New SqlParameter("@cdepartamento", objBE.cdepartamento))
        cmd.Parameters.Add(New SqlParameter("@cdistrito", objBE.cdistrito))
        cmd.Parameters.Add(New SqlParameter("@cprovincia", objBE.cprovincia))
        cmd.Parameters.Add(New SqlParameter("@curbanizacion", objBE.curbanizacion))
        cmd.Parameters.Add(New SqlParameter("@cdomicilio", objBE.cdomicilio))
        cmd.Parameters.Add(New SqlParameter("@cubigeo", objBE.cubigeo))
        cmd.Parameters.Add(New SqlParameter("@nenviosunat", objBE.nenviosunat))

        'Ejemplo para dfch_sunat
        Dim valorSunat As Object
        If String.IsNullOrWhiteSpace(objBE.dfch_sunat) Then
            valorSunat = DBNull.Value
        Else
            valorSunat = DateTime.ParseExact(objBE.dfch_sunat, "dd/MM/yyyy", CultureInfo.GetCultureInfo("es-ES"))
        End If
        Dim pSunat As New SqlParameter("@dfch_sunat", SqlDbType.DateTime)
        pSunat.Value = valorSunat
        cmd.Parameters.Add(pSunat)

        'Para dfch_vencimiento igual
        Dim valorVenc As Object
        If String.IsNullOrWhiteSpace(objBE.dfch_vencimiento) Then
            valorVenc = DBNull.Value
        Else
            valorVenc = DateTime.ParseExact(objBE.dfch_vencimiento, "dd/MM/yyyy", CultureInfo.GetCultureInfo("es-ES"))
        End If
        Dim pVenc As New SqlParameter("@dfch_vencimiento", SqlDbType.DateTime)
        pVenc.Value = valorVenc
        cmd.Parameters.Add(pVenc)

        cmd.Parameters.Add(New SqlParameter("@ctoken", objBE.ctoken))
        cmd.Parameters.Add(New SqlParameter("@ctip_facturador", objBE.ctip_facturador))
        cmd.CommandText = "webDatpos_editarEmpresa"

        Return objDAC.executestored(cmd)



    End Function

    Public Function EliminarEmpresa(cod As String)

        Dim cmd As New SqlCommand
        cmd.Parameters.Add(New SqlParameter("@ccod_empresa", cod))

        cmd.CommandText = "sp_eliminarempresa"

        Return objDAC.executestored(cmd)

    End Function



End Class
