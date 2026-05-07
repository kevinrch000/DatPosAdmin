<?php
/** Logica de negocio de login. Equivalente a BL/BLUser.vb. */

require_once __DIR__ . '/../DA/DAUser.php';

class BLUser
{
    private DAUser $da;

    public function __construct()
    {
        $this->da = new DAUser();
    }

    /**
     * Valida usuario contra `cpassw_bcrypt` (bcrypt). Si el usuario aun no
     * tiene hash (legacy plaintext en `cpassw`), cae al plaintext y, si
     * autentica, hashea perezosamente. La fila devuelta NO incluye los
     * campos de password.
     *
     * @return array<int,array<string,mixed>>
     */
    public function ValidarUsuario(string $usuario, string $clave): array
    {
        if ($usuario === '' || $clave === '') {
            return [];
        }

        $rows = $this->da->BuscarPorCodigo($usuario);
        if (!$rows) {
            return [];
        }
        $row    = $rows[0];
        $bcrypt = (string)($row['cpassw_bcrypt'] ?? '');
        $legacy = (string)($row['cpassw']        ?? '');

        $authed = false;

        if ($bcrypt !== '') {
            // Si ya hay hash bcrypt, SOLO bcrypt autentica. El fallback al
            // plaintext queda deshabilitado para evitar backdoor con la
            // password vieja despues de un cambio.
            if (password_verify($clave, $bcrypt)) {
                $authed = true;
                if (password_needs_rehash($bcrypt, PASSWORD_DEFAULT)) {
                    try {
                        $this->da->ActualizarPasswordHash(
                            $usuario,
                            password_hash($clave, PASSWORD_DEFAULT)
                        );
                    } catch (Throwable $e) {
                        error_log("[BLUser] Rehash fallo para '$usuario': " . $e->getMessage());
                    }
                }
            }
        } elseif ($legacy !== '' && hash_equals($legacy, $clave)) {
            // Solo se llega aca cuando el usuario nunca migro: cpassw_bcrypt
            // vacio. Hasheamos perezosamente y limpiamos legacy.
            try {
                $this->da->ActualizarPasswordHash(
                    $usuario,
                    password_hash($clave, PASSWORD_DEFAULT)
                );
            } catch (Throwable $e) {
                error_log("[BLUser] Migracion lazy fallo para '$usuario': " . $e->getMessage());
            }
            $authed = true;
        }

        if (!$authed) {
            return [];
        }

        unset($row['cpassw'], $row['cpassw_bcrypt']);
        return [$row];
    }
}
