<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('admin_logged_in') == null) {
            redirect('admin/login');
        }
    }

    public function load_view($file_name = false, $data = [])
    {
        $folder = 'admin';
        $this->load->view($folder . '/template/header', $data);
        $this->load->view($file_name, $data);
        $this->load->view($folder . '/template/footer', $data);
    }
}
