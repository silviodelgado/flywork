<?php

namespace Interart\Flywork\Controllers;

/**
 * Main restful controller class.
 * Every application rest controller shoud be inherited from this class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
abstract class RestController extends Controller
{
    protected $order_by = [];

    /**
     * Default constructor.
     */
    public function __construct()
    {
        header('Content-type:application/json;charset=utf-8');

        parent::__construct();
    }

    protected function handle_not_authenticated()
    {
        return $this->kill('Unauthorized request1', 401);
    }

    /**
     * Ends application with Error 500 and custom message.
     *
     * @param string $message
     * @return void
     */
    protected function kill(string $message = 'Invalid request', int $code = 500)
    {
        //header('HTTP/1.1 500 Internal Server Error');
        header("HTTP/1.1 " . $code);

        $this->JsonResult(false, $message);
        die;
    }

    private function get($id)
    {
        $result = $this->entity->FindById($id, $this->defaul_filter);
        return $this->JsonResult(true, '', ['data' => $result]);
    }

    function list() {

        $result = $this->entity->FindAll('', $this->defaul_filter);
        return $this->JsonResult(true, '', ['data' => $result]);
    }

    private function post()
    {
        $result = $this->entity->Insert($this->get_input_vars(), $this->defaul_filter);
        return $this->JsonResult(true, '', ['data' => $result]);
    }

    private function put($id)
    {
        $this->entity = $this->entity->FindById($id, $this->defaul_filter);
        if (empty($this->entity->rows)) {
            return $this->kill();
        }
        $result = $this->entity->Update($this->get_input_vars(), $this->defaul_filter);
        return $this->JsonResult(true, '', ['data' => $result]);
    }

    private function delete($id)
    {
        $this->entity = $this->entity->FindById($id, $this->defaul_filter);
        if (empty($this->entity->rows)) {
            return $this->kill();
        }
        $result = $this->entity->Delete($this->defaul_filter);
        if (!$result->rows) {
            return $this->kill();
        }

        return $this->JsonResult(true, '', ['data' => $result]);
    }

    public function _translate_call($id = null)
    {
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new \BadMethodCallException($action);
        }
        if ($method == 'GET' && empty($id)) {
            return $this->list();
        }

        $action = strtolower($method);
        $this->$action($id);
    }

    /**
     * Prints a json object with known structure for AJAX responses.
     *
     * @param boolean $success
     * @param string $message
     * @param mixed $data Array of values or a string
     * @return void
     */
    protected function JsonResult(bool $success, string $message, $data = null)
    {
        $result = [
            'success' => $success,
            'message' => $message,
        ];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $result[$key] = $value;
            }
        } else if (is_string($data)) {
            $result['data'] = $data;
        }

        echo json_encode($result, ENV == 'dev' ? JSON_PRETTY_PRINT : 0);
        exit(1);
    }
}
