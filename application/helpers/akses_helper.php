<?php

function is_logged_in()
{
    $ci = get_instance();
    if (!$ci->session->userdata('nis')) {
        redirect('auth');
    } else {
        $queryMenu = $ci->db->get_where('siswa')->row_array();
        $role_id = $queryMenu['role_id'];

        if ($role_id < 1) {
            redirect('auth/blocked');
        }
    }
}
