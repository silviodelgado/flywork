<?php

namespace Interart\Flywork\Controllers;

use Interart\Flywork\Library\Session;
use Interart\Flywork\Traits\AutoProperty;

/**
 * Main controller class.
 * All application controllers should be inherited from this class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
abstract class Controller
{
    use AutoProperty;

    protected static $instance;

    protected $need_auth = false;
    protected $is_logged = false;
    protected $need_admin = false;
    protected $is_admin = false;

    protected $session;
    protected $db;
    protected $defaul_filter = [];

    protected $entity;
    protected $entity_name;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        self::$instance = &$this;

        $this->session = new Session();

        $this->prepare_controller();
    }

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
     * @return void
     */
    public function init(array $options)
    {
        if (!empty($options['db_settings'])) {
            $this->db = new \Medoo\Medoo($options['db_settings']);
        }

        if (!empty($this->entity_name)) {
            $entity_name = '\\App\\Models\\' . $this->entity_name;
            $this->entity = new $entity_name($this->db);
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
     * @param integer $http_code
     * @return void
     */
    public function redirect(string $uri, int $http_code = 0)
    {
        if (headers_sent()) {
            echo '<script type="text/javascript">'
                . 'window.location.href="' . $uri . '";'
                . '</script>'
                . '<noscript>'
                . '<meta http-equiv="refresh" content="0;url=' . $uri . '" />'
                . '</noscript>';
            exit;
        }

        if ($http_code) {
            header("Location: " . $uri, true, $http_code);
            exit;
        }

        header("Location: " . $uri);
        exit;
    }

    /**
     * Native template engine.
     *
     * @param array $view_bag Array with values to be rendered
     * @param string $file_view Relative path to template file
     * @param boolean $return_as_result Specifies if the return should be rendered or returned as string
     * @return mixed If $return_as_result is true, returns rendered view as string, otherwise, renders HTML
     */
    public function view(array $view_bag = [], string $file_view = '', bool $return_as_result = false)
    {
        if (empty($file_view)) {
            $file_view = debug_backtrace()[1]['function'];
            $parts = explode("\\", debug_backtrace()[1]['class']);
            $file_view = array_pop($parts) . DIRECTORY_SEPARATOR . $file_view;
        }

        if ($return_as_result) {
            ob_start();
        }
        $_ctrl = $this;
        if (!empty($view_bag)) {
            extract($view_bag);
        }

        $file_view = ROOTPATH . 'Views' . DIRECTORY_SEPARATOR . $file_view . '.php';
        require $file_view;

        if ($return_as_result) {
            return ob_get_clean();
        }
    }

}
