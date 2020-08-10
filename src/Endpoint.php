<?php
namespace Lamansky\Api;

interface Endpoint {
    public function getRoutePattern () : string;
}
