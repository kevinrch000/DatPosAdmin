# DatPosAdmin (PHP)

Migración a **PHP + Microsoft SQL Server** del proyecto original ASP.NET
WebForms (VB.NET). Toda la capa de datos usa `pdo_sqlsrv` con el ODBC
Driver 18 for SQL Server.

## Estructura

```
php/
├── config/
│   ├── config.example.php       # plantilla (db admin + db tenant)
│   └── config.php               # tu configuración local (gitignored)
├── src/
│   ├── Database.php             # MULTI-TENANT: admin + tenant connections,
│   │                            #   selectStored / executeStored /
│   │                            #   selectStoredTenant / executeStoredTenant /
│   │                            #   executeStoredTenantReturnId /
│   │                            #   executeStoredTenantWithOutput
│   ├── Db.php                   # alias delgado a Database (compat)
│   ├── Auth.php                 # login/sesion (reemplaza Forms Auth)
│   ├── Json.php                 # respuestas {"d": ...}
│   ├── BE/                      # entidades (BEEmpresa, BEUsuario, ...)
│   ├── DA/                      # acceso a datos (DAEmpresa, DAUsuario, ...)
│   └── BL/                      # logica de negocio (BLEmpresa, ...)
└── public/                      # web root
    ├── index.php                # redirige a Account/Login.php
    ├── Site.layout.php          # equivalente a Site.Master
    ├── Account/
    │   ├── Login.php
    │   ├── ChangePassword.php
    │   └── Logout.php
    ├── Interfaces/
    │   ├── Home.php
    │   ├── ConsultaEmpresas.php
    │   ├── ConsultaUsuarios.php
    │   ├── AdministrarCompanias.php
    │   └── AdministrarUsuarios.php
    └── assets/                  # css/Scripts/Styles/Javascript copiados
```

## Requisitos

- **Microsoft SQL Server** 2016 o superior (probado con SQL Server 2022).
- **PHP 8.0+** con las extensiones:
  - `pdo`
  - `pdo_sqlsrv` y `sqlsrv` (Microsoft drivers para SQL Server)
  - `mbstring`
- **ODBC Driver 18 for SQL Server** (`msodbcsql18` en Linux).

### Instalar el driver y las extensiones (Ubuntu 22.04)

```bash
# 1. Repo de Microsoft
curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
  | sudo gpg --dearmor -o /usr/share/keyrings/microsoft.gpg
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft.gpg] \
      https://packages.microsoft.com/ubuntu/22.04/prod jammy main" \
  | sudo tee /etc/apt/sources.list.d/mssql-release.list
sudo apt-get update

# 2. ODBC driver + toolchain para compilar pecl
sudo ACCEPT_EULA=Y apt-get install -y \
    msodbcsql18 unixodbc-dev php-pear php-dev gcc g++ make

# 3. Extensiones PHP. Para PHP 8.1 usar la 5.11.x (la 5.12+ pide PHP 8.3+).
yes '' | sudo pecl install sqlsrv-5.11.1 pdo_sqlsrv-5.11.1
echo -e "extension=sqlsrv.so\nextension=pdo_sqlsrv.so" \
  | sudo tee /etc/php/8.1/cli/conf.d/30-sqlsrv.ini
```

## Setup

1. **Crear la base de datos** (`DatPosAdmin`) con tablas, FKs y SPs:

   ```bash
   sqlcmd -S localhost -U sa -P <pass> -C -i scriptsql/DatPosAdmin_mssql.sql
   ```

   El script (`DatPosAdmin_mssql.sql`) es **idempotente** y crea 10 tablas,
   31 procedimientos y 12 FOREIGN KEYS, mas datos semilla minimos
   (ubigeo de Lima/Callao/Arequipa, empresa demo `EMP01` y usuario
   `admin`/`admin`).

2. **(Opcional) Crear un login dedicado para la app**:

   ```sql
   CREATE LOGIN datpos WITH PASSWORD = 'TuPasswordSegura1!';
   USE DatPosAdmin;
   CREATE USER datpos FOR LOGIN datpos;
   ALTER ROLE db_owner ADD MEMBER datpos;
   ```

3. **Configurar credenciales**:

   ```bash
   cp php/config/config.example.php php/config/config.php
   # Editar config.php con server / dbname / user / pass.
   ```

4. **Levantar el servidor** (desarrollo):

   ```bash
   php -S localhost:8080 -t php/public
   ```

   Abrir <http://localhost:8080>. El index redirige a `Account/Login.php`.
   Login inicial: **`admin`** / **`admin`** (cambiar antes de produccion).

## Mapeo Web Methods (.aspx) -> endpoints PHP

Los `$.ajax` del frontend ahora apuntan a `Page.php?action=Method` y reciben
`{ "d": ... }` (mismo contrato que los WebMethods de ASP.NET).

| Pagina                        | Acciones                                                                                              |
|-------------------------------|-------------------------------------------------------------------------------------------------------|
| `Home.php`                    | `CantidadEmpresas`, `CantidadUsuarios`                                                                |
| `ConsultaEmpresas.php`        | `ConsultasEmpresasPrincipal`, `ConsultaUsuariosPorEmpresa`                                            |
| `ConsultaUsuarios.php`        | `ConsultasUsuariosPrincipal`, `ConsultaUsuariosPorEmpresa`                                            |
| `AdministrarCompanias.php`    | `CargarDepartamento`, `CargarProvincia`, `CargarDistrito`, `ConsultarEmpresas`, `ConsultarEmpresa`, `GrabarEmpresa`, `EliminarE` |
| `AdministrarUsuarios.php`     | `UsuariosAsociados`, `TablaEmpresas`, `ConsultarUsuarios`, `ConsultarUsuario`, `GrabarUsuario`, `Eliminar` |
| `Account/Login.php`           | POST clasico (`UserName`, `Password`)                                                                  |
| `Account/ChangePassword.php`  | `cambiar`                                                                                              |

## Foreign Keys agregadas

`scriptsql/DatPosAdmin_mssql.sql` agrega las siguientes referencias entre
tablas (no presentes en el `DatPosAdmin.sql` original):

- `Provincia.id_departamento`   → `Departamento.id_departamento`
- `Distrito.id_provincia`       → `Provincia.id_provincia`
- `Empresas.id_estado`          → `Estados.id_estado`
- `Empresas.cdepartamento`      → `Departamento.id_departamento`
- `Empresas.cprovincia`         → `Provincia.id_provincia`
- `Empresas.cdistrito`          → `Distrito.id_distrito`
- `Usuarios.ccod_empresa`       → `Empresas.ccod_empresa`
- `Usuarios.id_rol`             → `Roles.id_rol`
- `Usuarios.id_estado`          → `Estados.id_estado`
- `Accesos.id_rol`              → `Roles.id_rol`
- `Accesos.id_menu`             → `Menus.id_menu`
- `Menus.nid_menupadre`         → `Menus.id_menu` (auto-referencia)

## Notas sobre la arquitectura multi-tenant

El proyecto original tiene dos niveles de bases de datos:

1. **DatPosAdmin** (master) – almacena empresas (`Empresas.cnombre_bd`) y los
   usuarios administradores. Los SPs `webDatpos_*Admin` actuan aqui.
2. **BD por empresa** (`cnombre_bd` de cada Empresa) – almacena los usuarios
   operativos de cada empresa. Los SPs `webDatpos_insertarUsuario`,
   `webDatpos_editarUsuario`, `webDatpos_eliminarUsuario`, etc., actuan ahi.

`Database` (en `src/Database.php`) maneja ambas conexiones:

```php
// Conexion admin (lee config['db'])
$rows = Database::selectStored('webDatpos_consultaUsuarios');
$ok   = Database::executeStored('webDatpos_insertarUsuarioAdmin', [...]);

// Conexion tenant (abre conexion dinamica al server/bd de la empresa)
$tenant = (object)[
    'cnombre_servidor' => 'host:3306',
    'cnombre_bd'       => 'BD_de_empresa',
];
$rows = Database::selectStoredTenant('webDatpos_listarItems', [], $tenant);
$ok   = Database::executeStoredTenant('webDatpos_insertarUsuario', [...], $tenant);
$id   = Database::executeStoredTenantReturnId('sp_crear', [...], $tenant);
```

`DAUsuario::InsertarUsuario` / `EditarUsuario` / `EliminarUsuario` ya estan
cableados para resolver el tenant a partir de `Empresas.ccod_empresa` y
delegar al SP correspondiente en la BD hija. Si la BD hija no esta
configurada (`cnombre_servidor`/`cnombre_bd` vacios), solo persiste en
`DatPosAdmin` y reporta exito (degradacion segura).

Para que esto funcione end-to-end en produccion, las BDs hijas deben tener
los SPs `webDatpos_insertarUsuario` / `webDatpos_editarUsuario` /
`webDatpos_eliminarUsuario`. Esos SPs **no** estan en `DatPosAdmin.sql` (solo
existen en cada BD de empresa); replicarlos esta fuera del alcance de este
repo.

## Validar

```bash
# Lint PHP recursivo
find php -name "*.php" -print0 | xargs -0 -n1 php -l

# Revisar que el .sql se ejecuta sin errores
mysql -u root -p < scriptsql/DatPosAdmin_mysql.sql
```
