<?php
namespace Lamansky\Api;

/**
 * This class would be a good candidate for generic typing if/when PHP implements that feature.
 * @see https://wiki.php.net/rfc/generics
 */
abstract class JsonView /*<Entity>*/ {
    abstract public function render (/*Entity */$entity) : array;

    public function renderMultiple (iterable $entities) : array {
        return array_map(function($e) {
            return $this->render($e);
        }, array_values($entities));
    }

    public function single (/*Entity */$entity, int $response_code = Responder::OK) : JsonResponder {
        return new JsonResponder($response_code, $this->render($entity));
    }

    public function multiple (iterable $entities, int $response_code = Responder::OK) : JsonResponder {
        return new JsonResponder($response_code, $this->renderMultiple($entities));
    }
}
