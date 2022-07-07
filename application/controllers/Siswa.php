<?php
defined('BASEPATH') or exit('No direct script access allowed');

class siswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
        $data['menu'] = '';
        $data['title'] = 'Dashboard';
        $data['user'] = $this->db->get_where('siswa', ['nis' => $this->session->userdata('nis')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_siswa = $data['user']['id'];

        $data['sum_siswa'] = $this->db->get("siswa")->num_rows();
        $data['sum_pendidikan'] = $this->db->get("data_pendidikan")->num_rows();
        $data['sum_kelas'] = $this->db->get("data_kelas")->num_rows();

        $data['sum_izin'] = $this->db->get_where("perizinan", ['id_siswa' => $id_siswa])->num_rows();
        $data['sum_takzir'] = $this->db->get_where("pelanggaran", ['id_siswa' => $id_siswa])->num_rows();

        $data['about'] = $this->db->get("about")->row_array();
        $data['sum_konsel'] = $this->db->get_where("konseling", ['id_siswa' => $id_siswa])->num_rows();

        $this->db->where('jk', 'L');
        $data['sum_pria'] = $this->db->get("siswa")->num_rows();

        $this->db->where('jk', 'P');
        $data['sum_wanita'] = $this->db->get("siswa")->num_rows();

        $data['perizinan'] = $this->db->limit(7)->get_where('perizinan', ['id_siswa' => $id_siswa])->result_array();
        $data['pelanggaran'] = $this->db->limit(7)->get_where('pelanggaran', ['id_siswa' => $id_siswa])->result_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('template/topbar', $data);
        $this->load->view('siswa/index', $data);
        $this->load->view('template/footer');
    }

    public function perizinan()
    {

        $data['menu'] = 'menu-2';
        $data['title'] = 'Perizinan';
        $data['user'] = $this->db->get_where('siswa', ['nis' => $this->session->userdata('nis')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_pend = $data['user']['id_pend'];
        $id_kelas = $data['user']['id_kelas'];

        $this->db->order_by('id', 'desc');
        $data['perizinan'] =  $this->db->get_where('perizinan', ['id_siswa' => $data['user']['id']])->result_array();
        $data['data_izin'] =  $this->db->get('data_perizinan')->result_array();

        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('expired', 'Expired', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('template/topbar', $data);
            $this->load->view('siswa/perizinan', $data);
            $this->load->view('template/footer');
        } else {

            $id_san = $this->input->post('siswa');
            $id_pelang = $this->input->post('jenis');

            $cek_izin = $this->db->get_where('data_perizinan', ['id' => $id_pelang])->row_array();

            $data = [
                'id_siswa' => $id_san,
                'id_izin' => $cek_izin['id'],
                'keterangan' => $this->input->post('keterangan'),
                'tgl' => $this->input->post('tanggal'),
                'expired' => $this->input->post('expired'),
                'status' => 'Pending',
                // 'id_pelang' => $id_pelang,
                'id_pend' => $id_pend,
                'id_kelas' => $id_kelas
            ];

            $this->db->insert('perizinan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data perizinan <strong>' . $cek_izin['nama'] . '</strong> berhasil dibuat :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('siswa/perizinan');
        }
    }


    public function profile()
    {
        $data['menu'] = 'menu-3';
        $data['title'] = 'Setting Akun';
        $data['user'] = $this->db->get_where('siswa', ['nis' => $this->session->userdata('nis')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id = $data['user']['id'];
        $id_kelas = $data['user']['id_kelas'];
        $id_pend = $data['user']['id_pend'];

        $data['prov'] = $this->db->get('provinsi')->result_array();
        $data['pendidikan'] = $this->db->get_where('data_pendidikan', ['id' => $id_pend])->row_array();
        $data['kelas'] = $this->db->get_where('data_kelas', ['id' => $id_kelas])->row_array();

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('template/topbar', $data);
            $this->load->view('siswa/profile', $data);
            $this->load->view('template/footer');
        } else {

            $nama = $this->input->post('nama');
            $id_prov = $this->input->post('prov');

            $provinsi = $this->db->get_where('provinsi', ['id_prov' => $id_prov])->row_array();

            $data = [
                'nik' => $this->input->post('nik'),
                'nis' => $this->input->post('nis'),
                'nama' => $nama,
                'email' => $this->input->post('email'),
                'jk' => $this->input->post('jk'),
                'ttl' => $this->input->post('ttl'),
                'prov' => $provinsi['nama'],
                'kab' => $this->input->post('kab'),
                'alamat' => $this->input->post('alamat'),
                'nama_ayah' => $this->input->post('nama_ayah'),
                'nama_ibu' => $this->input->post('nama_ibu'),
                'pek_ayah' => $this->input->post('pek_ayah'),
                'pek_ibu' => $this->input->post('pek_ibu'),
                'nama_wali' => $this->input->post('nama_wali'),
                'pek_wali' => $this->input->post('pek_wali'),
                'peng_ortu' => $this->input->post('peng_ortu'),
                'no_telp' => $this->input->post('no_telp'),
                'thn_msk' => $this->input->post('thn_msk'),
                'sekolah_asal' => $this->input->post('sekolah_asal'),
                'kelas' => $this->input->post('kelas'),
                'diniyah' => $this->input->post('diniyah')
            ];

            $this->db->where('id', $id);
            $this->db->update('siswa', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Data akun kamu berhasil diupdate :)
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            redirect('siswa/profile');
        }
    }


    public function edit_pass()
    {
        $data['menu'] = 'menu-4';
        $data['title'] = 'Setting Akun';
        $data['user'] = $this->db->get_where('siswa', ['nis' => $this->session->userdata('nis')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->form_validation->set_rules('old_password', 'Password Lama', 'required|trim');
        $this->form_validation->set_rules('password1', 'Password Baru', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password tidak sama!', 'min_length' => 'Password terlalu pendek'
        ]);
        $this->form_validation->set_rules('password2', 'Konfirmasi Password Baru', 'required|trim|min_length[3]|matches[password1]', [
            'matches' => 'Password tidak sama!', 'min_length' => 'Password terlalu pendek'
        ]);
        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('template/topbar', $data);
            $this->load->view('siswa/edit_pass', $data);
            $this->load->view('template/footer');
        } else {
            $old_password = $this->input->post('old_password');
            $new_password = $this->input->post('password1');
            if (!password_verify($old_password, $data['user']['password'])) {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Password lama salah!
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
         </button>
         </div>'
                );
                redirect('siswa/edit_pass');
            } else {
                if ($old_password == $new_password) {
                    $this->session->set_flashdata(
                        'message',
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Password baru tidak boleh sama dengan Password saat ini!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>'
                    );
                    redirect('siswa/edit_pass');
                } else {
                    // password sudah ok
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                    $this->db->set('password', $password_hash);
                    $this->db->where('nis', $this->session->userdata('nis'));
                    $this->db->update('siswa');

                    $this->session->set_flashdata(
                        'message',
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Password berhasil di ubah! :)
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             </div>'
                    );
                    redirect('siswa/edit_pass');
                }
            }
        }
    }


    public function konseling()
    {

        $data['menu'] = 'menu-1';
        $data['title'] = 'Konseling';
        $data['user'] = $this->db->get_where('siswa', ['nis' => $this->session->userdata('nis')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_siswa = $data['user']['id'];
        $id_pend = $data['user']['id_pend'];
        $id_kelas = $data['user']['id_kelas'];

        $cek_karyawan = $this->db->get_where('data_kelas', ['id' => $id_kelas, 'id_pend' => $id_pend,])->row_array();
        $karyawan     = $cek_karyawan['id_peng'];

        $data['konseling'] =  $this->db->get_where('konseling', ['id_siswa' => $id_siswa, 'id_peng' => $karyawan])->result_array();
        $data['konsel'] =  $this->db->get_where('balas_konseling', ['id_siswa' => $id_siswa, 'id_peng' => $karyawan])->row_array();

        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        $this->form_validation->set_rules('topik', 'Topik', 'required');
        $this->form_validation->set_rules('solusi', 'Solusi', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('template/topbar', $data);
            $this->load->view('siswa/konseling', $data);
            $this->load->view('template/footer');
        } else {
            $id_san = $this->input->post('siswa');
            $topik = $this->input->post('topik');

            $data = [
                'id_siswa' => $id_san,
                'id_peng' => $karyawan,
                'id_kelas' => $id_kelas,
                'topik' => $topik,
                'solusi' => $this->input->post('solusi'),
                'tgl_pengajuan' => date('Y-m-d'),
                'pembuka' => 'siswa',
                'status' => 'Pending',
            ];

            $this->db->insert('konseling', $data);

            $cek_id = $this->db->get_where('konseling', ['topik' => $topik])->row_array();
            $id = $cek_id['id'];

            $data2 = [
                'pengirim'  => 'siswa',
                'id_peng'  => $karyawan,
                'id_siswa'  => $id_san,
                'balasan'   => $this->input->post('solusi'),
                'tgl'       => date('Y-m-d'),
                'waktu'     => date('h:i:s'),
                'role_konseling' => $id
            ];
            $this->db->insert('balas_konseling', $data2);

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data konseling <strong>' . $topik . '</strong> berhasil dibuat :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('siswa/konseling');
        }
    }


    public function balas_konseling()
    {
        $id_konseling = $this->input->get('id');
        $data['menu'] = 'menu-1';
        $data['title'] = 'Konseling';
        $data['user'] = $this->db->get_where('siswa', ['nis' => $this->session->userdata('nis')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['konseling'] =  $this->db->get_where('konseling', ['id' => $id_konseling])->row_array();
        $data['balas_konseling'] =  $this->db->get_where('balas_konseling', ['role_konseling' => $id_konseling]);

        if ($data['konseling']['status']  !== 'Respon siswa') {
            $this->db->set('status', 'Terbaca siswa');
            $this->db->where('id', $id_konseling);
            $this->db->update('konseling');
        }

        $this->form_validation->set_rules('balasan', 'Balasan', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('template/topbar', $data);
            $this->load->view('siswa/balas_konseling', $data);
            $this->load->view('template/footer');
        } else {
            $this->db->set('status', 'Respon siswa');
            $this->db->where('id', $id_konseling);
            $this->db->update('konseling');

            $id_peng = $data['konseling']['id_peng'];

            $id = $this->input->post('id');
            $tgl = date('Y-m-d');
            $data = [
                'pengirim'  => 'siswa',
                'id_peng'  => $id_peng,
                'id_siswa'  => $this->input->post('nama'),
                'balasan'   => $this->input->post('balasan'),
                'tgl'       => $tgl,
                'waktu'     => date('h:i:s'),
                'role_konseling' => $id
            ];
            $this->db->insert('balas_konseling', $data);

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Balasan Terkirim!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('siswa/balas_konseling?id=' . $id_konseling . '');
        }
    }



    public function pelanggaran()
    {

        $data['menu'] = 'menu-5';
        $data['title'] = 'Pelanggaran';
        $data['user'] = $this->db->get_where('siswa', ['nis' => $this->session->userdata('nis')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $id_san = $data['user']['id'];

        $data['pelanggaran'] =  $this->db->get_where('pelanggaran', ['id_siswa' => $id_san])->result_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('template/topbar', $data);
        $this->load->view('siswa/pelanggaran', $data);
        $this->load->view('template/footer');
    }
}
