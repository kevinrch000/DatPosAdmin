-- ============================================================================
-- Migracion 001: Hash de contrasenas (bcrypt)
-- ----------------------------------------------------------------------------
-- Agrega la columna `cpassw_bcrypt` a dbo.Usuarios para almacenar el hash
-- bcrypt generado por PHP (`password_hash` con PASSWORD_DEFAULT). La columna
-- legacy `cpassw` queda intacta para permitir migracion perezosa: cuando un
-- usuario se loguea con su password actual y todavia no tiene hash, el
-- backend lo hashea y lo guarda en `cpassw_bcrypt` en ese mismo login.
--
-- Tambien actualiza los SPs:
--   * webDatpos_validarUsuario        -> ya no compara password en SQL
--   * webDatpos_cambiarContrasena     -> recibe hash y solo hace UPDATE
--   * webDatpos_actualizarPasswordHash (nuevo) -> rehash perezoso
--   * webDatpos_insertarUsuarioAdmin  -> recibe hash y lo escribe en cpassw_bcrypt
--   * webDatpos_editarUsuarioAdmin    -> idem; mantiene hash actual si llega vacio
--
-- Importante: la verificacion (password_verify) ahora se hace en PHP. Los SPs
-- pueden devolver el hash al backend porque la BD admin ya esta protegida
-- detras del backend.
-- ============================================================================

USE [DatPosAdmin];
GO

-- ----------------------------------------------------------------------------
-- 1) Columna cpassw_bcrypt
-- ----------------------------------------------------------------------------
IF NOT EXISTS (
    SELECT 1
    FROM sys.columns
    WHERE object_id = OBJECT_ID('dbo.Usuarios')
      AND name      = 'cpassw_bcrypt'
)
BEGIN
    ALTER TABLE dbo.Usuarios ADD cpassw_bcrypt VARCHAR(255) NULL;
END;
GO

-- ----------------------------------------------------------------------------
-- 2) webDatpos_validarUsuario: ya no compara password
-- ----------------------------------------------------------------------------
ALTER PROCEDURE [dbo].[webDatpos_validarUsuario]
    @ccod_usuario VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        U.id_usuario,
        U.ccod_usuario,
        U.cdsc_usuario,
        U.id_rol,
        U.ccod_empresa,
        ISNULL(U.cpassw, '')         AS cpassw,
        ISNULL(U.cpassw_bcrypt, '')  AS cpassw_bcrypt,
        E.cdsc_empresa,
        E.cnombre_bd,
        E.cnombre_servidor
    FROM dbo.Usuarios U
    INNER JOIN dbo.Empresas E ON U.ccod_empresa = E.ccod_empresa
    WHERE U.ccod_usuario = @ccod_usuario
      AND U.id_estado    = 1;
END;
GO

-- ----------------------------------------------------------------------------
-- 3) webDatpos_actualizarPasswordHash (nuevo): rehash perezoso post-login
-- ----------------------------------------------------------------------------
IF OBJECT_ID('dbo.webDatpos_actualizarPasswordHash', 'P') IS NOT NULL
    DROP PROCEDURE dbo.webDatpos_actualizarPasswordHash;
GO

CREATE PROCEDURE [dbo].[webDatpos_actualizarPasswordHash]
    @ccod_usuario  VARCHAR(50),
    @cpassw_bcrypt VARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE dbo.Usuarios
       SET cpassw_bcrypt = @cpassw_bcrypt
     WHERE ccod_usuario = @ccod_usuario;
END;
GO

-- ----------------------------------------------------------------------------
-- 4) webDatpos_cambiarContrasena: solo UPDATE, verificacion de la actual va
--    en PHP. Devuelve 1 si actualiza, 0 si no encuentra el usuario.
-- ----------------------------------------------------------------------------
ALTER PROCEDURE [dbo].[webDatpos_cambiarContrasena]
    @ccod_usuario  VARCHAR(50),
    @cpassw_bcrypt VARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE dbo.Usuarios
       SET cpassw_bcrypt = @cpassw_bcrypt
     WHERE ccod_usuario = @ccod_usuario;
    SELECT CAST(@@ROWCOUNT AS INT) AS resultado;
END;
GO

-- ----------------------------------------------------------------------------
-- 5) webDatpos_insertarUsuarioAdmin: ahora recibe @cpassw_bcrypt
-- ----------------------------------------------------------------------------
ALTER PROCEDURE [dbo].[webDatpos_insertarUsuarioAdmin]
    @ccod_usuario  VARCHAR(50),
    @cdsc_usuario  VARCHAR(150),
    @cpassw_bcrypt VARCHAR(255),
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
        (ccod_usuario, cdsc_usuario, cpassw_bcrypt, cdirec, id_rol, ccod_empresa,
         cmail, ctelf, ccelular, id_estado)
    VALUES
        (@ccod_usuario, @cdsc_usuario, @cpassw_bcrypt, ISNULL(@cdirec, ''),
         @id_rol, @ccod_empresa,
         ISNULL(@cmail, ''), ISNULL(@ctelf, ''), ISNULL(@ccelular, ''),
         CASE WHEN @cstatus = 'A' OR @cstatus = '1' THEN 1 ELSE 0 END);
END;
GO

-- ----------------------------------------------------------------------------
-- 6) webDatpos_editarUsuarioAdmin: si llega @cpassw_bcrypt vacio o NULL no
--    pisa el hash actual (el form de admin NO siempre cambia password).
-- ----------------------------------------------------------------------------
ALTER PROCEDURE [dbo].[webDatpos_editarUsuarioAdmin]
    @ccod_usuario  VARCHAR(50),
    @cdsc_usuario  VARCHAR(150),
    @cpassw_bcrypt VARCHAR(255),
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
       SET cdsc_usuario  = @cdsc_usuario,
           cpassw_bcrypt = CASE
                              WHEN @cpassw_bcrypt IS NULL OR @cpassw_bcrypt = ''
                                  THEN cpassw_bcrypt
                              ELSE @cpassw_bcrypt
                           END,
           cdirec        = ISNULL(@cdirec, ''),
           id_rol        = @id_rol,
           ccod_empresa  = @ccod_empresa,
           cmail         = ISNULL(@cmail, ''),
           ctelf         = ISNULL(@ctelf, ''),
           ccelular      = ISNULL(@ccelular, ''),
           id_estado     = CASE WHEN @cstatus = 'A' OR @cstatus = '1' THEN 1 ELSE 0 END
     WHERE ccod_usuario = @ccod_usuario;
END;
GO
