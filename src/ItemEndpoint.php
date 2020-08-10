<?php
namespace Lamansky\Api;

interface ItemEndpoint extends Endpoint {
    public function get () : Responder;
    public function put () : Responder;
    public function delete () : Responder;
}
