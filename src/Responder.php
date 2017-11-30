<?php
namespace Lamansky\Api;

class Responder {
	const OK = 200;
	const CREATED = 201;
	const NO_CONTENT = 204;
	const CLIENT_ERROR = 400;
	const UNAUTHORIZED = 401; // = Not logged in
	const FORBIDDEN = 403; // = Logged in but not permitted
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501; // = Coming soon

	protected $headers = [];
	protected $http_response_code;
	protected $mime_type;
	protected $content;

	public function __construct(int $http_response_code, string $mime_type=null, string $content=null) {
		$this->http_response_code = $http_response_code;
		$this->mime_type = $mime_type;
		$this->content = $content;
	}

	protected function sendResponseCode(int $http_response_code=null) {
		http_response_code(
			$http_response_code ?: $this->http_response_code
		);
	}

	protected function sendResponse() {
		$this->sendResponseCode();

		foreach ($this->headers as $header) {
			header($header);
		}

		if ($this->mime_type && $this->content) {
			header('Content-Type: ' . $this->mime_type);
			header('Content-Length: ' . strlen($this->content));
			echo $this->content;
		}
	}

	public function sendResponseAndDie() {
		$this->sendResponse();
		die();
	}

	public function addHeader($header) {
		$this->headers[] = $header;
	}
}
