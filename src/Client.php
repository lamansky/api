<?php
namespace Lamansky\Api;

final class Client {
    private function __construct () {}
    private function __clone () {}
    private function __sleep () {}
    private function __wakeup () {}

    public static function instance () : Client {
        static $instance;
        if (!$instance) { $instance = new static(); }
        return $instance;
    }

    public function getIp () : ?IpAddress {
        static $ip = null;
        if ($ip === null) {
            try {
                $ip = IpAddress::factory($this->getIpString());
            } catch (\Darsyn\IP\Exception\IpException $e) {
                $ip = false;
            }
        }
        return $ip ?: null;
    }

    public function getIpString () : string {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function getUserAgentString () : string {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}
