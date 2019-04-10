<?php

namespace Interart\Flywork\Controllers;

/**
 * Main restful controller class.
 * Every application rest controller shoud be inherited from this class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
abstract class RestController extends Controller
{
    /**
     * Default constructor.
     */
    public function __construct(array $options = [])
    {
        header('Content-type:application/json;charset=utf-8');

        parent::__construct($options);
    }

    protected function handle_not_authenticated()
    {
        return $this->kill('Unauthorized request', 401);
    }

    protected function handle_not_administrator()
    {
        return $this->kill('Unauthorized request', 401);
    }

    /**
     * Ends application with Error 500 and custom message.
     *
     * @param string $message
     *
     * @return void
     */
    protected function kill(string $message = 'Invalid request', int $code = 500)
    {
        //header('HTTP/1.1 500 Internal Server Error');
        header('HTTP/1.1 ' . $code);

        $this->JsonResult(false, $message);
        exit(1);
    }

    public function list()
    {
        $result = $this->entity->findAll('', '', $this->defaul_filter);

        return $this->JsonResult(true, '', ['data' => $result]);
    }

    private function get($id)
    {
        $result = $this->entity->findById($id, $this->defaul_filter);

        return $this->JsonResult(true, '', ['data' => $result]);
    }

    private function post()
    {
        $result = $this->entity->insert($this->get_input_vars(), $this->defaul_filter);

        return $this->JsonResult(true, '', ['data' => $result]);
    }

    private function put($id)
    {
        $this->entity = $this->entity->findById($id, $this->defaul_filter);
        if (empty($this->entity->num_rows)) {
            return $this->kill();
        }
        $result = $this->entity->update($this->get_input_vars(), $this->defaul_filter);

        return $this->JsonResult(true, '', ['data' => $result]);
    }

    private function delete($id)
    {
        $this->entity = $this->entity->findById($id, $this->defaul_filter);
        if (empty($this->entity->num_rows)) {
            return $this->kill();
        }
        $result = $this->entity->delete($this->defaul_filter);
        if (!$result->success) {
            return $this->kill();
        }

        return $this->JsonResult(true, '', ['data' => $result]);
    }

    /**
     * Drives call to respective method.
     *
     * @param mixed $id ID requested, if applicable
     *
     * @return void
     */
    public function _translate_call($id = null)
    {
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new \BadMethodCallException("Invalid method '{$method}'");
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
     * @param bool $success
     * @param string $message
     * @param mixed $data Array of values or a string
     *
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
        } elseif (is_string($data)) {
            $result['data'] = $data;
        }

        echo json_encode($result, ENV == 'dev' ? JSON_PRETTY_PRINT : 0);
        exit;
    }
}
