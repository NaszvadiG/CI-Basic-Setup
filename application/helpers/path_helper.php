<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('imagePath')) {
    function imagePath($filename = '', $folder = 'image_path')
    {
        $folder = config_item($folder);
        return "{$folder}{$filename}";
    }
}

if (!function_exists('imageBasePath')) {
    function imageBasePath($filename = '', $folder = 'image_path')
    {
        return site_url(imagePath($filename, $folder));
    }
}
