<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roles extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('role_m');
    }

    public function index()
    {
        $this->load_view('roles/index');
    }

    public function listing()
    {
        echo $this->datatables->select('id, name, status')
            ->from('admin_roles')
            ->generate();

    }

    public function create()
    {
        $this->load_view('roles/create');
    }

    public function create_post()
    {
        $this->form_validation->set_rules(createValidateRules([
            'name' => 'trim|xss_clean|required|unique[admin_roles,name]',
            'status' => 'trim|xss_clean|required',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            $this->db->trans_begin();

            $dataArr = array_from_post(['name', 'status']);
            $this->role_m->save($dataArr);

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function update($id = null)
    {
        redirectIfNull($id, 'admin/roles');

        $role = $this->role_m->get($id);
        $this->load_view('roles/update', compact('role'));
    }

    public function update_post($id = null)
    {
        redirectIfNull($id, 'admin/roles');

        $this->form_validation->set_rules(createValidateRules([
            'name' => "trim|xss_clean|required|unique[admin_roles,name,id,{$id}]",
            'status' => 'trim|xss_clean|required',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            $this->db->trans_begin();

            $dataArr = array_from_post(['name', 'status']);
            $this->role_m->save($dataArr, $id);

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function permissions($id = null)
    {
        redirectIfNull($id, 'admin/roles');

        $navigation = getGroupNavigation();
        $rolePermissions = getRolePermission($id);

        $this->load_view('roles/permissions', compact('id', 'navigation', 'rolePermissions'));
    }

    public function permissions_post($id = null)
    {
        redirectIfNull($id, 'admin/roles');

        $this->form_validation->set_rules(createValidateRules([
            'navigation_id[]' => 'required|is_array[navigation_id]',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            $this->db->trans_begin();

            $dataArr = array_from_post(['navigation_id']);
            $this->load->model('role_permission_m');

            $this->role_permission_m->delete_by(['role_id' => $id]);

            foreach ($dataArr->navigation_id as $navigation_id) {
                $this->role_permission_m->save([
                    'navigation_id' => $navigation_id,
                    'role_id' => $id,
                ]);
            }

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }
}
