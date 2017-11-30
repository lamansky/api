<?php
namespace Lamansky\Api;
use Darsyn\IP\IP;
use Darsyn\IP\InvalidIpAddressException;

final class Client {
	private function __construct() {}
	private function __clone() {}
	private function __sleep() {}
	private function __wakeup() {}

	public static function instance() {
		static $instance;
		if (!$instance) $instance = new static();
		return $instance;
	}

	public function getIp() {
		static $ip = null;
		if ($ip === null) {
			try {
				$ip = new IP($this->getIpString());
			} catch (InvalidIpAddressException $e) {
				$ip = false;
			}
		}
		return $ip ?: null;
	}

	public function getIpString() : string {
		return $_SERVER['REMOTE_ADDR'] ?? '';
	}

	public function getUserAgentString() : string {
		return $_SERVER['HTTP_USER_AGENT'] ?? '';
	}
}
