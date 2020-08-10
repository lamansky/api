<?php
namespace Lamansky\Api;
use AltoRouter;
use ReflectionMethod;

class Api {
    protected $router;
    protected $request;
    protected $error_handler;

    protected const DEFAULT_VERBS = ['GET', 'POST', 'PUT', 'DELETE'];

    public function __construct (string $base_path = null,
                                 callable $error_handler = null,
                                 array $extra_verbs = null
                                 ) {
        $this->router = new AltoRouter([], $base_path);
        $this->request = Request::instance();
        $this->error_handler = $error_handler;
        $this->verbs = array_merge(static::DEFAULT_VERBS, $extra_verbs ?: []);
    }

    public function registerEndpoint (Endpoint $endpoint) : void {
        $route = $endpoint->getRoutePattern();
        foreach ($this->verbs as $verb) {
            $method = strtolower($verb);
            $this->router->map(
                $verb,
                $route,
                method_exists($endpoint, $method)
                  ? [$endpoint, $method]
                  : null
            );
        }
    }

    public function getResponder () : Responder {
        $route_match = $this->router->match();

        if (!$route_match) {
            return new Responder(Responder::NOT_FOUND);
        }

        $route_target = $route_match['target'];
        $route_params = $route_match['params'];

        if (!$route_target) {
            return new Responder(Responder::METHOD_NOT_ALLOWED);
        }

        list($endpoint, $method) = $route_target;

        if ($method !== 'get' && $this->request->hasMalformedJson()) {
            return $this->getErrorResponder('malformed_json');
        }

        $method_reflection = new ReflectionMethod($endpoint, $method);

        $method_args = [];
        foreach ($method_reflection->getParameters() as $parameter) {
            $param_names = [];
            $param_names['php'] = $parameter->getName();
            $param_names['oneword'] = strtolower(str_replace('_', '', $param_names['php']));

            $camelcase = str_replace(' ', '', ucwords(str_replace('_', ' ', $param_names['php'])));
            $camelcase[0] = strtolower($camelcase[0]); // Lowercase just the first letter.
            $param_names['camelcase'] = $camelcase;

            $param_names = array_unique(array_values($param_names));

            $found_param = false;

            foreach ($param_names as $param_name) {
                if (isset($route_params[$param_name])) {
                    $method_arg = $route_params[$param_name];
                    $found_param = true;
                    break;
                }
            }

            if (!$found_param) {
                foreach ($param_names as $param_name) {
                    $var = $this->request->getVar($param_name);

                    if ($var !== null) {
                        $method_arg = $var;
                        $found_param = true;
                        break;
                    }
                }
            }

            if ($found_param) {
                if ($parameter->hasType()) {
                    if (!settype($method_arg, (string)$parameter->getType())) {
                        $method_arg = $parameter->getDefaultValue();
                    }
                }

                $method_args[] = $method_arg;
            } else {
                // We can assume all parameters have default values, because
                // they need them in order to match the interface signature.
                $method_args[] = $parameter->getDefaultValue();
            }
        }

        try {
            return call_user_func_array($route_target, $method_args);
        } catch (\Throwable $e) {
            return $this->getErrorResponder($e);
        }
    }

    protected function getErrorResponder ($exception) : Responder {
        if (is_callable($this->error_handler)) {
            $error_handler = $this->error_handler;
            $responder = $error_handler($exception);
            if ($responder instanceof Responder) { return $responder; }
        }

        return new JsonErrorResponder(
            Responder::SERVER_ERROR,
            is_string($exception) ? $exception : 'unhandled_exception'
        );
    }
}
