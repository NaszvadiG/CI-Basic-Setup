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
        $this->load->view('auth/forgot_password');
    }

    public function forgot_password_post()
    {
        $this->form_validation->set_rules(createValidateRules([
            'email' => 'trim|xss_clean|required|valid_email',
        ]));

        if ($this->form_validation->run() !== true) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }
        $dataArr = array_from_post(['email']);

        $admin = $this->admin_m->get_by(['email' => $dataArr->email], 'row');
        if ($admin == null) {
            exit(_error('email_incorrect'));
        } elseif ($admin->status != 1) {
            exit(_error('account_inactive'));
        }

        // Try to send email
        $configArr = [
            'email' => $admin->email,
            'reset_link' => site_url("admin/reset/password/{$admin->id}/{$admin->hash}"),
        ];
        send_email('reset_password_email', $admin->email, 'Reset Password', $configArr);

        echo _success('recovery_mail_send');
    }

    public function reset_password($id = null, $hash = null)
    {
        $response = $this->admin_m->get_by(['id' => $id, 'hash' => $hash], 'row');
        if ($response == null) {
            exit('This link has expired.');
        } else {
            $this->load->view('auth/reset_password', compact('response'));
        }
    }

    public function reset_password_post($id = null)
    {
        $this->form_validation->set_rules(createValidateRules([
            'password' => 'trim|xss_clean|required|min_length[6]',
            'confirm_password' => 'matches[password]',
        ]));

        if ($this->form_validation->run() !== true) {
            exit(_error(strip_tags(validation_errors()), null, true, true));
        }
        $dataArr = array_from_post(['password']);

        $admin = $this->admin_m->get($id);
        if (blank($admin)) {
            exit(_error('something_went_wrong'));
        }

        $hash = hash_token();
        $this->admin_m->save([
            'password' => encrypt_hash($dataArr->password, $hash),
            'hash' => $hash,
        ], $id);

        echo _success('password_changed');
    }
}
