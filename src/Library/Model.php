<?php

namespace Interart\Flywork\Library;

/**
 * Main database model class.
 * All models should be inherited from this class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     2.0
 */
abstract class Model
{
    protected $db;
    protected $table_name;
    protected $primary_key = 'id';
    protected $default_order_by = '';
    protected $default_order_dir = 'ASC';
    protected $columns = [];
    protected $columns_readonly = [];
    protected $join_columns = [];

    /**
     * Contains the number of rows in result set.
     *
     * @var int
     */
    public $num_rows = 0;

    /**
     * Contains last executed statement result.
     *
     * @var bool
     */
    public $success = true;

    /**
     * Contains last inserted id.
     *
     * @var int
     */
    public $last_id = 0;

    /**
     * Contains query result.
     *
     * @var array
     */
    public $result = [];

    /**
     * Default constructor.
     *
     * @param Medoo $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->init();
    }

    abstract protected function init();

    abstract protected function validate();

    abstract protected function before_update();

    abstract protected function after_update();

    abstract protected function before_insert();

    abstract protected function after_insert();

    abstract protected function before_delete();

    abstract protected function after_delete();

    private function get_default_where_pk()
    {
        return [
            $this->primary_key => $this->result[$this->primary_key],
        ];
    }

    /**
     * Set resultset to entity and returns model.
     *
     * @param array $resultSet
     *
     * @return Model
     */
    protected function setResult($result = [])
    {
        $this->result = $result;
        $this->num_rows = is_array($result) && count($result)
        ? (isset($result[0]) && is_array($result[0]) ? count($result) : 1)
        : 0;

        return $this;
    }

    /**
     * Returns true if result has any row.
     *
     * @return bool
     */
    public function hasResult()
    {
        return is_array($this->result) && count($this->result) > 0;
    }

    /**
     * List all table columns in this entity.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * List all join columns used in this entity.
     *
     * @return array
     */
    public function getJoinColumns()
    {
        return $this->join_columns;
    }

    /**
     * Search in database by primary key
     * Default method.
     *
     * @param mixed $id
     * @param array $default_filters
     *
     * @return mixed
     */
    public function findById($id, array $filters = [])
    {
        $where = [
            $this->primary_key => $id,
            'deleted'          => 0,
        ];
        $where = array_merge($filters, $where);
        $result = $this->db->get($this->table_name, $this->columns, $where);

        return $this->setResult($result);
    }

    /**
     * Retrives all rows from database.
     * Default method.
     *
     * @param string $order_by
     * @param array $default_filters
     *
     * @return array
     */
    public function findAll(string $order_by = '', string $order_dir = '', array $filters = [])
    {
        $order_dir = strtoupper($order_dir);
        if (!empty($order_dir) && !in_array($order_dir, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException("Query order direction should be 'ASC' or 'DESC'.");
        }

        $where = in_array('deleted', $this->columns) ? ['deleted' => 0] : [];
        $where = array_merge($filters, $where);

        $order = empty($order_by)
        ? [$this->default_order_by => $this->default_order_dir]
        : [$order_by => ($order_dir ?? $this->default_order_dir)];

        $where['ORDER'] = $order;

        $result = $this->db->select($this->table_name, $this->columns, $where);

        return $this->setResult($result);
    }

    /**
     * Insert an instance into database.
     * Mainly used in rest requests.
     *
     * @param array $params
     * @param array $default_filters
     *
     * @return Model
     */
    public function insert(array $params, array $filters = [])
    {
        $this->before_insert();

        $data = $filters;
        foreach ($params as $key => $value) {
            if (in_array($key, $this->columns) && !in_array($key, $this->columns_readonly)) {
                $data[$key] = $value;
            }
        }
        $pdo = $this->db->insert($this->table_name, $data);
        $this->num_rows = $pdo->rowCount();
        $this->success = !empty($pdo);
        $this->last_id = $this->db->id();

        $this->after_insert();

        if ($this->num_rows) {
            $this->result = $this->FindById($this->db->id())->result;
        }

        return $this;
    }

    /**
     * Update a registry in database.
     * Mainly used in rest requests.
     *
     * @param array $params
     * @param array $default_filters
     *
     * @return Model
     */
    public function update(array $params, array $filters = [])
    {
        if (!isset($this->result[$this->primary_key])) {
            $this->num_rows = 0;

            return $this;
        }

        $this->before_update();

        $where = array_merge($filters, $this->get_default_where_pk());

        $data = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $this->columns) && !in_array($key, $this->columns_readonly)) {
                $data[$key] = $value;
            }
        }

        $pdo = $this->db->update($this->table_name, $data, $where);
        $this->num_rows = $pdo->rowCount();
        $this->success = !empty($pdo);

        $this->after_update();

        if ($this->num_rows) {
            $this->result = $this->FindById($this->result[$this->primary_key])->result;
        }

        return $this;
    }

    /**
     * Delete a registry in database.
     * Mainly used in rest requests.
     *
     * @param array $default_filters
     *
     * @return Model
     */
    public function delete(array $filters = [])
    {
        if (!isset($this->result[$this->primary_key])) {
            $this->num_rows = 0;

            return $this;
        }

        $this->before_delete();

        $where = array_merge($filters, $this->get_default_where_pk());

        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted'    => true,
        ];
        $pdo = $this->db->update($this->table_name, $data, $where);
        $this->num_rows = $pdo->rowCount();
        $this->success = !empty($pdo);

        $this->after_delete();

        if ($this->num_rows) {
            $this->result = array_merge($this->result, $data);
        }

        return $this;
    }
}
