<?php
/**
 * Helpers de respuesta JSON. ASP.NET WebMethods envuelven la respuesta en
 * `{ "d": ... }` y los `$.ajax` del frontend leen `response.d`. Mantenemos el
 * mismo contrato para no tocar la mayoria del JS.
 */

class Json
{
    /**
     * @param mixed $data
     */
    public static function respond($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['d' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error(string $message, int $status = 500): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['Message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Lee el body JSON de un POST. ASP.NET WebMethods reciben los argumentos
     * como JSON; mantenemos el mismo formato.
     *
     * @return array<string,mixed>
     */
    public static function readBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}
