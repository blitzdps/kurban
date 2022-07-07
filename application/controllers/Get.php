<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Get extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Main_model');
    }

    public function getsiswa()
    {
        // Search term
        $searchTerm = $this->input->post('search');

        // Get siswa
        $response = $this->Main_model->getsiswa($searchTerm);

        echo json_encode($response);
    }


    public function getsiswa_pendidikan()
    {
        $sesi = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $pendidikan = $sesi['pendidikan'];
        // Search term
        $searchTerm = $this->input->post('search');
        // Get siswa
        $response = $this->Main_model->getsiswa_pendidikan($pendidikan, $searchTerm);

        echo json_encode($response);
    }

    public function getKaryawan()
    {
        // Search term
        $searchTerm = $this->input->post('search');
        // Get siswa
        $response = $this->Main_model->getKaryawan($searchTerm);

        echo json_encode($response);
    }


    public function getsiswa_kelas()
    {
        $kelas = $this->uri->segment(3);
        // Search term
        $searchTerm = $this->input->post('search');
        // Get siswa
        $response = $this->Main_model->getsiswa_kelas($kelas, $searchTerm);

        echo json_encode($response);
    }


    public function get_point()
    {
        $jenis = $this->input->post('jenis');
        if (isset($jenis)) {
            $this->db->where('id', $jenis);
            $query  =  $this->db->get('data_pelanggaran');
            $result =  $query->result_array();
            if (isset($result[0]) && is_array($result)) {
                foreach ($result as $value) {
                    $options  = $value['point'];
                }
                echo $options;
            }
        }
    }

    public function get_pelanggaran()
    {
        $jenis = $this->input->post('jenis');
        if (isset($jenis)) {
            $this->db->where('id', $jenis);
            $query  =  $this->db->get('data_pelanggaran');
            $result =  $query->result_array();
            if (isset($result[0]) && is_array($result)) {
                foreach ($result as $value) {
                    $options  = $value['nama'];
                }
                echo $options;
            }
        }
    }

    public function get_kelas()
    {
        $pendidikan = $this->input->post('pendidikan');
        if (isset($pendidikan)) {
            $this->db->where('id_pend', $pendidikan);
            $query  =  $this->db->get('data_kelas');
            $result =  $query->result_array();
            if (isset($result[0]) && is_array($result)) {
                $options = '';
                $options .= '<option selected disabled>- Pilih Kelas -</option>';
                foreach ($result as $value) {
                    $options  .= '<option value="' . $value['id'] . '">' .
                        $value['nama'] . '</option>';
                }
                echo $options;
            }
        }
    }

    public function get_kota()
    {
        $seg = $this->uri->segment(3);
        $prov = $this->input->post('prov');
        if (isset($prov)) {
            $this->db->order_by('nama_kab', 'asc');
            $this->db->where('id_prov', $prov);
            $query  =  $this->db->get('kabupaten');
            $result =  $query->result_array();
            if (isset($result[0]) && is_array($result)) {
                $options = '';
                if ($seg == 'daftar') {
                    $options .= '<option selected value="">- Pilih provinsi saja -</option>';
                } else {
                    $options .= '<option selected disabled>- Pilih Kota -</option>';
                }
                foreach ($result as $value) {
                    $options  .= '<option value="' . $value['nama_kab'] . '">' .
                        $value['nama_kab'] . '</option>';
                }
                echo $options;
            }
        }
    }

    public function get_kota_edit()
    {
        $prov = $this->input->post('prov1');
        if (isset($prov)) {
            $this->db->order_by('nama_kab', 'asc');
            $this->db->where('id_prov', $prov);
            $query  =  $this->db->get('kabupaten');
            $result =  $query->result_array();
            if (isset($result[0]) && is_array($result)) {
                $options = '';
                $options .= '<option selected disabled>- Pilih Kota -</option>';
                foreach ($result as $value) {
                    $options  .= '<option value="' . $value['nama_kab'] . '">' .
                        $value['nama_kab'] . '</option>';
                }
                echo $options;
            }
        }
    }
}