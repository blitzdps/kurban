<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ppdb extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Jamaah_model');
    }

    public function index()
    {
        $users = $this->session->userdata('email');
        $user = $this->db->get_where('ppdb', ['email' => $users])->num_rows();
        if (!empty($user)) {
            redirect('ppdb/dashboard');
        }
        $data['menu'] = 'home';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['home'] =  $this->db->get('home')->row_array();
        $data['jamaah']=$this->Jamaah_model->getAllJamaah()->result_array();


        $this->form_validation->set_rules('nama', 'Nama Jamaah', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('blok', 'Blok', 'required');
        $this->form_validation->set_rules('no', 'Nomor', 'required');
        $this->form_validation->set_rules('status', 'Status');

        if ($this->form_validation->run() == false) {
            $this->load->view('frontend/header', $data);
            $this->load->view('frontend/ppdb/ppdb', $data);
            $this->load->view('frontend/footer', $data);
        } else {
            $this->Jamaah_model->tambahdata($data);
			$this->session->set_flashdata('flash', 'Ditambahkan');
            redirect('ppdb');
        }
    }

    public function login()
    {
        if ($this->session->userdata('email')) {
            redirect('ppdb/dashboard');
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['menu'] = 'home';
            $data['web'] =  $this->db->get('website')->row_array();
            $data['home'] =  $this->db->get('home')->row_array();

            $this->load->view('frontend/header', $data);
            $this->load->view('frontend/ppdb/ppdb_login', $data);
            $this->load->view('frontend/footer', $data);
        } else {
            // validasinya success
            $this->_login();
        }
    }


    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('ppdb', ['email' => $email])->row_array();

        // jika usernya ada
        if ($user) {
            // cek password
            if (password_verify($password, $user['password'])) {
                $data = [
                    'email' => $user['email'],
                    'nik' => $user['nik']
                ];
                $this->session->set_userdata($data);

                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Susccess!</strong> Anda berhasil login! :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>'
                );
                redirect('ppdb/dashboard');
            } else {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                     Password salah!
                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
                 </div>'
                );
                redirect('ppdb/login');
            }
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Email tidak terdaftar!
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             </div>'
            );
            redirect('ppdb/login');
        }
    }


    public function dashboard()
    {
        $users = $this->session->userdata('email');
        $user = $this->db->get_where('ppdb', ['email' => $users])->num_rows();
        if (empty($user)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
             Silahkan masuk terlebih dahulu!
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
         </button>
         </div>');
            redirect('ppdb/login');
        }
        $data['menu'] = 'home';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['home'] =  $this->db->get('home')->row_array();
        $data['user'] = $this->db->get_where('ppdb', ['email' => $this->session->userdata('email')])->row_array();

        $this->db->order_by('nama', 'asc');
        $data['prov'] = $this->db->get('provinsi')->result_array();

        $this->form_validation->set_rules('email', 'Email', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('frontend/header', $data);
            $this->load->view('frontend/ppdb/dashboard', $data);
            $this->load->view('frontend/footer', $data);
        } else {
            $id = $this->input->post('id');
            $id_prov = $this->input->post('prov');

            $provinsi =  $data['prov'] = $this->db->get_where('provinsi', ['id_prov' => $id_prov])->row_array();

            //GAMBAR
            $config['upload_path'] = './assets/img/data/';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config);

            $this->db->where('id', $id);
            $g =  $this->db->get('ppdb')->row_array();

            if ($this->upload->do_upload('img_siswa')) {
                $img_siswa  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img_siswa']);
            } else {
                $img_siswa  = $g['img_siswa'];
            }
            if ($this->upload->do_upload('img_kk')) {
                $img_kk  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img_kk']);
            } else {
                $img_kk  = $g['img_kk'];
            }
            if ($this->upload->do_upload('img_ijazah')) {
                $img_ijazah  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img_ijazah']);
            } else {
                $img_ijazah  = $g['img_ijazah'];
            }
            if ($this->upload->do_upload('img_ktp')) {
                $img_ktp  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img_ktp']);
            } else {
                $img_ktp  = $g['img_ktp'];
            }
            $data = [
                'nik' => $this->input->post('nik'),
                'nis' => $this->input->post('nis'),
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'no_hp' => $this->input->post('no_hp'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
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
                'diniyah' => $this->input->post('diniyah'),
                'img_siswa' => $img_siswa,
                'img_kk' => $img_kk,
                'img_ijazah' => $img_ijazah,
                'img_ktp' => $img_ktp
            ];

            $this->db->where('id', $id);
            $this->db->update('ppdb', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Data pendaftaran kamu berhasil di update.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            redirect('ppdb/dashboard');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('email');

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Anda berhasil Keluar :)
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>'
        );
        redirect('ppdb/login');
    }
}
