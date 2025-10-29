<?php

namespace App\Utils;

class UserSession extends Session
{
    // Tiempo de inactividad (segundos) tras el cual se cierra la sesión
    private const INACTIVITY_TIMEOUT = 1800; // 30 minutos

    private const KEY_USER_ID = 'user_id';
    private const KEY_LAST_ACTIVITY = 'last_activity';
    private const KEY_FINGERPRINT = 'fingerprint';


    public function __construct()
    {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        ini_set('session.use_strict_mode', '1');

        parent::__construct();

        if ($this->exist(self::KEY_LAST_ACTIVITY) && time() - $this->get(self::KEY_LAST_ACTIVITY) > 300) {
            session_regenerate_id(true);
            $this->set(self::KEY_LAST_ACTIVITY, time());
        }
    }


    public function login($userId): void
    {
        session_regenerate_id(true);

        $this->set(self::KEY_USER_ID, $userId);
        $this->set(self::KEY_LAST_ACTIVITY, time());
        $this->set(self::KEY_FINGERPRINT, $this->makeFingerprint());
    }


    public function isLoggedIn(): bool
    {
        if (!$this->exist(self::KEY_USER_ID)) {
            return false;
        }

        $last = $this->get(self::KEY_LAST_ACTIVITY, 0);
        if ($last + self::INACTIVITY_TIMEOUT < time()) {
            $this->logout();
            return false;
        }

        $stored = $this->get(self::KEY_FINGERPRINT);
        if (!is_string($stored) || !hash_equals($stored, $this->makeFingerprint())) {
            $this->logout();
            return false;
        }

        $this->set(self::KEY_LAST_ACTIVITY, time());
        return true;
    }

    public function getUser(): ?int
    {
        return $this->get(self::KEY_USER_ID);
    }


    public function logout(): void
    {
        $this->remove(self::KEY_USER_ID);
        $this->remove(self::KEY_FINGERPRINT);
        $this->remove(self::KEY_LAST_ACTIVITY);

        // destruir sesión y cookie
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }


    private function makeFingerprint(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // tomar sólo los primeros 3 octetos para no romper en redes móviles (Ej: "192.168.1")
        $ipParts = explode('.', $ip);
        $shortIp = count($ipParts) >= 3 ? "{$ipParts[0]}.{$ipParts[1]}.{$ipParts[2]}" : $ip;

        return hash('sha256', $ua . '|' . $shortIp);
    }
}
