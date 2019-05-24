<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['company_name'] = 'Basic CI Setup';
$config['site_title'] = 'Basic CI Setup';
$config['from_email'] = 'info@demo.com';
$config['action_status'] = ['1' => 'Active', '0' => 'Inactive'];
$config['other_status'] = ['1' => 'Yes', '0' => 'No'];

$config['image_path'] = 'assets/uploads/images/';

$config['allowed_image_mimes'] = 'mimes:jpeg,png,bmp,jpg|max:102400';
