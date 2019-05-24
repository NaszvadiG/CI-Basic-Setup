<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');
    }

    public function index()
    {
        $this->load_view('users/index');
    }

    public function listing()
    {
        echo $this->datatables->select('id, name, email, dial_code, mobile, status')
            ->from('users')
            ->generate();

    }

    public function create()
    {
        $this->load_view('users/create');
    }

    public function create_post()
    {
        $this->form_validation->set_rules(createValidateRules([
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

            $dataArr = array_from_post(['name', 'email', 'dial_code', 'mobile', 'password', 'status']);
            $dataArr->email = strtolower($dataArr->email);
            $dataArr->hash = hash_token();
            $dataArr->password = encrypt_hash($dataArr->password, $dataArr->hash);
            $dataArr->profile_image = upload_image('profile_image');
            $this->user_m->save($dataArr);

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function update($id = null)
    {
        redirectIfNull($id, 'admin/users');

        $user = $this->user_m->get($id);
        $this->load_view('users/update', compact('user'));
    }

    public function update_post($id = null)
    {
        redirectIfNull($id, 'admin/user');

        $this->form_validation->set_rules(createValidateRules([
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
            $this->user_m->save($dataArr, $id);

            $this->db->trans_commit();
            echo _success('data_saved');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function delete($id = null)
    {
        $this->user_m->delete($id);
        echo _success();
    }

    public function change_password($id = null)
    {
        redirectIfNull($id, 'admin/users');

        $user = $this->user_m->get($id);
        $this->load_view('users/change_password', compact('user'));
    }

    public function change_password_post($id = null)
    {
        redirectIfNull($id, 'admin/users');

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

            $user = $this->user_m->get($id);
            
            $this->user_m->save([
                'password' => encrypt_hash($dataArr->password, $user->hash),
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
        redirectIfNull($id, 'admin/users');

        $user = $this->user_m->get($id);
        $this->load_view('users/view', compact('user'));
    }
}
