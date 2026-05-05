Public Class BLEmpresa

    Dim objDA As New DA.DAEmpresa()


    Public Function CargarDepartamento(ccod_cia As String)
        Return objDA.CargarDepartamento(ccod_cia)
    End Function

    Public Function CargarProvincia(id_departamento As String)
        Return objDA.CargarProvincia(id_departamento)
    End Function

    Public Function CargarDistrito(id_provincia As String)
        Return objDA.CargarDistrito(id_provincia)
    End Function
     
    'Public Function CargarTarifa()
    '    Return objDA.CargarTarifa()
    'End Function
     
    Public Function CargarCompania(cod As String)
        Return objDA.CargarCompania(cod)
    End Function


    Public Function CargarCompanias()
        Return objDA.CargarCompanias()
    End Function

    Public Function InsertarCompania(objBE As BE.BEEmpresa)
        Return objDA.InsertarCompania(objBE)
    End Function

    Public Function EditarCompania(objBE As BE.BEEmpresa)
        Return objDA.EditarCompania(objBE)
    End Function


    Public Function EliminarEmpresa(cod As String)
        Return objDA.EliminarEmpresa(cod)
    End Function

    Public Function CargarCompaniasConBDValida()
        Return New DA.DAUsuario().ConsultarEmpresasConBDValida()
    End Function

End Class
