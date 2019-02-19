<?php

namespace Interart\Flywork;

use Interart\Flywork\Controllers\Controller;

/**
 * Application main class.
 * It manages all requests, call system loaders and prepare application.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */

final class Kernel
{
    private $routes = [
        'rest/([a-z\-]+)/?([0-9]+)?'        => 'rest/$1/_translate_call/$2',
        'rest/([a-z\-]+)/([a-z\-]+)/?(.*)?' => 'rest/$1/$2/$3',
    ];
    private $default_route = [
        'controller' => 'Home',
        'action'     => 'index',
    ];
    private $db_settings = [];

    private $request_uri;
    private $request_path;
    private $method;
    private $controller_name;
    private $action_name;
    private $route_parts = [];

    /**
     * Default constructor.
     *
     * @param array $settings
     * @return void
     */
    public function __construct(array $settings = [])
    {
        $this->init($settings);
    }

    /**
     * Initialize vars.
     *
     * @return void
     */
    private function init(array $settings = [])
    {
        $this->request_uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $this->request_path = trim(explode('?', $this->request_uri)[0], '/');
        $this->method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

        if (!empty($settings['default_route'])) {
            if (!empty($settings['default_route']['controller'])) {
                $this->default_route['controller'] = $settings['default_route']['controller'];
            }

            if (!empty($settings['default_route']['action'])) {
                $this->default_route['action'] = $settings['default_route']['action'];
            }
        }

        if (!empty($settings['custom_routes'])) {
            $this->routes = array_merge($this->routes, $settings['custom_routes']);
        }

        if (!empty($settings['database_entry'])) {
            $this->db_settings = $settings['database'][$settings['database_entry']];
        }

    }

    /**
     * Translates request path to custom route, if defined.
     *
     * @return void
     */
    private function translate_route()
    {
        if (empty($this->request_path)) {
            return;
        }

        foreach ($this->routes as $key => $value) {
            $regex = '/^' . str_replace('/', '\/', $key) . '$/';
            if (preg_match($regex, trim($this->request_path, '/'))) {
                $this->request_path = preg_replace($regex, $value, $this->request_path);
                break;
            }
        }

        $this->request_path = trim($this->request_path, '/');
    }

    /**
     * Parse request path to group [Controller/ActionName].
     *
     * @return void
     */
    private function parse_route_parts()
    {
        if (empty($this->request_path)) {
            $this->controller_name = $this->default_route['controller'];
            $this->action_name = $this->default_route['action'];
            return;
        }

        $this->route_parts = explode('/', $this->request_path);
        $this->controller_name = str_replace('-', '_', ucfirst(array_shift($this->route_parts)));
        if (is_dir(ROOTPATH . 'Controllers' . DIRECTORY_SEPARATOR . $this->controller_name)) {
            if (empty($this->route_parts)) {
                throw new \BadMethodCallException($this->controller_name);
            }
            $this->controller_name .= '\\' . str_replace('-', '_', ucfirst(array_shift($this->route_parts)));
        }
        $this->action_name = str_replace('-', '_', (count($this->route_parts) ? array_shift($this->route_parts) : $this->default_route['action']));

        //$this->action_name = (($this->method != 'GET') ? strtolower($this->method) . '_' : '') . $this->action_name;
    }

    /**
     * Validate if request Controller and Action are valid.
     *
     * @param Controller $controller_obj Instance of Controller class (or derived from it)
     * @return void
     */
    private function validate_route_parts(Controller $controller_obj)
    {
        if (!method_exists($controller_obj, $this->action_name)) {
            throw new \BadMethodCallException($this->action_name);
        }
        if (!is_callable([$controller_obj, $this->action_name])) {
            throw new \BadMethodCallException($this->action_name);
        }
    }

    /**
     * Runs application config, load and execution.
     *
     * @return void
     */
    public function run()
    {
        $this->translate_route();

        $this->parse_route_parts();

        $options = [
            'db_settings' => $this->db_settings,
        ];

        $controller_name = '\\App\\Controllers\\' . $this->controller_name;
        // throws Error if class not found
        $controller = new $controller_name();
        $controller->init($options);

        $this->validate_route_parts($controller);

        // throws ArgumentCountError if is wrong parameter count
        if (empty($this->route_parts)) {
            call_user_func([$controller, $this->action_name]);
            return;
        }
        
        call_user_func_array([$controller, $this->action_name], $this->route_parts);
    }

    /**
     * Shows default Error 404 message.
     *
     * @return void
     */
    public static function error404()
    {
        header('HTTP/1.1 404 Not Found.');
        echo '<h1>Error 404 Not Found</h1>The page that you have requested could not be found.';
        exit(1);
    }

    /**
     * Shows default Error 500 message.
     *
     * @return void
     */
    public static function error500()
    {
        header('HTTP/1.1 500 Internal Server Error.');
        echo '<h1>Error 500 Internal Server Error</h1>An uncaught error has occurred.';
        exit(1);
    }

    /**
     * Shows default Error 503 message.
     *
     * @return void
     */
    public static function error503()
    {
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo '<h1>Error 503 Service Unavailable</h1>The application environment is not set correctly.';
        exit(1);
    }
}
