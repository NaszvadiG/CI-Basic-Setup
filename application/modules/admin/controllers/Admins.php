<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admins extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['admin_m', 'role_m']);
    }

    public function index()
    {
        $this->load_view('admins/index');
    }

    public function listing()
    {
        echo $this->datatables->select('admins.id, admins.name, admins.email, admins.dial_code, admins.mobile, admin_roles.name AS role, admins.status')
            ->from('admins')
            ->join('admin_roles', 'admin_roles.id=admins.role_id')
            ->where('admins.id <>', getSessionUser('id'))
            ->generate();

    }

    public function create()
    {
        $roles = $this->role_m->get_by(['status' => 1]);
        $this->load_view('admins/create', compact('roles'));
    }

    public function create_post()
    {
        $this->form_validation->set_rules(createValidateRules([
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required|valid_email|unique[users,name]',
            'dial_code' => 'required',
            'mobile' => 'required',
            'password' => 'required|min_length[6]',
            'status' => 'required',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            if (($response = validateFile('profile_image', 'required|' . config_item('allowed_image_mimes'))) !== true) {
                exit($response);
            }

            $this->db->trans_begin();

            $dataArr = array_from_post(['role_id', 'name', 'email', 'dial_code', 'mobile', 'password', 'status']);
            $dataArr->email = strtolower($dataArr->email);
            $dataArr->hash = hash_token();
            $dataArr->password = encrypt_hash($dataArr->password, $dataArr->hash);
            $dataArr->profile_image = upload_image('profile_image');
            $this->admin_m->save($dataArr);

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function update($id = null)
    {
        redirectIfNull($id, 'admin/admins');

        $admin = $this->admin_m->get($id);
        $roles = $this->role_m->get_by(['status' => 1]);
        $this->load_view('admins/update', compact('admin', 'roles'));
    }

    public function update_post($id = null)
    {
        redirectIfNull($id, 'admin/admins');

        $this->form_validation->set_rules(createValidateRules([
            'role_id' => 'required',
            'name' => 'required',
            'email' => "required|valid_email|unique[users,name,id,{$id}]",
            'dial_code' => 'required',
            'mobile' => 'required',
            'status' => 'required',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            if (($response = validateFile('profile_image', config_item('allowed_image_mimes'))) !== true) {
                exit($response);
            }

            $this->db->trans_begin();

            $dataArr = array_from_post(['name', 'email', 'dial_code', 'mobile', 'status']);
            $dataArr->email = strtolower($dataArr->email);
            if (isset($_FILES['profile_image']) && !empty($_FILES['profile_image']['name'])) {
                $dataArr->profile_image = upload_image('profile_image');
            }
            $this->admin_m->save($dataArr, $id);

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function delete($id = null)
    {
        $this->admin_m->delete($id);
        echo _success();
    }

    public function change_password($id = null)
    {
        redirectIfNull($id, 'admin/admins');

        $admin = $this->admin_m->get($id);
        $this->load_view('admins/change_password', compact('admin'));
    }

    public function change_password_post($id = null)
    {
        redirectIfNull($id, 'admin/admins');

        $this->form_validation->set_rules(createValidateRules([
            'password' => "trim|xss_clean|required|min_length[6]",
            'confirm_password' => 'matches[password]',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            $dataArr = array_from_post(['password']);

            $this->db->trans_begin();

            $admin = $this->admin_m->get($id);

            $this->admin_m->save([
                'password' => encrypt_hash($dataArr->password, $admin->hash),
            ], $id);

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function view($id = null)
    {
        redirectIfNull($id, 'admin/admins');

        $admin = $this->admin_m->get($id);
        $admin->role = $this->role_m->get($admin->role_id);
        $this->load_view('admins/view', compact('admin'));
    }
}
