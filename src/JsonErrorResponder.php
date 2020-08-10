<?php
namespace Lamansky\Api;

class JsonErrorResponder extends JsonResponder {
    public function __construct (int $http_status_code, string $error_id, string $details = null) {
        $data = ['error' => $error_id];
        if ($details) { $data['errorDetails'] = $details; }
        parent::__construct($http_status_code, $data);
    }
}
