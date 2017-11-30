<?php
namespace Lamansky\Api;

class FileResponder extends Responder {
	protected $path;
	protected $mime_type;
	protected $not_found_responder;

	public function __construct(string $path, string $mime_type, Responder $not_found=null) {
		parent::__construct(Responder::OK);
		$this->path = $path;
		$this->mime_type = $mime_type;
		$this->not_found_responder = $not_found ?: new Responder(Responder::NOT_FOUND);
	}

	protected function fileExists() {
		return file_exists($this->path) && is_file($this->path);
	}

	protected function sendResponse() {
		if (!$this->fileExists()) {
			$this->not_found_responder->sendResponse();
			return;
		}

		$this->sendResponseCode();
		header('Content-Type: ' . $this->mime_type);
		@readfile($this->path);
	}
}
