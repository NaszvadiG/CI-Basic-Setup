<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('getGroupNavigation')) {
    function getGroupNavigation()
    {
        $CI = &get_instance();
        $CI->load->model('navigation_m');

        $CI->db->select('id, name, parent_id');
        $navigation = $CI->navigation_m->get_by([
            'status' => 1,
            'show_in_permission' => 1,
        ], 'result_array');

        return count($navigation) ? arrayToTree($navigation, null) : $navigation;
    }
}

if (!function_exists('getRolePermission')) {
    function getRolePermission($accessRoleId)
    {
        $CI = &get_instance();
        $CI->load->model('role_permission_m');

        $CI->db->select('navigation_id');
        return array_column($CI->role_permission_m->get_by(['role_id' => $accessRoleId], 'result_array'), 'navigation_id');
    }
}

if (!function_exists('getUsersPermission')) {
    function getUsersPermission($accessAdminId)
    {
        $CI = &get_instance();
        $CI->load->model('user_permission_m');

        $CI->db->select('navigation_id');
        return array_column($CI->user_permission_m->get_by(['user_id' => $accessAdminId], 'result_array'), 'navigation_id');
    }
}

if (!function_exists('getUsersPermissionIDs')) {
    function getUsersPermissionIDs($accessAdminId, $accessRoleId)
    {
        $usersPermissions = getUsersPermission($accessAdminId);
        return count($usersPermissions) ? $usersPermissions : getRolePermission($accessRoleId);
    }
}

if (!function_exists('arrayToTree')) {
    function arrayToTree($elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = arrayToTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}

if (!function_exists('navigationMenuListing')) {
    function navigationMenuListing($guard = 'admin', $saveSession = true, $accessAdminId = null, $accessRoleId = null)
    {
        $excludeRoleId = [1];
        $navigationMasters = [];
        $CI = &get_instance();
        $CI->load->model('navigation_m');

        if ($saveSession == true) {
            $guardData = $CI->session->userdata($guard);
            $accessAdminId = $guardData->id;
            $accessRoleId = $guardData->role_id;
        }

        if (in_array($accessRoleId, $excludeRoleId)) {
            $CI->db->select('id, name, icon, parent_id, action_path, show_in_menu');
            $CI->db->order_by('display_order', 'ASC');
            $navigationMasters = $CI->navigation_m->get_by(['status' => 1], 'result_array');
        } else {
            $allowedNavIds = getUsersPermissionIDs($accessAdminId, $accessRoleId);
            if (count($allowedNavIds)) {
                $CI->db->select('id, name, icon, parent_id, action_path, show_in_menu');
                $CI->db->order_by('display_order', 'ASC');
                $CI->db->where_in('id', $allowedNavIds);
                $navigationMasters = $CI->navigation_m->get_by(['status' => 1], 'result_array');
            }
        }

        if (count($navigationMasters)) {
            $navigationMasters = arrayToTree($navigationMasters, null);

            if ($saveSession == true) {
                $CI->session->set_userdata("navigation_{$guard}", $navigationMasters);
            }
        }

        return $saveSession === true ? $navigationMasters : true;
    }
}

if (!function_exists('hasAccess')) {
    function hasAccess($actionPath, $exclude = false)
    {
        if ($exclude === true) {
            return true;
        }

        $CI = &get_instance();
        if ($CI->session->userdata('navigationPermissions') !== null) {
            $navigationPermissions = $CI->session->userdata('navigationPermissions');
            $key = array_search($actionPath, array_column($navigationPermissions, 'action_path'));
            return $key !== false ? true : false;
        }
        return false;
    }
}
