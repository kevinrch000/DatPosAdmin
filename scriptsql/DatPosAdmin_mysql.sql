-- ===========================================================================
-- DatPosAdmin - Esquema MySQL/MariaDB (migrado desde SQL Server)
-- ===========================================================================
-- Migracion 1:1 del esquema y procedimientos almacenados de DatPosAdmin.sql
-- Cambios:
--   * Sintaxis MySQL/MariaDB (UTF-8, InnoDB, AUTO_INCREMENT, IFNULL, NOW, etc).
--   * FOREIGN KEYS agregadas para relacionar tablas.
--   * Tablas Estados, Roles, TipoDocumento, Departamento, Provincia, Distrito
--     se siembran con datos basicos para que las FKs sean utiles.
--   * Mismos nombres de procedimientos almacenados que el script T-SQL
--     original, llamables desde PHP via PDO::query("CALL <sp>(...)").
-- ===========================================================================

DROP DATABASE IF EXISTS DatPosAdmin;
CREATE DATABASE DatPosAdmin
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE DatPosAdmin;

-- Permitir conservar el valor literal 0 al insertar en columnas auto-increment
-- (necesario porque el dominio de id_estado incluye 0 = Bloqueado).
SET sql_mode = CONCAT(@@sql_mode, ',NO_AUTO_VALUE_ON_ZERO');

-- ---------------------------------------------------------------------------
-- TABLAS
-- ---------------------------------------------------------------------------

CREATE TABLE Estados (
    id_estado    INT AUTO_INCREMENT PRIMARY KEY,
    cdsc_estado  VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO Estados (id_estado, cdsc_estado) VALUES
    (1, 'Habilitado'),
    (0, 'Bloqueado');

CREATE TABLE Roles (
    id_rol    INT AUTO_INCREMENT PRIMARY KEY,
    cdsc_rol  VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO Roles (id_rol, cdsc_rol) VALUES
    (1, 'Administrador'),
    (2, 'Vendedor'),
    (3, 'Cajero'),
    (4, 'Supervisor');

CREATE TABLE TipoDocumento (
    id_tipodocumento  INT AUTO_INCREMENT PRIMARY KEY,
    cdsc_tipo_doc     VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO TipoDocumento (id_tipodocumento, cdsc_tipo_doc) VALUES
    (1, 'DNI'),
    (2, 'RUC'),
    (3, 'Pasaporte'),
    (4, 'Carnet de Extranjeria');

CREATE TABLE Departamento (
    id_departamento  VARCHAR(10) NOT NULL PRIMARY KEY,
    cdescripcion     VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Provincia (
    id_provincia     VARCHAR(10) NOT NULL PRIMARY KEY,
    id_departamento  VARCHAR(10) NULL,
    cdescripcion     VARCHAR(100) NULL,
    CONSTRAINT FK_Provincia_Departamento
        FOREIGN KEY (id_departamento) REFERENCES Departamento (id_departamento)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Distrito (
    id_distrito   VARCHAR(10) NOT NULL PRIMARY KEY,
    id_provincia  VARCHAR(10) NULL,
    cdescripcion  VARCHAR(100) NULL,
    CONSTRAINT FK_Distrito_Provincia
        FOREIGN KEY (id_provincia) REFERENCES Provincia (id_provincia)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Empresas (
    id_empresa         INT AUTO_INCREMENT NOT NULL UNIQUE,
    ccod_empresa       VARCHAR(20) NOT NULL,
    cdsc_empresa       VARCHAR(150) NULL,
    cnombre_bd         VARCHAR(100) NULL,
    cnombre_servidor   VARCHAR(100) NULL,
    id_estado          INT NULL DEFAULT 1,
    cnum_tribu         VARCHAR(20) NULL,
    csimbolo_moneda    VARCHAR(5) NULL,
    cnombre_moneda     VARCHAR(50) NULL,
    ctarifas           VARCHAR(50) NULL,
    nusuario_extra     INT NULL DEFAULT 0,
    ntienda_extra      INT NULL DEFAULT 0,
    cdepartamento      VARCHAR(10) NULL,
    cprovincia         VARCHAR(10) NULL,
    cdistrito          VARCHAR(10) NULL,
    curbanizacion      VARCHAR(100) NULL,
    cdomicilio         VARCHAR(200) NULL,
    cubigeo            VARCHAR(6) NULL,
    nenviosunat        VARCHAR(2) NULL,
    dfch_sunat         DATETIME NULL,
    ccod_cliente_emis  VARCHAR(50) NULL,
    dfch_vencimiento   DATETIME NULL,
    ctoken             LONGTEXT NULL,
    ctip_facturador    VARCHAR(50) NULL,
    dfch_crea          DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    cpais_origen       VARCHAR(50) NULL,
    cdoc               VARCHAR(20) NULL,
    cnomser            VARCHAR(100) NULL,
    PRIMARY KEY (ccod_empresa),
    CONSTRAINT FK_Empresas_Estado
        FOREIGN KEY (id_estado)        REFERENCES Estados (id_estado)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_Empresas_Departamento
        FOREIGN KEY (cdepartamento)    REFERENCES Departamento (id_departamento)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_Empresas_Provincia
        FOREIGN KEY (cprovincia)       REFERENCES Provincia (id_provincia)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_Empresas_Distrito
        FOREIGN KEY (cdistrito)        REFERENCES Distrito (id_distrito)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Menus (
    id_menu        INT AUTO_INCREMENT PRIMARY KEY,
    cdsc_menu      VARCHAR(100) NULL,
    curl_href      VARCHAR(255) NULL,
    curl_src       VARCHAR(255) NULL,
    nid_menupadre  INT NULL,
    cli_menu       VARCHAR(100) NULL,
    cul_menu       VARCHAR(100) NULL,
    nivel          VARCHAR(10) NULL,
    corden         INT NULL,
    cstatus        VARCHAR(1) NULL DEFAULT 'A',
    CONSTRAINT FK_Menus_Padre
        FOREIGN KEY (nid_menupadre) REFERENCES Menus (id_menu)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Accesos (
    id_acceso  INT AUTO_INCREMENT PRIMARY KEY,
    id_rol     INT NULL,
    id_menu    INT NULL,
    CONSTRAINT FK_Accesos_Rol
        FOREIGN KEY (id_rol)  REFERENCES Roles (id_rol)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_Accesos_Menu
        FOREIGN KEY (id_menu) REFERENCES Menus (id_menu)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Usuarios (
    id_usuario     INT AUTO_INCREMENT PRIMARY KEY,
    ccod_usuario   VARCHAR(50) NULL UNIQUE,
    cpassw         VARCHAR(100) NULL,
    cdsc_usuario   VARCHAR(150) NULL,
    id_rol         INT NULL,
    ccod_empresa   VARCHAR(20) NULL,
    cmail          VARCHAR(100) NULL,
    ctelf          VARCHAR(20) NULL,
    ccelular       VARCHAR(20) NULL,
    cdirec         VARCHAR(200) NULL,
    id_estado      INT NULL DEFAULT 1,
    dfch_crea      DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    ccod_tiend     VARCHAR(20) NULL,
    ccod_almacen   VARCHAR(20) NULL,
    ccod_caja      VARCHAR(20) NULL,
    cperm_descn    VARCHAR(50) NULL,
    ifoto          LONGBLOB NULL,
    CONSTRAINT FK_Usuarios_Empresa
        FOREIGN KEY (ccod_empresa) REFERENCES Empresas (ccod_empresa)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_Usuarios_Rol
        FOREIGN KEY (id_rol) REFERENCES Roles (id_rol)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_Usuarios_Estado
        FOREIGN KEY (id_estado) REFERENCES Estados (id_estado)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- PROCEDIMIENTOS ALMACENADOS
-- ---------------------------------------------------------------------------

DELIMITER $$

DROP PROCEDURE IF EXISTS ConsultasEmpresasPrincipal $$
CREATE PROCEDURE ConsultasEmpresasPrincipal(
    IN p_ccod_empresa VARCHAR(20),
    IN p_ctarifas VARCHAR(50),
    IN p_cpais_origen VARCHAR(50),
    IN p_cstatus VARCHAR(10)
)
BEGIN
    SELECT
        ccod_empresa,
        cdsc_empresa,
        IFNULL(cnum_tribu, '') AS Documento,
        cnombre_servidor,
        cnombre_bd,
        IFNULL(cpais_origen, '') AS Pais,
        IFNULL(ctarifas, '')     AS Tarifa,
        id_estado
    FROM Empresas
    WHERE (p_ccod_empresa = '' OR ccod_empresa LIKE CONCAT('%', p_ccod_empresa, '%'))
      AND (p_ctarifas = 'T' OR p_ctarifas = '' OR IFNULL(ctarifas, '') = p_ctarifas)
      AND (p_cpais_origen = '' OR IFNULL(cpais_origen, '') LIKE CONCAT('%', p_cpais_origen, '%'))
      AND (p_cstatus = 'T' OR p_cstatus = '' OR id_estado = CAST(p_cstatus AS SIGNED));
END $$

DROP PROCEDURE IF EXISTS sp_consultarempresas $$
CREATE PROCEDURE sp_consultarempresas()
BEGIN
    SELECT
        ccod_empresa,
        cdsc_empresa AS cdescripcion,
        IFNULL(cdoc, '')        AS cdoc,
        IFNULL(cnum_tribu, '')  AS cnum_tribu,
        IFNULL(cnomser, '')     AS cnomser,
        cnombre_bd
    FROM Empresas;
END $$

DROP PROCEDURE IF EXISTS sp_consultarroles $$
CREATE PROCEDURE sp_consultarroles()
BEGIN
    SELECT id_rol, cdsc_rol AS cdescripcion FROM Roles;
END $$

DROP PROCEDURE IF EXISTS sp_consultaestados $$
CREATE PROCEDURE sp_consultaestados()
BEGIN
    SELECT id_estado, cdsc_estado AS cdescripcion FROM Estados;
END $$

DROP PROCEDURE IF EXISTS sp_consultatipodocumento $$
CREATE PROCEDURE sp_consultatipodocumento()
BEGIN
    SELECT id_tipodocumento, cdsc_tipo_doc AS cdescripcion FROM TipoDocumento;
END $$

DROP PROCEDURE IF EXISTS sp_editarempresa $$
CREATE PROCEDURE sp_editarempresa(
    IN p_ccod_empresa VARCHAR(20),
    IN p_cdescripcion VARCHAR(200),
    IN p_cdoc VARCHAR(20),
    IN p_cnum_tribu VARCHAR(20),
    IN p_cnomser VARCHAR(100),
    IN p_cnombre_bd VARCHAR(100)
)
BEGIN
    UPDATE Empresas
    SET cdsc_empresa = p_cdescripcion,
        cdoc         = p_cdoc,
        cnum_tribu   = p_cnum_tribu,
        cnomser      = p_cnomser,
        cnombre_bd   = p_cnombre_bd
    WHERE ccod_empresa = p_ccod_empresa;
END $$

DROP PROCEDURE IF EXISTS sp_editarusuariocliente $$
CREATE PROCEDURE sp_editarusuariocliente(
    IN p_ccod_usuario VARCHAR(50),
    IN p_cdsc_usuario VARCHAR(200),
    IN p_id_rol INT,
    IN p_ccod_empresa VARCHAR(20),
    IN p_id_estado INT,
    IN p_cmail VARCHAR(100),
    IN p_ctelf VARCHAR(20),
    IN p_ccelular VARCHAR(20),
    IN p_cdirec VARCHAR(200)
)
BEGIN
    UPDATE Usuarios
    SET cdsc_usuario = p_cdsc_usuario,
        id_rol       = p_id_rol,
        ccod_empresa = p_ccod_empresa,
        id_estado    = p_id_estado,
        cmail        = p_cmail,
        ctelf        = p_ctelf,
        ccelular     = p_ccelular,
        cdirec       = p_cdirec
    WHERE ccod_usuario = p_ccod_usuario;
END $$

DROP PROCEDURE IF EXISTS sp_eliminarempresa $$
CREATE PROCEDURE sp_eliminarempresa(IN p_ccod_empresa VARCHAR(20))
BEGIN
    DELETE FROM Empresas WHERE ccod_empresa = p_ccod_empresa;
END $$

DROP PROCEDURE IF EXISTS sp_eliminarusuariocliente $$
CREATE PROCEDURE sp_eliminarusuariocliente(IN p_ccod_usuario VARCHAR(50))
BEGIN
    DELETE FROM Usuarios WHERE ccod_usuario = p_ccod_usuario;
END $$

DROP PROCEDURE IF EXISTS sp_insertarempresas $$
CREATE PROCEDURE sp_insertarempresas(
    IN p_ccod_empresa VARCHAR(20),
    IN p_cdescripcion VARCHAR(200),
    IN p_cdoc VARCHAR(20),
    IN p_cnum_tribu VARCHAR(20),
    IN p_cnomser VARCHAR(100),
    IN p_cnombre_bd VARCHAR(100)
)
BEGIN
    INSERT INTO Empresas (ccod_empresa, cdsc_empresa, cdoc, cnum_tribu, cnomser, cnombre_bd)
    VALUES (p_ccod_empresa, p_cdescripcion, p_cdoc, p_cnum_tribu, p_cnomser, p_cnombre_bd);
END $$

DROP PROCEDURE IF EXISTS sp_insertarusuarios $$
CREATE PROCEDURE sp_insertarusuarios(
    IN p_ccod_usuario VARCHAR(50),
    IN p_cdsc_usuario VARCHAR(200),
    IN p_cpassw VARCHAR(200),
    IN p_id_rol INT,
    IN p_ccod_empresa VARCHAR(20),
    IN p_id_estado INT,
    IN p_cmail VARCHAR(100),
    IN p_ctelf VARCHAR(20),
    IN p_ccelular VARCHAR(20),
    IN p_cdirec VARCHAR(200)
)
BEGIN
    INSERT INTO Usuarios
        (ccod_usuario, cdsc_usuario, cpassw, id_rol, ccod_empresa,
         id_estado, cmail, ctelf, ccelular, cdirec)
    VALUES
        (p_ccod_usuario, p_cdsc_usuario, p_cpassw, p_id_rol, p_ccod_empresa,
         p_id_estado, p_cmail, p_ctelf, p_ccelular, p_cdirec);
END $$

DROP PROCEDURE IF EXISTS sp_validarusuario $$
CREATE PROCEDURE sp_validarusuario(
    IN p_ccod_usuario VARCHAR(50),
    IN p_cpassw VARCHAR(200)
)
BEGIN
    SELECT
        U.id_usuario                        AS id_ctusu,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.id_rol                            AS rolMaster,
        U.ccod_empresa,
        IFNULL(E.cnombre_bd, '')            AS cnombre_bd,
        IFNULL(E.cnombre_servidor, '')      AS cnomser,
        IFNULL(E.cdsc_empresa, '')          AS cdescripcion,
        IFNULL(E.cnum_tribu, '')            AS cnum_tribu,
        IFNULL(E.ntienda_extra, 0)          AS ntienda_extra,
        IFNULL(E.nusuario_extra, 0)         AS nusuario_extra,
        IFNULL(E.ctarifas, '')              AS ctarifas,
        IFNULL(E.cnombre_moneda, '')        AS cnombre_moneda,
        IFNULL(E.csimbolo_moneda, '')       AS csimbolo_moneda,
        IFNULL(E.cdomicilio, '')            AS cdomicilio,
        IFNULL(E.cprovincia, '')            AS cprovincia,
        IFNULL(E.cdistrito, '')             AS cdistrito,
        IFNULL(E.cdepartamento, '')         AS cdepartamento,
        IFNULL(E.ctip_facturador, '')       AS ctip_facturador,
        E.dfch_vencimiento,
        CASE WHEN U.id_estado = 1 THEN 'Habilitado' ELSE 'Bloqueado' END AS estado,
        IFNULL(E.ccod_cliente_emis, '')     AS ccod_cliente_emis,
        IFNULL(E.ctoken, '')                AS ctoken
    FROM Usuarios U
    INNER JOIN Empresas E ON E.ccod_empresa = U.ccod_empresa
    WHERE U.ccod_usuario = p_ccod_usuario
      AND U.cpassw       = p_cpassw;
END $$

DROP PROCEDURE IF EXISTS webDatpos_buscarTarifa $$
CREATE PROCEDURE webDatpos_buscarTarifa(
    IN p_ccod_empresa VARCHAR(20),
    IN p_ctarifas VARCHAR(20),
    IN p_cpais_origen VARCHAR(50),
    IN p_cstatus VARCHAR(10)
)
BEGIN
    SELECT
        E.ccod_empresa,
        E.cdsc_empresa,
        IFNULL(E.cnum_tribu, '')   AS Documento,
        E.cnombre_servidor,
        E.cnombre_bd,
        IFNULL(E.cpais_origen, '') AS Pais,
        IFNULL(E.ctarifas, '')     AS Tarifa,
        E.id_estado
    FROM Empresas E
    WHERE (p_ccod_empresa = '' OR E.ccod_empresa = p_ccod_empresa)
      AND (p_ctarifas = 'T' OR IFNULL(E.ctarifas, '') = p_ctarifas)
      AND (p_cpais_origen = 'T' OR IFNULL(E.cpais_origen, '') = p_cpais_origen)
      AND (p_cstatus = 'T' OR E.id_estado = CAST(p_cstatus AS SIGNED));
END $$

DROP PROCEDURE IF EXISTS webDatpos_cambiarContrasena $$
CREATE PROCEDURE webDatpos_cambiarContrasena(
    IN p_ccod_usuario VARCHAR(50),
    IN p_cpassw VARCHAR(200),
    IN p_newpassw VARCHAR(200)
)
BEGIN
    DECLARE existe INT DEFAULT 0;

    SELECT COUNT(*) INTO existe
    FROM Usuarios
    WHERE ccod_usuario = p_ccod_usuario
      AND cpassw       = p_cpassw;

    IF existe > 0 THEN
        UPDATE Usuarios
        SET cpassw = p_newpassw
        WHERE ccod_usuario = p_ccod_usuario;
        SELECT 1 AS resultado;
    ELSE
        SELECT 0 AS resultado;
    END IF;
END $$

DROP PROCEDURE IF EXISTS webDatpos_cargarDepartamentos $$
CREATE PROCEDURE webDatpos_cargarDepartamentos()
BEGIN
    SELECT id_departamento, cdescripcion
    FROM Departamento
    ORDER BY cdescripcion;
END $$

DROP PROCEDURE IF EXISTS webDatpos_cargarDistritos $$
CREATE PROCEDURE webDatpos_cargarDistritos(IN p_id_provincia VARCHAR(10))
BEGIN
    SELECT id_distrito, cdescripcion
    FROM Distrito
    WHERE id_provincia = p_id_provincia
    ORDER BY cdescripcion;
END $$

DROP PROCEDURE IF EXISTS webDatpos_cargarProvincias $$
CREATE PROCEDURE webDatpos_cargarProvincias(IN p_id_departamento VARCHAR(10))
BEGIN
    SELECT id_provincia, cdescripcion
    FROM Provincia
    WHERE id_departamento = p_id_departamento
    ORDER BY cdescripcion;
END $$

DROP PROCEDURE IF EXISTS webDatpos_consultaPorCodEmpresa $$
CREATE PROCEDURE webDatpos_consultaPorCodEmpresa(
    IN p_ccod_empresa VARCHAR(20),
    IN p_cstatus VARCHAR(10)
)
BEGIN
    SELECT
        E.ccod_empresa,
        E.cdsc_empresa,
        U.ccod_usuario,
        U.cdsc_usuario,
        IFNULL(U.cdirec, '')        AS cdirec,
        IFNULL(R.cdsc_rol, '')      AS cdsc_rol,
        IFNULL(D.cdescripcion, '')  AS cdsc_departamento,
        U.id_estado,
        IFNULL(U.ccelular, '')      AS ccelular
    FROM Usuarios U
    INNER JOIN Empresas E ON U.ccod_empresa = E.ccod_empresa
    LEFT JOIN Roles R         ON U.id_rol         = R.id_rol
    LEFT JOIN Departamento D  ON E.cdepartamento  = D.id_departamento
    WHERE E.ccod_empresa = p_ccod_empresa
      AND (p_cstatus = 'T' OR p_cstatus = '' OR U.id_estado = CAST(p_cstatus AS SIGNED));
END $$

DROP PROCEDURE IF EXISTS webDatpos_consultarEmpresa $$
CREATE PROCEDURE webDatpos_consultarEmpresa(IN p_ccod_empresa VARCHAR(20))
BEGIN
    SELECT
        id_empresa,
        ccod_empresa,
        cdsc_empresa,
        IFNULL(cnum_tribu, '')      AS cnum_tribu,
        cnombre_servidor,
        cnombre_bd,
        IFNULL(csimbolo_moneda, '') AS csimbolo_moneda,
        IFNULL(cnombre_moneda, '')  AS cnombre_moneda,
        IFNULL(ctarifas, '')        AS ctarifas,
        IFNULL(nusuario_extra, 0)   AS nusuario_extra,
        IFNULL(ntienda_extra, 0)    AS ntienda_extra,
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
    FROM Empresas
    WHERE ccod_empresa = p_ccod_empresa;
END $$

DROP PROCEDURE IF EXISTS webDatpos_consultarEmpresas $$
CREATE PROCEDURE webDatpos_consultarEmpresas()
BEGIN
    SELECT
        id_empresa,
        ccod_empresa,
        cdsc_empresa,
        IFNULL(cnum_tribu, '')      AS cnum_tribu,
        cnombre_servidor,
        cnombre_bd,
        IFNULL(csimbolo_moneda, '') AS csimbolo_moneda,
        IFNULL(cnombre_moneda, '')  AS cnombre_moneda,
        IFNULL(ctarifas, '')        AS ctarifas,
        IFNULL(nusuario_extra, 0)   AS nusuario_extra,
        IFNULL(ntienda_extra, 0)    AS ntienda_extra,
        IFNULL(DATE_FORMAT(dfch_crea, '%Y-%m-%d %H:%i:%s'), '') AS dfch_crea
    FROM Empresas
    WHERE id_estado = 1;
END $$

DROP PROCEDURE IF EXISTS webDatpos_consultaUsuario $$
CREATE PROCEDURE webDatpos_consultaUsuario(IN p_ccod_usuario VARCHAR(50))
BEGIN
    SELECT
        U.id_usuario,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.cpassw,
        IFNULL(U.cdirec, '')                                      AS cdirec,
        U.id_rol,
        U.ccod_empresa,
        CAST(U.id_estado AS CHAR(10))                             AS id_estado,
        IFNULL(DATE_FORMAT(U.dfch_crea, '%Y-%m-%d %H:%i:%s'), '') AS dfch_crea,
        IFNULL(U.cmail, '')                                       AS cmail,
        IFNULL(U.ctelf, '')                                       AS ctelf,
        IFNULL(U.ccelular, '')                                    AS ccelular,
        IFNULL(E.cdsc_empresa, '')                                AS cdsc_empresa,
        IFNULL(U.ccod_tiend, '')                                  AS ccod_tiend,
        IFNULL(U.ccod_almacen, '')                                AS ccod_almacen,
        IFNULL(U.ccod_caja, '')                                   AS ccod_caja,
        IFNULL(U.cperm_descn, '')                                 AS cperm_descn
    FROM Usuarios U
    INNER JOIN Empresas E ON U.ccod_empresa = E.ccod_empresa
    WHERE U.ccod_usuario = p_ccod_usuario
      AND U.id_estado = 1;
END $$

DROP PROCEDURE IF EXISTS webDatpos_consultaUsuarios $$
CREATE PROCEDURE webDatpos_consultaUsuarios()
BEGIN
    SELECT
        U.id_usuario,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.cpassw,
        IFNULL(U.cdirec, '')                                      AS cdirec,
        CAST(U.id_rol AS CHAR(10))                                AS id_rol,
        U.ccod_empresa,
        CAST(U.id_estado AS CHAR(10))                             AS id_estado,
        IFNULL(DATE_FORMAT(U.dfch_crea, '%Y-%m-%d %H:%i:%s'), '') AS dfch_crea
    FROM Usuarios U
    WHERE U.id_estado = 1;
END $$

DROP PROCEDURE IF EXISTS webDatpos_contadorEmpresa $$
CREATE PROCEDURE webDatpos_contadorEmpresa()
BEGIN
    SELECT COUNT(*) AS cantidaTienda
    FROM Empresas
    WHERE id_estado = 1;
END $$

DROP PROCEDURE IF EXISTS webDatpos_contadorUsuario $$
CREATE PROCEDURE webDatpos_contadorUsuario()
BEGIN
    SELECT COUNT(*) AS cantidaUsuarios
    FROM Usuarios
    WHERE id_estado = 1;
END $$

DROP PROCEDURE IF EXISTS webDatpos_countUsuariosPorEmpresa $$
CREATE PROCEDURE webDatpos_countUsuariosPorEmpresa(IN p_ccod_empresa VARCHAR(20))
BEGIN
    SELECT
        E.ccod_empresa,
        E.cdsc_empresa,
        COUNT(U.id_usuario) AS TotalUsuarios
    FROM Empresas E
    LEFT JOIN Usuarios U
        ON E.ccod_empresa = U.ccod_empresa
       AND U.id_estado = 1
    WHERE (p_ccod_empresa = '' OR E.ccod_empresa = p_ccod_empresa)
    GROUP BY E.ccod_empresa, E.cdsc_empresa;
END $$

DROP PROCEDURE IF EXISTS webDatpos_editarEmpresa $$
CREATE PROCEDURE webDatpos_editarEmpresa(
    IN p_ccod_empresa VARCHAR(20),
    IN p_cdescripcion VARCHAR(150),
    IN p_cnum_tribu VARCHAR(20),
    IN p_cnombre_bd VARCHAR(100),
    IN p_cnombre_servidor VARCHAR(100),
    IN p_csimbolo_moneda VARCHAR(5),
    IN p_cnombre_moneda VARCHAR(50),
    IN p_ctarifas VARCHAR(50),
    IN p_nusuario_extra INT,
    IN p_ntienda_extra INT,
    IN p_cdepartamento VARCHAR(10),
    IN p_cdistrito VARCHAR(10),
    IN p_cprovincia VARCHAR(10),
    IN p_curbanizacion VARCHAR(100),
    IN p_cdomicilio VARCHAR(200),
    IN p_cubigeo VARCHAR(6),
    IN p_nenviosunat VARCHAR(2),
    IN p_dfch_sunat DATETIME,
    IN p_dfch_vencimiento DATETIME,
    IN p_ctoken LONGTEXT,
    IN p_ctip_facturador VARCHAR(50)
)
BEGIN
    UPDATE Empresas
    SET cdsc_empresa     = IFNULL(p_cdescripcion,     cdsc_empresa),
        cnum_tribu       = IFNULL(p_cnum_tribu,       cnum_tribu),
        cnombre_bd       = IFNULL(p_cnombre_bd,       cnombre_bd),
        cnombre_servidor = IFNULL(p_cnombre_servidor, cnombre_servidor),
        csimbolo_moneda  = IFNULL(p_csimbolo_moneda,  csimbolo_moneda),
        cnombre_moneda   = IFNULL(p_cnombre_moneda,   cnombre_moneda),
        ctarifas         = IFNULL(p_ctarifas,         ctarifas),
        nusuario_extra   = IFNULL(p_nusuario_extra,   nusuario_extra),
        ntienda_extra    = IFNULL(p_ntienda_extra,    ntienda_extra),
        cdepartamento    = IFNULL(p_cdepartamento,    cdepartamento),
        cdistrito        = IFNULL(p_cdistrito,        cdistrito),
        cprovincia       = IFNULL(p_cprovincia,       cprovincia),
        curbanizacion    = IFNULL(p_curbanizacion,    curbanizacion),
        cdomicilio       = IFNULL(p_cdomicilio,       cdomicilio),
        cubigeo          = IFNULL(p_cubigeo,          cubigeo),
        nenviosunat      = IFNULL(p_nenviosunat,      nenviosunat),
        dfch_sunat       = IFNULL(p_dfch_sunat,       dfch_sunat),
        dfch_vencimiento = IFNULL(p_dfch_vencimiento, dfch_vencimiento),
        ctoken           = IFNULL(p_ctoken,           ctoken),
        ctip_facturador  = IFNULL(p_ctip_facturador,  ctip_facturador)
    WHERE ccod_empresa = p_ccod_empresa;
END $$

DROP PROCEDURE IF EXISTS webDatpos_editarUsuarioAdmin $$
CREATE PROCEDURE webDatpos_editarUsuarioAdmin(
    IN p_ccod_usuario VARCHAR(50),
    IN p_cdsc_usuario VARCHAR(150),
    IN p_cpassw VARCHAR(100),
    IN p_cdirec VARCHAR(200),
    IN p_id_rol INT,
    IN p_ccod_empresa VARCHAR(20),
    IN p_cstatus VARCHAR(1),
    IN p_cmail VARCHAR(100),
    IN p_ctelf VARCHAR(20),
    IN p_ccelular VARCHAR(20)
)
BEGIN
    UPDATE Usuarios
    SET cdsc_usuario = p_cdsc_usuario,
        cpassw       = p_cpassw,
        cdirec       = IFNULL(p_cdirec, ''),
        id_rol       = p_id_rol,
        ccod_empresa = p_ccod_empresa,
        cmail        = IFNULL(p_cmail, ''),
        ctelf        = IFNULL(p_ctelf, ''),
        ccelular     = IFNULL(p_ccelular, ''),
        id_estado    = CASE WHEN p_cstatus = 'A' OR p_cstatus = '1' THEN 1 ELSE 0 END
    WHERE ccod_usuario = p_ccod_usuario;
END $$

DROP PROCEDURE IF EXISTS webDatpos_eliminarUsuarioAdmin $$
CREATE PROCEDURE webDatpos_eliminarUsuarioAdmin(IN p_ccod_usuario VARCHAR(50))
BEGIN
    UPDATE Usuarios
    SET id_estado = 0
    WHERE ccod_usuario = p_ccod_usuario;
END $$

DROP PROCEDURE IF EXISTS webDatpos_insertarEmpresas $$
CREATE PROCEDURE webDatpos_insertarEmpresas(
    IN p_ccod_empresa VARCHAR(20),
    IN p_cdsc_empresa VARCHAR(150),
    IN p_cnombre_bd VARCHAR(100),
    IN p_cnombre_servidor VARCHAR(100),
    IN p_cnum_tribu VARCHAR(20),
    IN p_csimbolo_moneda VARCHAR(5),
    IN p_cnombre_moneda VARCHAR(50),
    IN p_ctarifas VARCHAR(50),
    IN p_nusuario_extra INT,
    IN p_ntienda_extra INT,
    IN p_cdepartamento VARCHAR(10),
    IN p_cprovincia VARCHAR(10),
    IN p_cdistrito VARCHAR(10),
    IN p_curbanizacion VARCHAR(100),
    IN p_cdomicilio VARCHAR(200),
    IN p_cubigeo VARCHAR(6),
    IN p_nenviosunat VARCHAR(2),
    IN p_dfch_sunat DATETIME,
    IN p_ccod_cliente_emis VARCHAR(50),
    IN p_dfch_vencimiento DATETIME,
    IN p_ctoken LONGTEXT,
    IN p_ctip_facturador VARCHAR(50),
    IN p_id_estado INT,
    IN p_cpais_origen VARCHAR(50)
)
BEGIN
    INSERT INTO Empresas
        (ccod_empresa, cdsc_empresa, cnombre_bd, cnombre_servidor, id_estado, cnum_tribu,
         csimbolo_moneda, cnombre_moneda, ctarifas, nusuario_extra, ntienda_extra, cdepartamento,
         cprovincia, cdistrito, curbanizacion, cdomicilio, cubigeo, nenviosunat, dfch_sunat,
         ccod_cliente_emis, dfch_vencimiento, ctoken, ctip_facturador, dfch_crea, cpais_origen)
    VALUES
        (p_ccod_empresa, p_cdsc_empresa, p_cnombre_bd, p_cnombre_servidor, IFNULL(p_id_estado, 1),
         p_cnum_tribu, p_csimbolo_moneda, p_cnombre_moneda, p_ctarifas,
         IFNULL(p_nusuario_extra, 0), IFNULL(p_ntienda_extra, 0), p_cdepartamento,
         p_cprovincia, p_cdistrito, p_curbanizacion, p_cdomicilio, p_cubigeo, p_nenviosunat,
         p_dfch_sunat, p_ccod_cliente_emis, p_dfch_vencimiento, p_ctoken, p_ctip_facturador,
         NOW(), p_cpais_origen);
END $$

DROP PROCEDURE IF EXISTS webDatpos_insertarUsuarioAdmin $$
CREATE PROCEDURE webDatpos_insertarUsuarioAdmin(
    IN p_ccod_usuario VARCHAR(50),
    IN p_cdsc_usuario VARCHAR(150),
    IN p_cpassw VARCHAR(100),
    IN p_cdirec VARCHAR(200),
    IN p_id_rol INT,
    IN p_ccod_empresa VARCHAR(20),
    IN p_cstatus VARCHAR(1),
    IN p_cmail VARCHAR(100),
    IN p_ctelf VARCHAR(20),
    IN p_ccelular VARCHAR(20)
)
BEGIN
    INSERT INTO Usuarios
        (ccod_usuario, cdsc_usuario, cpassw, cdirec, id_rol, ccod_empresa,
         cmail, ctelf, ccelular, id_estado)
    VALUES
        (p_ccod_usuario, p_cdsc_usuario, p_cpassw, IFNULL(p_cdirec, ''), p_id_rol, p_ccod_empresa,
         IFNULL(p_cmail, ''), IFNULL(p_ctelf, ''), IFNULL(p_ccelular, ''),
         CASE WHEN p_cstatus = 'A' OR p_cstatus = '1' THEN 1 ELSE 0 END);
END $$

DROP PROCEDURE IF EXISTS webDatpos_validarUsuario $$
CREATE PROCEDURE webDatpos_validarUsuario(
    IN p_ccod_usuario VARCHAR(50),
    IN p_cpassw VARCHAR(100)
)
BEGIN
    SELECT
        U.id_usuario,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.id_rol,
        U.ccod_empresa,
        E.cdsc_empresa,
        E.cnombre_bd,
        E.cnombre_servidor
    FROM Usuarios U
    INNER JOIN Empresas E ON U.ccod_empresa = E.ccod_empresa
    WHERE U.ccod_usuario = p_ccod_usuario
      AND U.cpassw       = p_cpassw
      AND U.id_estado    = 1;
END $$

DELIMITER ;

-- =====================================================================
-- Datos semilla minimos para poder arrancar la app y hacer login.
-- (Se pueden borrar luego en produccion: TRUNCATE Usuarios; TRUNCATE Empresas;)
-- =====================================================================

-- Ubigeo de ejemplo (Lima) para que los dropdowns no esten vacios.
INSERT INTO Departamento (id_departamento, cdescripcion) VALUES
    ('15', 'LIMA'),
    ('07', 'CALLAO'),
    ('04', 'AREQUIPA')
ON DUPLICATE KEY UPDATE cdescripcion = VALUES(cdescripcion);

INSERT INTO Provincia (id_provincia, id_departamento, cdescripcion) VALUES
    ('1501', '15', 'LIMA'),
    ('0701', '07', 'CALLAO'),
    ('0401', '04', 'AREQUIPA')
ON DUPLICATE KEY UPDATE cdescripcion = VALUES(cdescripcion);

INSERT INTO Distrito (id_distrito, id_provincia, cdescripcion) VALUES
    ('150101', '1501', 'LIMA CERCADO'),
    ('150116', '1501', 'MIRAFLORES'),
    ('070101', '0701', 'CALLAO')
ON DUPLICATE KEY UPDATE cdescripcion = VALUES(cdescripcion);

-- Empresa demo. Usuarios.ccod_empresa hace FK a esta tabla, asi que tiene
-- que existir antes del INSERT en Usuarios.
INSERT INTO Empresas
    (ccod_empresa, cdsc_empresa, cnombre_bd, cnombre_servidor, id_estado,
     cnum_tribu, csimbolo_moneda, cnombre_moneda, ctarifas, nusuario_extra,
     ntienda_extra, cdepartamento, cprovincia, cdistrito, cpais_origen)
VALUES ('EMP01', 'Empresa Demo', 'dbdemo', 'localhost', 1,
        '20123456789', 'S/.', 'SOLES', 'T1', 0,
        0, '15', '1501', '150101', 'PERU')
ON DUPLICATE KEY UPDATE cdsc_empresa = VALUES(cdsc_empresa);

-- Usuario admin / admin para poder iniciar sesion al instalar.
INSERT INTO Usuarios
    (ccod_usuario, cpassw, cdsc_usuario, id_rol, ccod_empresa,
     cmail, ctelf, ccelular, cdirec, id_estado)
VALUES ('admin', 'admin', 'Administrador del Sistema', 1, 'EMP01',
        'admin@datpos.local', '01-2345678', '999999999', 'Av. Demo 123', 1)
ON DUPLICATE KEY UPDATE cdsc_usuario = VALUES(cdsc_usuario);
