<?php
/**
 * SecurityHelper - Funciones de seguridad reutilizables
 * - CSRF token generation and validation
 * - Rate limiting
 * - Password validation
 */

class SecurityHelper
{
    /**
     * Genera o retorna el CSRF token de la sesión
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida que el CSRF token sea correcto
     * Retorna true si es válido, false en caso contrario
     */
    public static function validateCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Obtiene el token CSRF desde POST o GET
     */
    public static function getCsrfTokenFromInput(): string
    {
        return trim($_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '');
    }

    /**
     * Valida CSRF y redirige si es inválido
     * Útil para usar en controladores
     */
    public static function checkCsrfOrDie(string $redirectUrl = 'index.php?c=home&a=index'): void
    {
        $token = self::getCsrfTokenFromInput();
        if (!self::validateCsrfToken($token)) {
            header("HTTP/1.1 403 Forbidden");
            header("Location: " . $redirectUrl . "&error=" . urlencode("Solicitud inválida (CSRF)"));
            exit();
        }
    }

    // ====== RATE LIMITING ======

    /**
     * Registra un intento fallido para un identificador (IP, email, user_id, etc.)
     * $key: identificador único (ej: "login_192.168.1.1" o "forgot_email@example.com")
     * $maxAttempts: máximo de intentos permitidos
     * $windowSeconds: ventana de tiempo en segundos
     */
    public static function recordFailedAttempt(string $key, int $maxAttempts = 5, int $windowSeconds = 900): bool
    {
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }

        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 0,
                'first_attempt_time' => time(),
            ];
        }

        $data = &$_SESSION['rate_limit'][$key];
        $now = time();
        $elapsed = $now - $data['first_attempt_time'];

        // Si ya pasó la ventana, reiniciar contador
        if ($elapsed > $windowSeconds) {
            $data['attempts'] = 1;
            $data['first_attempt_time'] = $now;
            return true; // Intento permitido
        }

        // Si no pasó, incrementar
        $data['attempts']++;

        // Devolver si está dentro del límite
        return $data['attempts'] <= $maxAttempts;
    }

    /**
     * Obtiene el número de intentos restantes
     */
    public static function getRemainingAttempts(string $key, int $maxAttempts = 5, int $windowSeconds = 900): int
    {
        if (!isset($_SESSION['rate_limit'][$key])) {
            return $maxAttempts;
        }

        $data = $_SESSION['rate_limit'][$key];
        $now = time();
        $elapsed = $now - $data['first_attempt_time'];

        // Si pasó la ventana, reiniciar
        if ($elapsed > $windowSeconds) {
            return $maxAttempts;
        }

        // Retornar intentos restantes
        return max(0, $maxAttempts - $data['attempts']);
    }

    /**
     * Obtiene tiempo restante (en segundos) para que se reinicie el rate limit
     */
    public static function getTimeUntilReset(string $key, int $windowSeconds = 900): int
    {
        if (!isset($_SESSION['rate_limit'][$key])) {
            return 0;
        }

        $data = $_SESSION['rate_limit'][$key];
        $now = time();
        $elapsed = $now - $data['first_attempt_time'];
        $remaining = $windowSeconds - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Limpia el rate limit para una clave
     */
    public static function clearRateLimit(string $key): void
    {
        if (isset($_SESSION['rate_limit'][$key])) {
            unset($_SESSION['rate_limit'][$key]);
        }
    }

    /**
     * Obtiene la IP del cliente
     */
    public static function getClientIp(): string
    {
        // Orden de verificación para diferentes tipos de proxy
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            // CloudFlare
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Proxy estándar
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '';
        }
    }
}
?>
