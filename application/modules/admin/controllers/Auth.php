<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_m');
    }

    public function index()
    {
        $this->load->view('auth/login');
    }

    public function validate_login()
    {
        $this->form_validation->set_rules(createValidateRules([
            'email' => 'trim|xss_clean|required|valid_email',
            'password' => 'trim|xss_clean|required',
        ]));

        if ($this->form_validation->run() !== true) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }

        $dataArr = array_from_post(['email', 'password']);
        $response = $this->admin_m->get_by(['email' => $dataArr->email], 'row');
        if ($response == null) {
            exit(_error('email_password_incorrect'));
        } elseif ($response->password != encrypt_hash($dataArr->password, $response->hash)) {
            exit(_error('email_password_incorrect'));
        } elseif ($response->status != 1) {
            exit(_error('account_inactive'));
        }

        $this->session->set_userdata('admin_logged_in', true);
        $this->session->set_userdata('admin', $response);

        // Set Navigation in Session
        navigationMenuListing();

        echo _success('success');
    }

    public function forgot_password()
    {
        $this->load->view('forgot_password');
    }

    public function process_forgot_password_request()
    {
        $this->form_validation->set_rules($this->user_m->rules['recovery_email']);

        if ($this->form_validation->run() == true) {
            $dataArr = $this->user_m->array_from_post(['email']);

            // Validate Email
            $this->db->select('email, user_id, token, status');
            $response = $this->user_m->get_by(['email' => $dataArr['email']], 'row');
            if ($response == null) {
                exit(_error(null, 'email_incorrect', true));
            } elseif ($response->status == 'Inactive') {
                exit(_error(null, 'account_inactive', true));
            } elseif ($response->status == 'Offline') {
                exit(_error(null, 'account_offline', true));
            }

            // Try to send email
            $configArr = [
                'email' => $response->email,
                'reset_link' => site_url("admin/request_reset_password/{$response->user_id}/{$response->token}"),
            ];
            // echo $configArr['reset_link'];die;
            $response = send_email('reset_password_email', $response->email, 'Reset Password', $configArr);

            return _success($response, 'recovery_mail_send', true);
        } else {
            echo _error(null, strip_tags(validation_errors()), true, true);
        }
    }

    public function request_reset_password($user_id = false, $token = false)
    {
        $this->db->select('user_id');
        $response = $this->user_m->get_by(array('user_id' => $user_id, 'token' => $token), 'row');
        if ($response == null) {
            exit('This link has expired.');
        } else {
            $this->load->view('reset_password');
        }
    }

    public function reset_password($user_id = false)
    {
        $this->form_validation->set_rules('password', 'New Password', 'trim|required|xss_clean|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

        if ($this->form_validation->run() == true) {
            $user = $this->user_m->get($user_id);
            if ($user != null) {
                $dataArr = $this->user_m->array_from_post(['password']);
                $dataArr['password'] = encrypt_hash($dataArr['password'], $user->hash);
                $dataArr['token'] = hash_token();
                $this->user_m->save($dataArr, $user_id);

                $this->session->set_flashdata('msg', lang('password_changed'));
                echo _success(site_url('admin/login'), 'success', true);
            } else {
                echo _error(null, 'invalid_user', true);
            }
        } else {
            echo _error(null, strip_tags(validation_errors()), true, true);
        }

    }
}
