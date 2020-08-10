<?php
namespace Lamansky\Api;

class Responder {
    public const OK = 200;
    public const CREATED = 201;
    public const NO_CONTENT = 204;
    public const CLIENT_ERROR = 400;
    public const UNAUTHORIZED = 401; // = Not logged in
    public const FORBIDDEN = 403; // = Logged in but not permitted
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501; // = Coming soon

    protected $headers = [];
    protected $http_response_code;
    protected $mime_type;
    protected $content;

    public function __construct (int $http_response_code, string $mime_type = null, string $content = null) {
        $this->http_response_code = $http_response_code;
        $this->mime_type = $mime_type;
        $this->content = $content;
    }

    protected function sendResponseCode (int $http_response_code = null) : void {
        http_response_code(
            $http_response_code ?: $this->http_response_code
        );
    }

    protected function sendResponse () : void {
        $this->sendResponseCode();

        foreach ($this->headers as $header) {
            header($header);
        }

        if ($this->mime_type) {
            header('Content-Type: ' . $this->mime_type);
        }

        if ($this->content) {
            header('Content-Length: ' . strlen($this->content));
            echo $this->content;
        }
    }

    public function sendResponseAndDie () : void {
        $this->sendResponse();
        die();
    }

    public function addHeader ($header) : void {
        $this->headers[] = $header;
    }
}
