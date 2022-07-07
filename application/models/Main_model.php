<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main_model extends CI_Model
{
    // Fetch siswa
    function getsiswa($searchTerm = "")
    {

        // Fetch siswa
        $this->db->select('*');
        $this->db->where("nama like '%" . $searchTerm . "%' ");
        $fetched_records = $this->db->get('siswa');
        $siswa = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($siswa as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['nis'] . ' | ' . $user['nama']);
        }
        return $data;
    }

    // Fetch Karyawan
    function getKaryawan($searchTerm = "")
    {

        // Fetch karyawan
        $this->db->select('*');
        $this->db->where("nama like '%" . $searchTerm . "%' ");

        $fetched_records = $this->db->get('karyawan');
        $siswa = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($siswa as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['nama']);
        }
        return $data;
    }

    // Fetch siswa
    function getsiswa_pendidikan($pendidikan, $searchTerm = "")
    {
        // Fetch siswa
        $this->db->select('*');
        $this->db->where("nama like '%" . $searchTerm . "%' ");
        $fetched_records = $this->db->get_where('siswa', ['id_pend' => $pendidikan]);
        $siswa = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($siswa as $user) {
            $data[] = array("id" => $user['nama'], "text" => $user['nis'] . ' | ' . $user['nama']);
        }
        return $data;
    }

    // Fetch siswa
    function getsiswa_kelas($kelas, $searchTerm = "")
    {
        // Fetch siswa
        $this->db->select('*');
        $this->db->where("nama like '%" . $searchTerm . "%' ");
        $fetched_records = $this->db->get_where('siswa', ['id_kelas' => $kelas]);
        $siswa = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($siswa as $user) {
            $data[] = array("id" => $user['nama'], "text" => $user['nis'] . ' | ' . $user['nama']);
        }
        return $data;
    }

    // Fetch Takzir
    function getTakzir($searchTerm = "")
    {

        // Fetch Takzir
        $this->db->select('*');
        $this->db->where("nama like '%" . $searchTerm . "%' ");
        $fetched_records = $this->db->get('data_pelanggaran');
        $takzir = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($takzir as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['kode'] . ' | ' . $user['nama']);
        }
        return $data;
    }
}
