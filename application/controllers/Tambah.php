<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tambah extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function tambah_isi_kursi()
    {

        $id     = $this->input->post('id');
        $id_siswa   = $this->input->post('siswa');

        $cek_siswa = $this->db->get_where('data_kursi', ['id_siswa' => $id_siswa])->row_array();

        $nama = $this->db->get_where('siswa', ['id' => $id_siswa])->row_array();

        if ($cek_siswa['id_siswa']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            siswa <strong>' . $nama['nama'] . '</strong> sudah mempunyai kursi.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_kursi');
        } else {

            $this->db->set('id_siswa', $id_siswa);
            $this->db->where('id', $id);
            $this->db->update('data_kursi');

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success alert-dismissible fade show" role="alert">
            siswa <strong>' . $nama['nama'] . '</strong> berhasil ditambahkan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>'
            );
            redirect('admin/data_kursi');
        }
    }


    public function tambah_isi_kursi_kelas()
    {

        $segmen = $this->uri->segment(3);
        $id     = $this->input->post('id');
        $id_siswa   = $this->input->post('siswa');

        $nama = $this->db->get_where('siswa', ['id' => $id_siswa])->row_array();

        $cek_kursi = $this->db->get_where('data_kursi', ['id_siswa' => $id_siswa])->row_array();

        $cek = $this->db->get_where('data_kursi', ['id' => $id])->row_array();
        $kelas1 = $this->db->get_where('data_kelas', ['id' => $cek['id_kelas']])->row_array();

        $id_kelas = $kelas1['id'];

        if ($cek_kursi['id_siswa']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            siswa <strong>' . $nama['nama'] . '</strong> sudah mempunyai kursi.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');

            if ($segmen == 'karyawan') {
                redirect('karyawan/view_kelas/' . $id_kelas . '');
            } else {
                redirect('admin/view_kelas/' . $id_kelas . '');
            }
        } else {
            $data = [
                'id_siswa' => $id_siswa,
            ];
            $this->db->where('id', $id);
            $this->db->update('data_kursi', $data);
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success alert-dismissible fade show" role="alert">
            siswa <strong>' . $nama['nama'] . '</strong> berhasil ditambahkan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>'
            );

            if ($segmen == 'karyawan') {
                redirect('karyawan/view_kelas/' . $id_kelas . '');
            } else {
                redirect('admin/view_kelas/' . $id_kelas . '');
            }
        }
    }



    public function tambah_kursi()
    {
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->form_validation->set_rules('kursi', 'kursi', 'required');
        $this->form_validation->set_rules('tipe', 'kursi', 'required');
        $this->form_validation->set_rules('kelas', 'kelas', 'required');

        $id = $this->input->post('id');
        $kelas = $this->input->post('kelas');
        $nama = $this->input->post('kursi');

        $cek_kelas = $this->db->get_where('kelas', ['kelas' => $kelas])->row_array();

        $cek = $this->db->get_where('data_kursi', ['kursi' => $nama, 'kelas' => $kelas])->row_array();

        if ($cek['kursi']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Data kursi <strong>' . $nama . '</strong> di kelas <strong>' . $cek_kelas['kelas'] . '</strong> sudah ada.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            redirect('karyawan/view_kelas/' . $id . '');
        } else {
            $data = [
                'kursi' => $nama,
                'tipe' => $this->input->post('tipe'),
                'kelas' => $kelas
            ];

            $this->db->insert('data_kursi', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data kursi <strong>' . $nama . '</strong> berhasil ditambahkan.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            redirect('karyawan/view_kelas/' . $id . '');
        }
    }
    
    
    public function data_siswa()
    {
        $tgl = date('Y-m-d');
        $segmen = $this->uri->segment(3);
        $id    = $this->input->get('id');
        $data_sis =  $this->db->get_where('ppdb', ['id' => $id])->row_array();
        
         $data = [
                'point'         => '100',
                'nik'           => $data_sis['nik'],
                'nis'           => $data_sis['nis'],
                'nama'          => $data_sis['nama'],
                'email'         => $data_sis['email'],
                'password'      => $data_sis['password'],
                'jk'            => $data_sis['jk'],
                'ttl'           => $data_sis['ttl'],
                'prov'          => $data_sis['prov'],
                'kab'           => $data_sis['kab'],
                'alamat'        => $data_sis['alamat'],
                'nama_ayah'     => $data_sis['nama_ayah'],
                'nama_ibu'      => $data_sis['nama_ibu'],
                'pek_ayah'      => $data_sis['pek_ayah'],
                'pek_ibu'       => $data_sis['pek_ibu'],
                'nama_wali'     => $data_sis['nama_wali'],
                'pek_wali'      => $data_sis['pek_wali'],
                'peng_ortu'     => $data_sis['peng_ortu'],
                'no_telp'       => $data_sis['no_telp'],
                'thn_msk'       => $data_sis['thn_msk'],
                'sekolah_asal'  => $data_sis['sekolah_asal'],
                'kelas'         => $data_sis['kelas'],
                'diniyah'       => $data_sis['diniyah'],
                'id_kelas'      => $data_sis['kelas'],
                'date_created'  => $tgl,
                'status'        => 1,
                'role_id'       => 5
            ];
             $this->db->insert('siswa', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Data siswa <strong>' . $nama . '</strong> berhasil ditambahkan!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
           if ($segmen == 'karyawan') {
                redirect('karyawan/daftar_siswa');
            } else {
                redirect('admin/daftar_siswa');
            }
            
    }
}
