<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Navigation extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('navigation_m');
    }

    public function index()
    {
        $this->load_view('navigation/index');
    }

    public function listing()
    {
        echo $this->datatables->select('id, name, parent_id, display_order, status')
            ->from('navigations')
            ->generate();

    }

    public function create()
    {
        $this->db->select('id, name');
        $navigation_list = $this->navigation_m->get_by(['parent_id' => null, 'status' => 1]);
        $this->load_view('navigation/create', compact('navigation_list'));
    }

    public function create_post()
    {
        $this->form_validation->set_rules(createValidateRules([
            'name' => 'trim|xss_clean|required',
            'action_path' => 'trim|xss_clean|required',
            'display_order' => 'trim|xss_clean|numeric|required',
            'show_in_menu' => 'trim|xss_clean|numeric|required',
            'show_in_permission' => 'trim|xss_clean|numeric|required',
            'status' => 'trim|xss_clean|numeric|required',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            $this->db->trans_begin();

            $dataArr = array_from_post(['name', 'action_path', 'icon', 'display_order', 'parent_id', 'show_in_menu', 'show_in_permission', 'status']);

            $dataArr->parent_id = $dataArr->parent_id ? $dataArr->parent_id : null;
            $this->navigation_m->save($dataArr);

            $this->db->trans_commit();

            // Refresh Navigation in Session
            navigationMenuListing();

            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function update($id = null)
    {
        redirectIfNull($id, 'admin/navigation');

        $navigation = $this->navigation_m->get($id);
        $navigation_list = $this->navigation_m->get_by(['parent_id' => null]);
        $this->load_view('navigation/update', compact('navigation', 'navigation_list'));
    }

    public function update_post($id = null)
    {
        redirectIfNull($id, 'admin/navigation');

        $this->form_validation->set_rules(createValidateRules([
            'name' => 'trim|xss_clean|required',
            'action_path' => 'trim|xss_clean|required',
            'display_order' => 'trim|xss_clean|numeric|required',
            'show_in_menu' => 'trim|xss_clean|numeric|required',
            'show_in_permission' => 'trim|xss_clean|numeric|required',
            'status' => 'trim|xss_clean|numeric|required',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            $this->db->trans_begin();

            $dataArr = array_from_post(['name', 'action_path', 'icon', 'display_order', 'parent_id', 'show_in_menu', 'show_in_permission', 'status']);

            $dataArr->parent_id = $dataArr->parent_id ? $dataArr->parent_id : null;
            $this->navigation_m->save($dataArr, $id);

            $this->db->trans_commit();

            // Refresh Navigation in Session
            navigationMenuListing();

            echo _success('data_updated');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function delete($id = null)
    {
        $this->navigation_m->delete($id);

        // Refresh Navigation in Session
        navigationMenuListing();

        echo _success();
    }
}
