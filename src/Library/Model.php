<?php

namespace Interart\Flywork\Library;

/**
 * Main database model class.
 * All models should be inherited from this class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 * @version     2.0
 */
abstract class Model
{
    protected $db;
    protected $table_name;
    protected $primary_key;
    protected $default_order_by;
    protected $columns = [];

    public $result = [];
    public $rows = 0;

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

    /**
     * Returns true if result has any row
     *
     * @return bool
     */
    public function HasResult()
    {
        return count($this->result) > 0;
    }

    /**
     * Search in database by primary key
     * Default method
     *
     * @param mixed $id
     * @param array $default_filters
     * @return mixed
     */
    public function FindById($id, array $default_filters = [])
    {
        $where = [
            $this->primary_key => $id,
            'deleted'          => 0,
        ];
        $where = array_merge($default_filters, $where);
        $this->result = $this->db->get($this->table_name, $this->columns, $where);
        $this->rows = count($this->result) ? 1 : 0;
        return $this;
    }

    /**
     * Search all occurrences in database.
     * Default method
     *
     * @param string $order_by
     * @param array $default_filters
     * @return array
     */
    public function FindAll(string $order_by = '', array $default_filters = [])
    {
        $where = ['deleted' => 0];
        $where = array_merge($default_filters, $where);
        $order = ['ORDER' => $order_by ?? $this->default_order_by];
        $this->result = $this->db->select($this->table_name, $this->columns, $where, $order);
        $this->rows = count($this->result);
        return $this;
    }

    /**
     * Insert an instance into database.
     * Mainly used in rest requests
     *
     * @param array $params
     * @param array $default_filters
     * @return Model
     */
    public function Insert(array $params, array $default_filters = [])
    {
        $data = $default_filters;
        foreach ($params as $key => $value) {
            if (in_array($key, $this->columns)) {
                $data[$key] = $value;
            }
        }
        $pdo = $this->db->insert($this->table_name, $data);
        $this->rows = $pdo->rowCount();

        if ($this->rows) {
            return $this->FindById($this->result[$this->primary_key]);
        }

        return $this;
    }

    /**
     * Update a registry in database.
     * Mainly used in rest requests
     *
     * @param array $params
     * @param array $default_filters
     * @return Model
     */
    public function Update(array $params, array $default_filters = [])
    {
        if (!isset($this->result[$this->primary_key])) {
            $this->rows = 0;
            return $this;
        }

        $where = [$this->primary_key => $this->result[$this->primary_key]];
        $where = array_merge($default_filters, $where);

        $data = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $this->columns)) {
                $data[$key] = $value;
            }
        }
        $pdo = $this->db->update($this->table_name, $data, $where);
        $this->rows = $pdo->rowCount();

        if ($this->rows) {
            return $this->FindById($this->result[$this->primary_key]);
        }

        return $this;
    }

    /**
     * Delete a registry in database.
     * Mainly used in rest requests
     *
     * @param array $default_filters
     * @return Model
     */
    public function Delete(array $default_filters = [])
    {
        if (!isset($this->result[$this->primary_key])) {
            $this->rows = 0;
            return $this;
        }

        $where = [$this->primary_key => $this->result[$this->primary_key]];
        $where = array_merge($default_filters, $where);
        
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted'    => true,
        ];
        $pdo = $this->db->update($this->table_name, $data, $where);
        $this->rows = $pdo->rowCount();

        if ($this->rows) {
            $this->result = array_merge($this->result, $data);
        }

        return $this;
    }
}
