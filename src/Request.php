<?php
namespace Lamansky\Api;

final class Request {
	private $json_data = [];
	private $json_error = false;

	private function __construct() {
		$content_type = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? null;
		if (strpos($content_type, 'application/json') === 0) {
			$json_data = json_decode($this->getBody(), true);
			if (is_array($json_data))
				$this->json_data = $json_data;
			elseif ($json_data === null)
				// We don't do anything to $this->json_data, so it remains set to []
				$this->json_error = true;
		}
	}

	private function __clone() {}
	private function __sleep() {}
	private function __wakeup() {}

	public static function instance() {
		static $instance;
		if (!$instance) $instance = new static();
		return $instance;
	}

	public function getBody() {
		return file_get_contents('php://input');
	}

	public function getVar($var) {
		return $this->json_data[$var] ?? $_POST[$var] ?? $_GET[$var] ?? null;
	}

	public function getJsonData() {
		return $this->json_data;
	}

	public function hasMalformedJson() : bool {
		return $this->json_error;
	}
}
