<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{
    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
    }

    public function unique($val, $params)
    {
        $params = explode(',', $params);
        if (count($params) < 2) {
            return false;
        }

        $this->CI->form_validation->set_message('unique', sprintf($this->CI->lang->line('val_already_taken'), $params[1]));

        $where = [$params[1] => $val];
        if (count($params) > 2) {
            for ($i = 2; $i < count($params); $i += 2) {
                $where["{$params[$i]} !="] = $params[$i + 1];
            }
        }
        return $this->CI->db->limit(1)->get_where($params[0], $where)->num_rows() === 0;
    }

    public function exists($val, $params)
    {
        $params = explode(',', $params);
        if (count($params) < 2) {
            return false;
        }

        $this->CI->form_validation->set_message('exists', sprintf($this->CI->lang->line('field_is_invalid'), $params[1]));

        $where = [$params[1] => $val];
        if (count($params) > 2) {
            for ($i = 2; $i < count($params); $i += 2) {
                $where[$params[$i]] = $params[$i + 1];
            }
        }
        $this->CI->db->limit(1)->get_where($params[0], $where)->num_rows();
        return $this->CI->db->limit(1)->get_where($params[0], $where)->num_rows() > 0;
    }

    public function is_array($val, $field)
    {
        $this->CI->form_validation->set_message('is_array', sprintf($this->CI->lang->line('field_must_array'), $field));
        return !isset($_POST[$field]) || !is_array($_POST[$field]) ? false : true;
    }

    public function is_valid_url($val, $field)
    {
        $this->CI->form_validation->set_message('is_valid_url', sprintf($this->CI->lang->line('invalid_url'), $field));
        return filter_var($val, FILTER_VALIDATE_URL) !== false;
    }
}
