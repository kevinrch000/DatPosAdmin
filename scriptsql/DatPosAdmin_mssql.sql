-- ============================================================================
-- DatPosAdmin – Esquema Microsoft SQL Server (T-SQL)
-- ============================================================================
-- Script idempotente que crea la base DatPosAdmin con:
--   * 10 tablas con FOREIGN KEYS para integridad referencial
--   * 31 procedimientos almacenados (CREATE OR ALTER, requiere SQL Server 2016+)
--   * Datos semilla minimos (ubigeo Lima/Callao/Arequipa, Empresa Demo, admin/admin)
--
-- Compatibilidad probada: SQL Server 2022 (CU24). Compatible con 2019/2017/2016
-- por uso de CREATE OR ALTER. Para 2014 o anterior, reemplazar por DROP + CREATE.
--
-- Uso:
--   sqlcmd -S localhost -U sa -P <pass> -i DatPosAdmin_mssql.sql
-- ============================================================================

SET NOCOUNT ON;
GO

IF DB_ID(N'DatPosAdmin') IS NULL
    CREATE DATABASE DatPosAdmin;
GO

USE DatPosAdmin;
GO

SET ANSI_NULLS ON;
SET QUOTED_IDENTIFIER ON;
GO

-- ----------------------------------------------------------------------------
-- TABLAS (drop en orden inverso de dependencias para reejecucion limpia)
-- ----------------------------------------------------------------------------

IF OBJECT_ID(N'dbo.Accesos',       N'U') IS NOT NULL DROP TABLE dbo.Accesos;
IF OBJECT_ID(N'dbo.Usuarios',      N'U') IS NOT NULL DROP TABLE dbo.Usuarios;
IF OBJECT_ID(N'dbo.Menus',         N'U') IS NOT NULL DROP TABLE dbo.Menus;
IF OBJECT_ID(N'dbo.Empresas',      N'U') IS NOT NULL DROP TABLE dbo.Empresas;
IF OBJECT_ID(N'dbo.Distrito',      N'U') IS NOT NULL DROP TABLE dbo.Distrito;
IF OBJECT_ID(N'dbo.Provincia',     N'U') IS NOT NULL DROP TABLE dbo.Provincia;
IF OBJECT_ID(N'dbo.Departamento',  N'U') IS NOT NULL DROP TABLE dbo.Departamento;
IF OBJECT_ID(N'dbo.TipoDocumento', N'U') IS NOT NULL DROP TABLE dbo.TipoDocumento;
IF OBJECT_ID(N'dbo.Roles',         N'U') IS NOT NULL DROP TABLE dbo.Roles;
IF OBJECT_ID(N'dbo.Estados',       N'U') IS NOT NULL DROP TABLE dbo.Estados;
GO

CREATE TABLE dbo.Estados (
    id_estado    INT          NOT NULL,
    cdsc_estado  VARCHAR(50)  NOT NULL,
    CONSTRAINT PK_Estados PRIMARY KEY CLUSTERED (id_estado)
);
GO

INSERT INTO dbo.Estados (id_estado, cdsc_estado) VALUES (1, 'Habilitado'), (0, 'Bloqueado');
GO

CREATE TABLE dbo.Roles (
    id_rol    INT IDENTITY(1,1) NOT NULL,
    cdsc_rol  VARCHAR(100)      NOT NULL,
    CONSTRAINT PK_Roles PRIMARY KEY CLUSTERED (id_rol)
);
GO

SET IDENTITY_INSERT dbo.Roles ON;
INSERT INTO dbo.Roles (id_rol, cdsc_rol) VALUES
    (1, 'Administrador'), (2, 'Vendedor'), (3, 'Cajero'), (4, 'Supervisor');
SET IDENTITY_INSERT dbo.Roles OFF;
GO

CREATE TABLE dbo.TipoDocumento (
    id_tipodocumento  INT IDENTITY(1,1) NOT NULL,
    cdsc_tipo_doc     VARCHAR(50)       NOT NULL,
    CONSTRAINT PK_TipoDocumento PRIMARY KEY CLUSTERED (id_tipodocumento)
);
GO

SET IDENTITY_INSERT dbo.TipoDocumento ON;
INSERT INTO dbo.TipoDocumento (id_tipodocumento, cdsc_tipo_doc) VALUES
    (1, 'DNI'), (2, 'RUC'), (3, 'Pasaporte'), (4, 'Carnet de Extranjeria');
SET IDENTITY_INSERT dbo.TipoDocumento OFF;
GO

CREATE TABLE dbo.Departamento (
    id_departamento  VARCHAR(10)  NOT NULL,
    cdescripcion     VARCHAR(100) NULL,
    CONSTRAINT PK_Departamento PRIMARY KEY CLUSTERED (id_departamento)
);
GO

CREATE TABLE dbo.Provincia (
    id_provincia     VARCHAR(10)  NOT NULL,
    id_departamento  VARCHAR(10)  NULL,
    cdescripcion     VARCHAR(100) NULL,
    CONSTRAINT PK_Provincia PRIMARY KEY CLUSTERED (id_provincia),
    CONSTRAINT FK_Provincia_Departamento FOREIGN KEY (id_departamento)
        REFERENCES dbo.Departamento (id_departamento)
        ON UPDATE CASCADE ON DELETE SET NULL
);
GO

CREATE TABLE dbo.Distrito (
    id_distrito   VARCHAR(10)  NOT NULL,
    id_provincia  VARCHAR(10)  NULL,
    cdescripcion  VARCHAR(100) NULL,
    CONSTRAINT PK_Distrito PRIMARY KEY CLUSTERED (id_distrito),
    CONSTRAINT FK_Distrito_Provincia FOREIGN KEY (id_provincia)
        REFERENCES dbo.Provincia (id_provincia)
        ON UPDATE CASCADE ON DELETE SET NULL
);
GO

CREATE TABLE dbo.Empresas (
    id_empresa         INT IDENTITY(1,1) NOT NULL,
    ccod_empresa       VARCHAR(20)       NOT NULL,
    cdsc_empresa       VARCHAR(150)      NULL,
    cnombre_bd         VARCHAR(100)      NULL,
    cnombre_servidor   VARCHAR(100)      NULL,
    id_estado          INT               NULL DEFAULT 1,
    cnum_tribu         VARCHAR(20)       NULL,
    csimbolo_moneda    VARCHAR(5)        NULL,
    cnombre_moneda     VARCHAR(50)       NULL,
    ctarifas           VARCHAR(50)       NULL,
    nusuario_extra     INT               NULL DEFAULT 0,
    ntienda_extra      INT               NULL DEFAULT 0,
    cdepartamento      VARCHAR(10)       NULL,
    cprovincia         VARCHAR(10)       NULL,
    cdistrito          VARCHAR(10)       NULL,
    curbanizacion      VARCHAR(100)      NULL,
    cdomicilio         VARCHAR(200)      NULL,
    cubigeo            VARCHAR(6)        NULL,
    nenviosunat        VARCHAR(2)        NULL,
    dfch_sunat         DATETIME          NULL,
    ccod_cliente_emis  VARCHAR(50)       NULL,
    dfch_vencimiento   DATETIME          NULL,
    ctoken             NVARCHAR(MAX)     NULL,
    ctip_facturador    VARCHAR(50)       NULL,
    dfch_crea          DATETIME          NULL DEFAULT GETDATE(),
    cpais_origen       VARCHAR(50)       NULL,
    cdoc               VARCHAR(20)       NULL,
    cnomser            VARCHAR(100)      NULL,
    CONSTRAINT PK_Empresas       PRIMARY KEY CLUSTERED (ccod_empresa),
    CONSTRAINT UQ_Empresas_Id    UNIQUE NONCLUSTERED (id_empresa),
    CONSTRAINT FK_Empresas_Estado       FOREIGN KEY (id_estado)
        REFERENCES dbo.Estados       (id_estado)       ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_Empresas_Departamento FOREIGN KEY (cdepartamento)
        REFERENCES dbo.Departamento  (id_departamento) ON UPDATE NO ACTION ON DELETE SET NULL,
    CONSTRAINT FK_Empresas_Provincia    FOREIGN KEY (cprovincia)
        REFERENCES dbo.Provincia     (id_provincia)    ON UPDATE NO ACTION ON DELETE SET NULL,
    CONSTRAINT FK_Empresas_Distrito     FOREIGN KEY (cdistrito)
        REFERENCES dbo.Distrito      (id_distrito)     ON UPDATE NO ACTION ON DELETE SET NULL
);
GO

CREATE TABLE dbo.Menus (
    id_menu        INT IDENTITY(1,1) NOT NULL,
    cdsc_menu      VARCHAR(100) NULL,
    curl_href      VARCHAR(255) NULL,
    curl_src       VARCHAR(255) NULL,
    nid_menupadre  INT          NULL,
    cli_menu       VARCHAR(100) NULL,
    cul_menu       VARCHAR(100) NULL,
    nivel          VARCHAR(10)  NULL,
    corden         INT          NULL,
    cstatus        VARCHAR(1)   NULL DEFAULT 'A',
    CONSTRAINT PK_Menus PRIMARY KEY CLUSTERED (id_menu),
    CONSTRAINT FK_Menus_Padre FOREIGN KEY (nid_menupadre)
        REFERENCES dbo.Menus (id_menu)
        ON UPDATE NO ACTION ON DELETE NO ACTION  -- self-ref no admite cascadas en SQL Server
);
GO

CREATE TABLE dbo.Accesos (
    id_acceso  INT IDENTITY(1,1) NOT NULL,
    id_rol     INT NULL,
    id_menu    INT NULL,
    CONSTRAINT PK_Accesos PRIMARY KEY CLUSTERED (id_acceso),
    CONSTRAINT FK_Accesos_Rol  FOREIGN KEY (id_rol)
        REFERENCES dbo.Roles (id_rol) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_Accesos_Menu FOREIGN KEY (id_menu)
        REFERENCES dbo.Menus (id_menu) ON UPDATE CASCADE ON DELETE CASCADE
);
GO

CREATE TABLE dbo.Usuarios (
    id_usuario     INT IDENTITY(1,1) NOT NULL,
    ccod_usuario   VARCHAR(50)  NULL,
    cpassw         VARCHAR(100) NULL,
    cdsc_usuario   VARCHAR(150) NULL,
    id_rol         INT          NULL,
    ccod_empresa   VARCHAR(20)  NULL,
    cmail          VARCHAR(100) NULL,
    ctelf          VARCHAR(20)  NULL,
    ccelular       VARCHAR(20)  NULL,
    cdirec         VARCHAR(200) NULL,
    id_estado      INT          NULL DEFAULT 1,
    dfch_crea      DATETIME     NULL DEFAULT GETDATE(),
    ccod_tiend     VARCHAR(20)  NULL,
    ccod_almacen   VARCHAR(20)  NULL,
    ccod_caja      VARCHAR(20)  NULL,
    cperm_descn    VARCHAR(50)  NULL,
    ifoto          VARBINARY(MAX) NULL,
    CONSTRAINT PK_Usuarios          PRIMARY KEY CLUSTERED (id_usuario),
    CONSTRAINT UQ_Usuarios_Codigo   UNIQUE NONCLUSTERED (ccod_usuario),
    -- Empresas ya cascada de Estados → Usuarios podria recibir doble path, asi
    -- que para SQL Server forzamos NO ACTION en los FKs a Roles y Estados.
    CONSTRAINT FK_Usuarios_Empresa  FOREIGN KEY (ccod_empresa)
        REFERENCES dbo.Empresas (ccod_empresa) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_Usuarios_Rol      FOREIGN KEY (id_rol)
        REFERENCES dbo.Roles    (id_rol)       ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT FK_Usuarios_Estado   FOREIGN KEY (id_estado)
        REFERENCES dbo.Estados  (id_estado)    ON UPDATE NO ACTION ON DELETE NO ACTION
);
GO

-- ----------------------------------------------------------------------------
-- PROCEDIMIENTOS ALMACENADOS
-- ----------------------------------------------------------------------------

CREATE OR ALTER PROCEDURE dbo.ConsultasEmpresasPrincipal
    @ccod_empresa  VARCHAR(20),
    @ctarifas      VARCHAR(50),
    @cpais_origen  VARCHAR(50),
    @cstatus       VARCHAR(10)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        ccod_empresa,
        cdsc_empresa,
        ISNULL(cnum_tribu, '')   AS Documento,
        cnombre_servidor,
        cnombre_bd,
        ISNULL(cpais_origen, '') AS Pais,
        ISNULL(ctarifas, '')     AS Tarifa,
        id_estado
    FROM dbo.Empresas
    WHERE (@ccod_empresa = '' OR ccod_empresa LIKE '%' + @ccod_empresa + '%')
      AND (@ctarifas = 'T' OR @ctarifas = '' OR ISNULL(ctarifas, '') = @ctarifas)
      AND (@cpais_origen = '' OR ISNULL(cpais_origen, '') LIKE '%' + @cpais_origen + '%')
      AND (@cstatus = 'T' OR @cstatus = '' OR id_estado = TRY_CAST(@cstatus AS INT));
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_consultarempresas
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        ccod_empresa,
        cdsc_empresa            AS cdescripcion,
        ISNULL(cdoc, '')        AS cdoc,
        ISNULL(cnum_tribu, '')  AS cnum_tribu,
        ISNULL(cnomser, '')     AS cnomser,
        cnombre_bd
    FROM dbo.Empresas;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_consultarroles
AS
BEGIN
    SET NOCOUNT ON;
    SELECT id_rol, cdsc_rol AS cdescripcion FROM dbo.Roles;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_consultaestados
AS
BEGIN
    SET NOCOUNT ON;
    SELECT id_estado, cdsc_estado AS cdescripcion FROM dbo.Estados;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_consultatipodocumento
AS
BEGIN
    SET NOCOUNT ON;
    SELECT id_tipodocumento, cdsc_tipo_doc AS cdescripcion FROM dbo.TipoDocumento;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_editarempresa
    @ccod_empresa  VARCHAR(20),
    @cdescripcion  VARCHAR(200),
    @cdoc          VARCHAR(20),
    @cnum_tribu    VARCHAR(20),
    @cnomser       VARCHAR(100),
    @cnombre_bd    VARCHAR(100)
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE dbo.Empresas
    SET cdsc_empresa = @cdescripcion,
        cdoc         = @cdoc,
        cnum_tribu   = @cnum_tribu,
        cnomser      = @cnomser,
        cnombre_bd   = @cnombre_bd
    WHERE ccod_empresa = @ccod_empresa;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_editarusuariocliente
    @ccod_usuario  VARCHAR(50),
    @cdsc_usuario  VARCHAR(200),
    @id_rol        INT,
    @ccod_empresa  VARCHAR(20),
    @id_estado     INT,
    @cmail         VARCHAR(100),
    @ctelf         VARCHAR(20),
    @ccelular      VARCHAR(20),
    @cdirec        VARCHAR(200)
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE dbo.Usuarios
    SET cdsc_usuario = @cdsc_usuario,
        id_rol       = @id_rol,
        ccod_empresa = @ccod_empresa,
        id_estado    = @id_estado,
        cmail        = @cmail,
        ctelf        = @ctelf,
        ccelular     = @ccelular,
        cdirec       = @cdirec
    WHERE ccod_usuario = @ccod_usuario;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_eliminarempresa
    @ccod_empresa VARCHAR(20)
AS
BEGIN
    SET NOCOUNT ON;
    DELETE FROM dbo.Empresas WHERE ccod_empresa = @ccod_empresa;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_eliminarusuariocliente
    @ccod_usuario VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    DELETE FROM dbo.Usuarios WHERE ccod_usuario = @ccod_usuario;
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_insertarempresas
    @ccod_empresa  VARCHAR(20),
    @cdescripcion  VARCHAR(200),
    @cdoc          VARCHAR(20),
    @cnum_tribu    VARCHAR(20),
    @cnomser       VARCHAR(100),
    @cnombre_bd    VARCHAR(100)
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.Empresas (ccod_empresa, cdsc_empresa, cdoc, cnum_tribu, cnomser, cnombre_bd)
    VALUES (@ccod_empresa, @cdescripcion, @cdoc, @cnum_tribu, @cnomser, @cnombre_bd);
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_insertarusuarios
    @ccod_usuario  VARCHAR(50),
    @cdsc_usuario  VARCHAR(200),
    @cpassw        VARCHAR(200),
    @id_rol        INT,
    @ccod_empresa  VARCHAR(20),
    @id_estado     INT,
    @cmail         VARCHAR(100),
    @ctelf         VARCHAR(20),
    @ccelular      VARCHAR(20),
    @cdirec        VARCHAR(200)
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.Usuarios
        (ccod_usuario, cdsc_usuario, cpassw, id_rol, ccod_empresa,
         id_estado, cmail, ctelf, ccelular, cdirec)
    VALUES
        (@ccod_usuario, @cdsc_usuario, @cpassw, @id_rol, @ccod_empresa,
         @id_estado, @cmail, @ctelf, @ccelular, @cdirec);
END;
GO

CREATE OR ALTER PROCEDURE dbo.sp_validarusuario
    @ccod_usuario VARCHAR(50),
    @cpassw       VARCHAR(200)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        U.id_usuario                     AS id_ctusu,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.id_rol                         AS rolMaster,
        U.ccod_empresa,
        ISNULL(E.cnombre_bd, '')         AS cnombre_bd,
        ISNULL(E.cnombre_servidor, '')   AS cnomser,
        ISNULL(E.cdsc_empresa, '')       AS cdescripcion,
        ISNULL(E.cnum_tribu, '')         AS cnum_tribu,
        ISNULL(E.ntienda_extra, 0)       AS ntienda_extra,
        ISNULL(E.nusuario_extra, 0)      AS nusuario_extra,
        ISNULL(E.ctarifas, '')           AS ctarifas,
        ISNULL(E.cnombre_moneda, '')     AS cnombre_moneda,
        ISNULL(E.csimbolo_moneda, '')    AS csimbolo_moneda,
        ISNULL(E.cdomicilio, '')         AS cdomicilio,
        ISNULL(E.cprovincia, '')         AS cprovincia,
        ISNULL(E.cdistrito, '')          AS cdistrito,
        ISNULL(E.cdepartamento, '')      AS cdepartamento,
        ISNULL(E.ctip_facturador, '')    AS ctip_facturador,
        E.dfch_vencimiento,
        CASE WHEN U.id_estado = 1 THEN 'Habilitado' ELSE 'Bloqueado' END AS estado,
        ISNULL(E.ccod_cliente_emis, '')  AS ccod_cliente_emis,
        ISNULL(CAST(E.ctoken AS NVARCHAR(MAX)), '') AS ctoken
    FROM dbo.Usuarios U
    INNER JOIN dbo.Empresas E ON E.ccod_empresa = U.ccod_empresa
    WHERE U.ccod_usuario = @ccod_usuario
      AND U.cpassw       = @cpassw;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_buscarTarifa
    @ccod_empresa  VARCHAR(20),
    @ctarifas      VARCHAR(20),
    @cpais_origen  VARCHAR(50),
    @cstatus       VARCHAR(10)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        E.ccod_empresa,
        E.cdsc_empresa,
        ISNULL(E.cnum_tribu, '')   AS Documento,
        E.cnombre_servidor,
        E.cnombre_bd,
        ISNULL(E.cpais_origen, '') AS Pais,
        ISNULL(E.ctarifas, '')     AS Tarifa,
        E.id_estado
    FROM dbo.Empresas E
    WHERE (@ccod_empresa = '' OR E.ccod_empresa = @ccod_empresa)
      AND (@ctarifas = 'T' OR ISNULL(E.ctarifas, '') = @ctarifas)
      AND (@cpais_origen = 'T' OR ISNULL(E.cpais_origen, '') = @cpais_origen)
      AND (@cstatus = 'T' OR E.id_estado = TRY_CAST(@cstatus AS INT));
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_cambiarContrasena
    @ccod_usuario VARCHAR(50),
    @cpassw       VARCHAR(200),
    @newpassw     VARCHAR(200)
AS
BEGIN
    SET NOCOUNT ON;
    DECLARE @existe INT = 0;
    SELECT @existe = COUNT(*)
    FROM dbo.Usuarios
    WHERE ccod_usuario = @ccod_usuario
      AND cpassw       = @cpassw;

    IF @existe > 0
    BEGIN
        UPDATE dbo.Usuarios SET cpassw = @newpassw WHERE ccod_usuario = @ccod_usuario;
        SELECT 1 AS resultado;
    END
    ELSE
    BEGIN
        SELECT 0 AS resultado;
    END
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_cargarDepartamentos
AS
BEGIN
    SET NOCOUNT ON;
    SELECT id_departamento, cdescripcion FROM dbo.Departamento ORDER BY cdescripcion;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_cargarDistritos
    @id_provincia VARCHAR(10)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT id_distrito, cdescripcion
    FROM dbo.Distrito
    WHERE id_provincia = @id_provincia
    ORDER BY cdescripcion;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_cargarProvincias
    @id_departamento VARCHAR(10)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT id_provincia, cdescripcion
    FROM dbo.Provincia
    WHERE id_departamento = @id_departamento
    ORDER BY cdescripcion;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_consultaPorCodEmpresa
    @ccod_empresa VARCHAR(20),
    @cstatus      VARCHAR(10)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        E.ccod_empresa,
        E.cdsc_empresa,
        U.ccod_usuario,
        U.cdsc_usuario,
        ISNULL(U.cdirec, '')        AS cdirec,
        ISNULL(R.cdsc_rol, '')      AS cdsc_rol,
        ISNULL(D.cdescripcion, '')  AS cdsc_departamento,
        U.id_estado,
        ISNULL(U.ccelular, '')      AS ccelular
    FROM dbo.Usuarios U
    INNER JOIN dbo.Empresas E ON U.ccod_empresa = E.ccod_empresa
    LEFT JOIN dbo.Roles R         ON U.id_rol         = R.id_rol
    LEFT JOIN dbo.Departamento D  ON E.cdepartamento  = D.id_departamento
    WHERE E.ccod_empresa = @ccod_empresa
      AND (@cstatus = 'T' OR @cstatus = '' OR U.id_estado = TRY_CAST(@cstatus AS INT));
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_consultarEmpresa
    @ccod_empresa VARCHAR(20)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        id_empresa,
        ccod_empresa,
        cdsc_empresa,
        ISNULL(cnum_tribu, '')      AS cnum_tribu,
        cnombre_servidor,
        cnombre_bd,
        ISNULL(csimbolo_moneda, '') AS csimbolo_moneda,
        ISNULL(cnombre_moneda, '')  AS cnombre_moneda,
        ISNULL(ctarifas, '')        AS ctarifas,
        ISNULL(nusuario_extra, 0)   AS nusuario_extra,
        ISNULL(ntienda_extra, 0)    AS ntienda_extra,
        cdepartamento,
        cprovincia,
        cdistrito,
        curbanizacion,
        cdomicilio,
        cubigeo,
        nenviosunat,
        dfch_sunat,
        ccod_cliente_emis,
        dfch_vencimiento,
        ctoken,
        ctip_facturador,
        dfch_crea
    FROM dbo.Empresas
    WHERE ccod_empresa = @ccod_empresa;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_consultarEmpresas
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        id_empresa,
        ccod_empresa,
        cdsc_empresa,
        ISNULL(cnum_tribu, '')      AS cnum_tribu,
        cnombre_servidor,
        cnombre_bd,
        ISNULL(csimbolo_moneda, '') AS csimbolo_moneda,
        ISNULL(cnombre_moneda, '')  AS cnombre_moneda,
        ISNULL(ctarifas, '')        AS ctarifas,
        ISNULL(nusuario_extra, 0)   AS nusuario_extra,
        ISNULL(ntienda_extra, 0)    AS ntienda_extra,
        ISNULL(CONVERT(VARCHAR(19), dfch_crea, 120), '') AS dfch_crea
    FROM dbo.Empresas
    WHERE id_estado = 1;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_consultaUsuario
    @ccod_usuario VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        U.id_usuario,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.cpassw,
        ISNULL(U.cdirec, '')                              AS cdirec,
        U.id_rol,
        U.ccod_empresa,
        CAST(U.id_estado AS VARCHAR(10))                  AS id_estado,
        ISNULL(CONVERT(VARCHAR(19), U.dfch_crea, 120), '') AS dfch_crea,
        ISNULL(U.cmail, '')                               AS cmail,
        ISNULL(U.ctelf, '')                               AS ctelf,
        ISNULL(U.ccelular, '')                            AS ccelular,
        ISNULL(E.cdsc_empresa, '')                        AS cdsc_empresa,
        ISNULL(U.ccod_tiend, '')                          AS ccod_tiend,
        ISNULL(U.ccod_almacen, '')                        AS ccod_almacen,
        ISNULL(U.ccod_caja, '')                           AS ccod_caja,
        ISNULL(U.cperm_descn, '')                         AS cperm_descn
    FROM dbo.Usuarios U
    INNER JOIN dbo.Empresas E ON U.ccod_empresa = E.ccod_empresa
    WHERE U.ccod_usuario = @ccod_usuario
      AND U.id_estado = 1;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_consultaUsuarios
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        U.id_usuario,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.cpassw,
        ISNULL(U.cdirec, '')                               AS cdirec,
        CAST(U.id_rol AS VARCHAR(10))                      AS id_rol,
        U.ccod_empresa,
        CAST(U.id_estado AS VARCHAR(10))                   AS id_estado,
        ISNULL(CONVERT(VARCHAR(19), U.dfch_crea, 120), '') AS dfch_crea
    FROM dbo.Usuarios U
    WHERE U.id_estado = 1;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_contadorEmpresa
AS
BEGIN
    SET NOCOUNT ON;
    SELECT COUNT(*) AS cantidaTienda FROM dbo.Empresas WHERE id_estado = 1;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_contadorUsuario
AS
BEGIN
    SET NOCOUNT ON;
    SELECT COUNT(*) AS cantidaUsuarios FROM dbo.Usuarios WHERE id_estado = 1;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_countUsuariosPorEmpresa
    @ccod_empresa VARCHAR(20)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        E.ccod_empresa,
        E.cdsc_empresa,
        COUNT(U.id_usuario) AS TotalUsuarios
    FROM dbo.Empresas E
    LEFT JOIN dbo.Usuarios U
        ON E.ccod_empresa = U.ccod_empresa
       AND U.id_estado = 1
    WHERE (@ccod_empresa = '' OR E.ccod_empresa = @ccod_empresa)
    GROUP BY E.ccod_empresa, E.cdsc_empresa;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_editarEmpresa
    @ccod_empresa      VARCHAR(20),
    @cdescripcion      VARCHAR(150),
    @cnum_tribu        VARCHAR(20),
    @cnombre_bd        VARCHAR(100),
    @cnombre_servidor  VARCHAR(100),
    @csimbolo_moneda   VARCHAR(5),
    @cnombre_moneda    VARCHAR(50),
    @ctarifas          VARCHAR(50),
    @nusuario_extra    INT,
    @ntienda_extra     INT,
    @cdepartamento     VARCHAR(10),
    @cdistrito         VARCHAR(10),
    @cprovincia        VARCHAR(10),
    @curbanizacion     VARCHAR(100),
    @cdomicilio        VARCHAR(200),
    @cubigeo           VARCHAR(6),
    @nenviosunat       VARCHAR(2),
    @dfch_sunat        DATETIME,
    @dfch_vencimiento  DATETIME,
    @ctoken            NVARCHAR(MAX),
    @ctip_facturador   VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE dbo.Empresas
    SET cdsc_empresa     = ISNULL(@cdescripcion,     cdsc_empresa),
        cnum_tribu       = ISNULL(@cnum_tribu,       cnum_tribu),
        cnombre_bd       = ISNULL(@cnombre_bd,       cnombre_bd),
        cnombre_servidor = ISNULL(@cnombre_servidor, cnombre_servidor),
        csimbolo_moneda  = ISNULL(@csimbolo_moneda,  csimbolo_moneda),
        cnombre_moneda   = ISNULL(@cnombre_moneda,   cnombre_moneda),
        ctarifas         = ISNULL(@ctarifas,         ctarifas),
        nusuario_extra   = ISNULL(@nusuario_extra,   nusuario_extra),
        ntienda_extra    = ISNULL(@ntienda_extra,    ntienda_extra),
        cdepartamento    = ISNULL(@cdepartamento,    cdepartamento),
        cdistrito        = ISNULL(@cdistrito,        cdistrito),
        cprovincia       = ISNULL(@cprovincia,       cprovincia),
        curbanizacion    = ISNULL(@curbanizacion,    curbanizacion),
        cdomicilio       = ISNULL(@cdomicilio,       cdomicilio),
        cubigeo          = ISNULL(@cubigeo,          cubigeo),
        nenviosunat      = ISNULL(@nenviosunat,      nenviosunat),
        dfch_sunat       = ISNULL(@dfch_sunat,       dfch_sunat),
        dfch_vencimiento = ISNULL(@dfch_vencimiento, dfch_vencimiento),
        ctoken           = ISNULL(@ctoken,           ctoken),
        ctip_facturador  = ISNULL(@ctip_facturador,  ctip_facturador)
    WHERE ccod_empresa = @ccod_empresa;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_editarUsuarioAdmin
    @ccod_usuario  VARCHAR(50),
    @cdsc_usuario  VARCHAR(150),
    @cpassw        VARCHAR(100),
    @cdirec        VARCHAR(200),
    @id_rol        INT,
    @ccod_empresa  VARCHAR(20),
    @cstatus       VARCHAR(1),
    @cmail         VARCHAR(100),
    @ctelf         VARCHAR(20),
    @ccelular      VARCHAR(20)
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE dbo.Usuarios
    SET cdsc_usuario = @cdsc_usuario,
        cpassw       = @cpassw,
        cdirec       = ISNULL(@cdirec, ''),
        id_rol       = @id_rol,
        ccod_empresa = @ccod_empresa,
        cmail        = ISNULL(@cmail, ''),
        ctelf        = ISNULL(@ctelf, ''),
        ccelular     = ISNULL(@ccelular, ''),
        id_estado    = CASE WHEN @cstatus = 'A' OR @cstatus = '1' THEN 1 ELSE 0 END
    WHERE ccod_usuario = @ccod_usuario;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_eliminarUsuarioAdmin
    @ccod_usuario VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE dbo.Usuarios SET id_estado = 0 WHERE ccod_usuario = @ccod_usuario;
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_insertarEmpresas
    @ccod_empresa       VARCHAR(20),
    @cdsc_empresa       VARCHAR(150),
    @cnombre_bd         VARCHAR(100),
    @cnombre_servidor   VARCHAR(100),
    @cnum_tribu         VARCHAR(20),
    @csimbolo_moneda    VARCHAR(5),
    @cnombre_moneda     VARCHAR(50),
    @ctarifas           VARCHAR(50),
    @nusuario_extra     INT,
    @ntienda_extra      INT,
    @cdepartamento      VARCHAR(10),
    @cprovincia         VARCHAR(10),
    @cdistrito          VARCHAR(10),
    @curbanizacion      VARCHAR(100),
    @cdomicilio         VARCHAR(200),
    @cubigeo            VARCHAR(6),
    @nenviosunat        VARCHAR(2),
    @dfch_sunat         DATETIME,
    @ccod_cliente_emis  VARCHAR(50),
    @dfch_vencimiento   DATETIME,
    @ctoken             NVARCHAR(MAX),
    @ctip_facturador    VARCHAR(50),
    @id_estado          INT,
    @cpais_origen       VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.Empresas
        (ccod_empresa, cdsc_empresa, cnombre_bd, cnombre_servidor, id_estado, cnum_tribu,
         csimbolo_moneda, cnombre_moneda, ctarifas, nusuario_extra, ntienda_extra, cdepartamento,
         cprovincia, cdistrito, curbanizacion, cdomicilio, cubigeo, nenviosunat, dfch_sunat,
         ccod_cliente_emis, dfch_vencimiento, ctoken, ctip_facturador, dfch_crea, cpais_origen)
    VALUES
        (@ccod_empresa, @cdsc_empresa, @cnombre_bd, @cnombre_servidor, ISNULL(@id_estado, 1),
         @cnum_tribu, @csimbolo_moneda, @cnombre_moneda, @ctarifas,
         ISNULL(@nusuario_extra, 0), ISNULL(@ntienda_extra, 0), @cdepartamento,
         @cprovincia, @cdistrito, @curbanizacion, @cdomicilio, @cubigeo, @nenviosunat,
         @dfch_sunat, @ccod_cliente_emis, @dfch_vencimiento, @ctoken, @ctip_facturador,
         GETDATE(), @cpais_origen);
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_insertarUsuarioAdmin
    @ccod_usuario  VARCHAR(50),
    @cdsc_usuario  VARCHAR(150),
    @cpassw        VARCHAR(100),
    @cdirec        VARCHAR(200),
    @id_rol        INT,
    @ccod_empresa  VARCHAR(20),
    @cstatus       VARCHAR(1),
    @cmail         VARCHAR(100),
    @ctelf         VARCHAR(20),
    @ccelular      VARCHAR(20)
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO dbo.Usuarios
        (ccod_usuario, cdsc_usuario, cpassw, cdirec, id_rol, ccod_empresa,
         cmail, ctelf, ccelular, id_estado)
    VALUES
        (@ccod_usuario, @cdsc_usuario, @cpassw, ISNULL(@cdirec, ''), @id_rol, @ccod_empresa,
         ISNULL(@cmail, ''), ISNULL(@ctelf, ''), ISNULL(@ccelular, ''),
         CASE WHEN @cstatus = 'A' OR @cstatus = '1' THEN 1 ELSE 0 END);
END;
GO

CREATE OR ALTER PROCEDURE dbo.webDatpos_validarUsuario
    @ccod_usuario VARCHAR(50),
    @cpassw       VARCHAR(100)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        U.id_usuario,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.id_rol,
        U.ccod_empresa,
        E.cdsc_empresa,
        E.cnombre_bd,
        E.cnombre_servidor
    FROM dbo.Usuarios U
    INNER JOIN dbo.Empresas E ON U.ccod_empresa = E.ccod_empresa
    WHERE U.ccod_usuario = @ccod_usuario
      AND U.cpassw       = @cpassw
      AND U.id_estado    = 1;
END;
GO

-- ============================================================================
-- Datos semilla minimos para poder arrancar la app y hacer login.
-- (Idempotente: usa MERGE para upsert sin duplicar.)
-- ============================================================================

-- Ubigeo: Lima / Callao / Arequipa
MERGE dbo.Departamento AS T
USING (VALUES ('15','LIMA'), ('07','CALLAO'), ('04','AREQUIPA')) AS S(id_departamento, cdescripcion)
   ON T.id_departamento = S.id_departamento
WHEN NOT MATCHED BY TARGET THEN
    INSERT (id_departamento, cdescripcion) VALUES (S.id_departamento, S.cdescripcion)
WHEN MATCHED THEN
    UPDATE SET cdescripcion = S.cdescripcion;
GO

MERGE dbo.Provincia AS T
USING (VALUES ('1501','15','LIMA'), ('0701','07','CALLAO'), ('0401','04','AREQUIPA'))
       AS S(id_provincia, id_departamento, cdescripcion)
   ON T.id_provincia = S.id_provincia
WHEN NOT MATCHED BY TARGET THEN
    INSERT (id_provincia, id_departamento, cdescripcion)
    VALUES (S.id_provincia, S.id_departamento, S.cdescripcion)
WHEN MATCHED THEN
    UPDATE SET id_departamento = S.id_departamento, cdescripcion = S.cdescripcion;
GO

MERGE dbo.Distrito AS T
USING (VALUES ('150101','1501','LIMA CERCADO'),
              ('150116','1501','MIRAFLORES'),
              ('070101','0701','CALLAO'))
       AS S(id_distrito, id_provincia, cdescripcion)
   ON T.id_distrito = S.id_distrito
WHEN NOT MATCHED BY TARGET THEN
    INSERT (id_distrito, id_provincia, cdescripcion)
    VALUES (S.id_distrito, S.id_provincia, S.cdescripcion)
WHEN MATCHED THEN
    UPDATE SET id_provincia = S.id_provincia, cdescripcion = S.cdescripcion;
GO

-- Empresa demo (Usuarios.ccod_empresa hace FK a esta tabla).
IF NOT EXISTS (SELECT 1 FROM dbo.Empresas WHERE ccod_empresa = 'EMP01')
BEGIN
    INSERT INTO dbo.Empresas
        (ccod_empresa, cdsc_empresa, cnombre_bd, cnombre_servidor, id_estado,
         cnum_tribu, csimbolo_moneda, cnombre_moneda, ctarifas, nusuario_extra,
         ntienda_extra, cdepartamento, cprovincia, cdistrito, cpais_origen)
    VALUES ('EMP01', 'Empresa Demo', 'dbdemo', 'localhost', 1,
            '20123456789', 'S/.', 'SOLES', 'T1', 0,
            0, '15', '1501', '150101', 'PERU');
END;
GO

-- Usuario admin / admin para iniciar sesion al instalar.
IF NOT EXISTS (SELECT 1 FROM dbo.Usuarios WHERE ccod_usuario = 'admin')
BEGIN
    INSERT INTO dbo.Usuarios
        (ccod_usuario, cpassw, cdsc_usuario, id_rol, ccod_empresa,
         cmail, ctelf, ccelular, cdirec, id_estado)
    VALUES ('admin', 'admin', 'Administrador del Sistema', 1, 'EMP01',
            'admin@datpos.local', '01-2345678', '999999999', 'Av. Demo 123', 1);
END;
GO

DECLARE @nTablas INT = (SELECT COUNT(*) FROM sys.tables);
DECLARE @nSPs    INT = (SELECT COUNT(*) FROM sys.procedures);
DECLARE @nFKs    INT = (SELECT COUNT(*) FROM sys.foreign_keys);
PRINT '----------------------------------------------------------------';
PRINT 'DatPosAdmin (T-SQL) instalado correctamente.';
PRINT 'Tablas:         ' + CAST(@nTablas AS VARCHAR(10));
PRINT 'Procedimientos: ' + CAST(@nSPs    AS VARCHAR(10));
PRINT 'FKs:            ' + CAST(@nFKs    AS VARCHAR(10));
PRINT 'Login: admin / admin  (cambiar antes de produccion)';
PRINT '----------------------------------------------------------------';
GO
