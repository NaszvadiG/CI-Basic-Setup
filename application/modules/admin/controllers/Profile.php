<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_m');
    }

    public function index()
    {
        $user = getSessionUser();
        $this->load_view('profile/index', compact('user'));
    }

    public function update()
    {
        $user = getSessionUser();

        $this->form_validation->set_rules(createValidateRules([
            'name' => 'trim|xss_clean|required',
            'email' => "trim|xss_clean|valid_email|required|unique[admins,email,id,{$user->id}]",
            'dial_code' => 'trim|xss_clean|required',
            'mobile' => 'trim|xss_clean|numeric|required',
        ]));

        if ($this->form_validation->run() === false) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            if (($response = validateFile('profile_image', config_item('allowed_image_mimes'))) !== true) {
                exit($response);
            }

            $this->db->trans_begin();

            $dataArr = array_from_post(['name', 'email', 'dial_code', 'mobile']);

            if (!empty($_FILES['profile_image']['name'])) {
                $dataArr->profile_image = upload_image('profile_image');
            }
            $this->admin_m->save($dataArr, $user->id);

            // Update Session
            updateUserSession();

            $this->db->trans_commit();
            echo _success('data_updated');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function change_password()
    {
        $this->form_validation->set_rules(createValidateRules([
            'current_password' => 'trim|xss_clean|required',
            'password' => 'trim|xss_clean|required|min_length[6]',
            'password_confirmation' => 'matches[password]',
        ]));

        if ($this->form_validation->run() !== true) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        try {
            $this->db->trans_begin();

            $dataArr = array_from_post(['current_password', 'password']);
            $response = $this->admin_m->get(getSessionUser('id'));
            if ($response === null) {
                exit(_error('something_went_wrong'));
            }

            if ($response->password != encrypt_hash($dataArr->current_password, $response->hash)) {
                exit(_error('current_password_invalid'));
            }

            $this->admin_m->save(['password' => encrypt_hash($dataArr->password, $response->hash)], $response->id);

            $this->db->trans_commit();
            echo _success('password_changed');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo _error($e->getMessage(), null, true, true);
        }
    }

    public function sign_out()
    {
        $this->session->unset_userdata('admin_logged_in');
        $this->session->sess_destroy();
        redirect('admin');
    }
}
