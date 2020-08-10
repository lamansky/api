<?php
namespace Lamansky\Api;

interface CollectionEndpoint extends Endpoint {
    public function get () : Responder;
    public function post () : Responder;
}
