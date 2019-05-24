<?php
class MY_Model extends CI_Model
{
    protected $table_name = null;
    protected $primary_key = null;
    protected $primary_filter = 'intval';
    protected $order_by = null;
    protected $created_at = false;
    protected $updated_at = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function all()
    {
        return $this->db->get($this->table_name)->result();
    }

    public function get($id = null, $method = 'row')
    {
        if ($id != null) {
            $filter = $this->primary_filter;
            $id = $filter($id);
            $this->db->where($this->primary_key, $id);
        }

        return $this->db->get($this->table_name)->$method();
    }

    public function get_by($where, $method = 'result')
    {
        $this->db->where((array) $where);
        return $this->get(null, $method);
    }

    public function save($data, $id = null)
    {
        $data = (array) $data;

        // Set created_at
        if ($this->created_at == true && $id) {
            $data['created_at'] = date('Y-m-d H:i:s', NOW());
        }

        // Set created_at
        if ($this->updated_at == true) {
            $data['updated_at'] = date('Y-m-d H:i:s', NOW());
        }

        if ($id === null) {
            !isset($data[$this->primary_key]) || $data[$this->primary_key] = null;
            $this->db->set($data);
            $this->db->insert($this->table_name);
            $id = $this->db->insert_id();
        } else {
            $filter = $this->primary_filter;
            $id = $filter($id);
            $this->db->set($data);
            $this->db->where($this->primary_key, $id);
            $this->db->update($this->table_name);
        }

        return $id;
    }

    public function save_by($data, $conditionArr = [])
    {
        // Set created_at
        if ($this->updated_at == true) {
            $data['updated_at'] = date('Y-m-d H:i:s', NOW());
        }

        $this->db->set($data);
        $this->db->where($conditionArr);
        $this->db->update($this->table_name);

        return true;
    }

    public function delete($id)
    {
        $filter = $this->primary_filter;

        if (!$id = $filter($id)) {
            return false;
        }

        $this->db->where($this->primary_key, $id);
        $this->db->limit(1);
        $this->db->delete($this->table_name);
        return true;
    }

    public function delete_by($where)
    {
        $this->db->where($where);
        $this->db->delete($this->table_name);
        return true;
    }

    public function pluck($where, $column)
    {
        $this->db->select("GROUP_CONCAT({$column}) AS col");
        $this->db->where($where);
        $result = $this->get();
        return is_null($result->col) ? [] : explode(',', $result->col);
    }
}
