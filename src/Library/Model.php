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
    protected $num_rows = 0;
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
        $this->num_rows = is_array($result) ? count($result) : 0;

        return $this;
    }

    /**
     * Shows the number of rows in result set.
     *
     * @return int
     */
    public function numRows()
    {
        return $this->num_rows;
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
    public function findById($id, array $default_filters = [])
    {
        $where = [
            $this->primary_key => $id,
            'deleted'          => 0,
        ];
        $where = array_merge($default_filters, $where);
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
    public function findAll(string $order_by = '', string $order_dir = '', array $default_filters = [])
    {
        $order_dir = strtoupper($order_dir);
        if (!empty($order_dir) && !in_array($order_dir, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException("Query order direction should be 'ASC' or 'DESC'.");
        }

        $where = in_array('deleted', $this->columns) ? ['deleted' => 0] : [];
        $where = array_merge($default_filters, $where);

        $order = empty($order_by)
        ? [$this->default_order_by => $this->default_order_dir]
        : [$order_by => ($order_dir ?? $this->default_order_dir)];

        $result = $this->db->select($this->table_name, $this->columns, $where, $order);

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
    public function insert(array $params, array $default_filters = [])
    {
        $data = $default_filters;
        foreach ($params as $key => $value) {
            if (in_array($key, $this->columns) && !in_array($key, $this->columns_readonly)) {
                $data[$key] = $value;
            }
        }
        $pdo = $this->db->insert($this->table_name, $data);
        $this->num_rows = $pdo->rowCount();

        if ($this->num_rows) {
            return $this->FindById($this->db->id());
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
    public function update(array $params, array $default_filters = [])
    {
        if (!isset($this->result[$this->primary_key])) {
            $this->num_rows = 0;

            return $this;
        }

        $where = [$this->primary_key => $this->result[$this->primary_key]];
        $where = array_merge($default_filters, $where);

        $data = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $this->columns) && !in_array($key, $this->columns_readonly)) {
                $data[$key] = $value;
            }
        }
        $pdo = $this->db->update($this->table_name, $data, $where);
        $this->num_rows = $pdo->rowCount();

        if ($this->num_rows) {
            return $this->FindById($this->result[$this->primary_key]);
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
    public function delete(array $default_filters = [])
    {
        if (!isset($this->result[$this->primary_key])) {
            $this->num_rows = 0;

            return $this;
        }

        $where = [$this->primary_key => $this->result[$this->primary_key]];
        $where = array_merge($default_filters, $where);

        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted'    => true,
        ];
        $pdo = $this->db->update($this->table_name, $data, $where);
        $this->num_rows = $pdo->rowCount();

        if ($this->num_rows) {
            $this->result = array_merge($this->result, $data);
        }

        return $this;
    }
}
