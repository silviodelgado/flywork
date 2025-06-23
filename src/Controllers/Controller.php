<?php

namespace Interart\Flywork\Controllers;

/**
 * Main controller class.
 * All application controllers should be inherited from this class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
abstract class Controller
{
    protected static $instance;

    protected $need_auth = false;
    protected $is_logged = false;
    protected $need_admin = false;
    protected $is_admin = false;

    protected $errors = [];

    protected $session;
    protected $db;
    protected $defaul_filter = [];

    protected $entity;
    protected $entity_name;

    protected $mailer_settings;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        if (!defined('ROOTPATH')) {
            throw new \Exception("Required var 'ROOTPATH' not defined.");
        }

        self::$instance = &$this;

        $this->prepare_controller();
    }

    /**
     * Check if need authentication and user is logged.
     * Check if admin level is required and user is admin.
     */
    protected function prepare_controller()
    {
        if ($this->need_auth && !$this->is_logged) {
            return $this->handle_not_authenticated();
        }
        if ($this->need_admin && !$this->is_admin) {
            return $this->handle_not_administrator();
        }
    }

    /**
     * Defines application behavior if user isn't authenticated.
     *
     * @return void
     */
    abstract protected function handle_not_authenticated();

    /**
     * Defines application behavior if user isn't an administrator.
     *
     * @return void
     */
    abstract protected function handle_not_administrator();

    /**
     * Initialize properties.
     *
     * @param array $options
     *
     * @return void
     */
    public function init(array $options = [])
    {
        if (!empty($options['db_settings'])) {
            $this->db = new \Medoo\Medoo($options['db_settings']);

            if (!empty($this->entity_name)) {
                $entity_name = '\\App\\Models\\' . $this->entity_name;
                $this->entity = new $entity_name($this->db);
            }
        }

        if (!empty($options['mailer_settings'])) {
            $this->mailer_settings = $options['mailer_settings'];
        }
    }

    /**
     * Obtains all input vars sent in request.
     *
     * @return array [field => value]
     */
    protected function get_input_vars()
    {
        $request_data = '';
        $content_length = filter_input(INPUT_SERVER, 'CONTENT_LENGTH');

        if ($content_length && $content_length > 0) {
            $request_data = file_get_contents('php://input');
        }

        if (empty($request_data)) {
            $request_data = filter_input(INPUT_SERVER, 'QUERY_STRING');
        }

        $vars = [];
        parse_str($request_data, $vars);

        return $vars;
    }

    /**
     * Get Singleton instance for current Controller.
     *
     * @return Controller
     */
    public static function &instance()
    {
        return self::$instance;
    }

    /**
     * Redirects to another uri.
     *
     * @param string $path
     * @param int $http_code
     *
     * @return void
     */
    protected function redirect(string $uri, int $http_code = 0)
    {
        if (headers_sent()) {
            header_remove();
        }

        if ($http_code) {
            header('Location: ' . $uri, true, $http_code);
            exit;
        }

        header('Location: ' . $uri);
        exit;
    }

    /**
     * Native template engine.
     *
     * @param mixed $view_file Relative path to template file
     * @param mixed $view_bag Array with values to be rendered
     * @param bool $return_as_result Specifies if the return should be rendered or returned as string
     *
     * @return mixed If $return_as_result is true, returns rendered view as string, otherwise, renders HTML
     */
    protected function view(mixed $view_file = '', mixed $view_bag = [], bool $return_as_result = false)
    {
        if (gettype($view_bag) == 'boolean') {
            $return_as_result = $view_bag;
            $view_bag = null;
        }
        if (gettype($view_file) == 'array') {
            $view_bag = $view_file;
            $view_file = '';
        }
        if (empty($view_file) || $view_file == 'index') {
            $view_file = debug_backtrace()[1]['function'];
            $parts = explode('\\', debug_backtrace()[1]['class']);
            $view_file = array_pop($parts) . DIRECTORY_SEPARATOR . $view_file;
            if (!file_exists(ROOTPATH . 'Views' . DIRECTORY_SEPARATOR . $view_file . '.php')) {
                $view_file = str_replace('_', '-', $view_file);
            }
        }

        ob_start();
        $_ctrl = $this;
        if (!empty($view_bag)) {
            extract($view_bag);
        }
        $view_file_path = ROOTPATH . 'Views' . DIRECTORY_SEPARATOR . $view_file . '.php';
        require $view_file_path;
        $bodyContents = ob_get_clean();

        if ($return_as_result) {
            ob_start();
        }

        if (!isset($layout) || $layout !== false) {
            $layout = $layout ?? 'layout.default';
            $layout_file = ROOTPATH . 'Views' . DIRECTORY_SEPARATOR . $layout . '.php';
            if (!file_exists($layout_file)) {
                $layout_file = str_replace($layout, 'Shared' . DIRECTORY_SEPARATOR . $layout, $layout_file);
                if (!file_exists($layout_file)) {
                    throw new \Exception("Layout '$layout' not found.");
                }
            }
            require $layout_file;
        } else {
            echo $bodyContents;
        }

        if ($return_as_result) {
            return ob_get_clean();
        }
    }
}
