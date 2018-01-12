<?php
namespace Lamansky\Api;

/**
 * Allows you to defer providing the content of the response, instead providing
 * a content generation callback.
 *
 * Useful in conjunction with e.g. `passthru()` or `readfile()`.
 *
 * @see http://php.net/manual/en/function.passthru.php
 * @see http://php.net/manual/en/function.readfile.php
 */
class DeferredResponder extends Responder {
	protected $get_content;

	public function __construct(int $http_status_code, string $mime_type, callable $get_content) {
		parent::__construct($http_status_code, $mime_type);
		$this->get_content = $get_content;
	}

	protected function sendResponse() {
		parent::sendResponse();
		echo ($this->get_content)();
	}
}
