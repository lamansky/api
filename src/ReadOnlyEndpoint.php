<?php
namespace Lamansky\Api;

interface ReadOnlyEndpoint extends Endpoint {
	public function get() : Responder;
}
