<?php
namespace Lamansky\Api;

class JsonResponder extends Responder {
	protected $data;

	public function __construct(int $http_status_code, array $data) {
		parent::__construct($http_status_code);
		$this->data = $data;
	}

	protected function sendResponse() {
		$this->sendResponseCode();
		header('Content-Type: application/json');
		echo json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	}
}
