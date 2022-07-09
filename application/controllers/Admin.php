<?php
ob_start();
defined('BASEPATH') or exit('No direct script access allowed');
//load Spout Library
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        require APPPATH . 'libraries/phpmailer/src/Exception.php';
        require APPPATH . 'libraries/phpmailer/src/PHPMailer.php';
        require APPPATH . 'libraries/phpmailer/src/SMTP.php';

        $users = $this->session->userdata('email');
        $this->load->model('Main_model');
        $this->load->model('Export_model');
        $this->load->model('Import_model');
        $this->load->model('Masjid_model');
        $this->load->model('Jamaah_model');
        $this->load->helper('tgl_indo');
        $this->load->library('email');

        $user = $this->db->get_where('karyawan', ['email' => $users])->row_array();
        if ($user['role_id'] == '5') {
            redirect('siswa');
        } elseif ($user['role_id'] !== '1') {
            redirect('karyawan');
        } elseif ($user['role_id'] < '1') {
            redirect('auth/blocked');
        }

        if (!$users) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
             Silahkan masuk terlebih dahulu!
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
         </button>
         </div>');
            redirect('auth/admin');
        }
    }

    public function index()
    {
        //Setting point default
        // $this->db->set('point', 300);
        // $this->db->update('siswa');


        $data['menu'] = '';
        $data['title'] = 'Dashboard';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        
        $data['about'] = $this->db->get("about")->row_array();

        $data['sum_kontak'] = $this->db->get("kontak")->num_rows();
        $data['sum_gallery'] = $this->db->get("gallery")->num_rows();
        $data['sum_acara'] = $this->db->get("acara")->num_rows();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('template/footer_admin');
    }

    public function website()
    {
        $data['menu'] = 'website';
        $data['title'] = 'Setting Website';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['website'] =  $this->db->get('website')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');

        $id = $this->input->post('id');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/website', $data);
            $this->load->view('template/footer_admin');
        } else {
            $data = [
                'nama' => $this->input->post('nama'),
                'deskripsi' => $this->input->post('deskripsi'),
                'alamat' => $this->input->post('alamat'),
                'email' => $this->input->post('email'),
                'telp' => $this->input->post('no_telp')
            ];

            $this->db->where('id', $id);
            $this->db->update('website', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong>  Update data website berhasil!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('admin/website');
        }
    }


    public function utama()
    {
        $data['menu'] = 'home';
        $data['title'] = 'Utama';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['home'] =  $this->db->get('home')->result_array();
        $data['img'] =  $this->db->get('home')->row_array();

        $this->form_validation->set_rules('judul', 'Judul', 'required');
        $this->form_validation->set_rules('isi', 'Isi', 'required');
        $this->form_validation->set_rules('tombol', 'Tombol', 'required');
        $this->form_validation->set_rules('link', 'Link', 'required');

        $id = $this->input->post('id');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/utama', $data);
            $this->load->view('template/footer_admin');
        } else {
            $data = [
                'judul' => $this->input->post('judul'),
                'isi' => $this->input->post('isi'),
                'tombol' => $this->input->post('tombol'),
                'link' => $this->input->post('link')
            ];

            $this->db->where('id', $id);
            $this->db->update('home', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Update data Utama berhasil!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('admin/utama');
        }
    }
    


    public function about()
    {
        $data['menu'] = 'website';
        $data['title'] = 'About';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['about'] =  $this->db->get('about')->result_array();
        $data['img'] =  $this->db->get('about')->row_array();

        $this->form_validation->set_rules('about', 'About', 'required');
        $this->form_validation->set_rules('visi', 'Visi', 'required');
        $this->form_validation->set_rules('misi', 'Misi', 'required');

        $id = $this->input->post('id');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/about', $data);
            $this->load->view('template/footer_admin');
        } else {
            $data = [
                'about' => $this->input->post('about'),
                'visi' => $this->input->post('visi'),
                'misi' => $this->input->post('misi')
            ];

            $this->db->where('id', $id);
            $this->db->update('about', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Update data website berhasil!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('admin/about');
        }
    }

    public function maps()
    {
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['website'] =  $this->db->get('website')->result_array();

        $this->form_validation->set_rules('maps', 'maps', 'required');
        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/website', $data);
            $this->load->view('template/footer_admin');
        } else {
            $data = [
                'maps' => $this->input->post('maps'),
            ];

            $this->db->update('website', $data);
            $this->session->set_flashdata('messageMaps', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Update data Maps website berhasil!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('admin/website');
        }
    }


    public function setting()
    {
        $data['menu'] = 'menu-5';
        $data['title'] = 'Setting Akun';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|max_length[15]', [
            'max_length' => 'Kolom Nama Lengkap tidak boleh lebih dari 15 karakter.'
        ]);

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/setting', $data);
            $this->load->view('template/footer_admin');
        } else {
            $id = $this->input->post('id');
            $edit = [
                'nama' => $this->security->xss_clean($this->input->post('nama')),
                'alamat' => $this->security->xss_clean($this->input->post('alamat')),
                'telp' => $this->security->xss_clean($this->input->post('no_hp'))
            ];

            $this->db->where('id', $id);
            $this->db->update('karyawan', $edit);

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success alert-dismissible fade show" role="alert">
             Akun kamu berhasil di Update!
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
         </button>
         </div>'
            );
            redirect('admin/setting');
        }
    }

    public function edit_pass()
    {
        $data['menu'] = 'menu-5';
        $data['title'] = 'Setting Akun';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
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
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/setting', $data);
            $this->load->view('template/footer_admin');
        } else {
            $old_password = $this->input->post('old_password');
            $new_password = $this->input->post('password1');
            if (!password_verify($old_password, $data['user']['password'])) {
                $this->session->set_flashdata(
                    'messagepp',
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Password lama salah!
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
         </button>
         </div>'
                );
                redirect('admin/setting');
            } else {
                if ($old_password == $new_password) {
                    $this->session->set_flashdata(
                        'messagepp',
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Password baru tidak boleh sama dengan Password saat ini!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>'
                    );
                    redirect('admin/setting');
                } else {
                    // password sudah ok
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                    $this->db->set('password', $password_hash);
                    $this->db->where('email', $this->session->userdata('email'));
                    $this->db->update('karyawan');

                    $this->session->set_flashdata(
                        'messagepp',
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Password berhasil di ubah! :)
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             </div>'
                    );
                    redirect('admin/setting');
                }
            }
        }
    }


    public function masjid()
    {
        $data['menu'] = 'menu-9';
        $data['title'] = 'Data Masjid';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['masjid']=$this->Masjid_model->getAllMasjid()->result_array();

            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/masjid', $data);
            $this->load->view('template/footer_admin');
        
    }

    public function tambah_masjid()
	{
        $data['menu'] = 'menu-9';
        $data['title'] = 'Data Masjid';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['masjid']=$this->Masjid_model->getAllMasjid()->result_array();

        $this->form_validation->set_rules('nama_masjid', 'Nama Masjid', 'required');
        $this->form_validation->set_rules('sapi', 'Sapi', 'required');
        $this->form_validation->set_rules('kambing', 'Kambing', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required');

		if ($this->form_validation->run() == FALSE ) {
			$this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/tambah_masjid', $data);
            $this->load->view('template/footer_admin');	
		} else {
			$this->Masjid_model->tambahdata($data);
			$this->session->set_flashdata('flash', 'Ditambahkan');
			redirect('admin/masjid');
		}	
	}

    public function ubah_masjid($id)
	{
        $data['menu'] = 'menu-9';
        $data['title'] = 'Data Masjid';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['masjid']=$this->Masjid_model->getMasjidById($id);

        $this->form_validation->set_rules('nama_masjid', 'Nama Masjid', 'required');
        $this->form_validation->set_rules('sapi', 'Sapi', 'required');
        $this->form_validation->set_rules('kambing', 'Kambing', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required');

		if ($this->form_validation->run() == FALSE ) {
			$this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/ubah_masjid', $data);
            $this->load->view('template/footer_admin');	
		} else {
			$this->Masjid_model->ubahdata($data);
			$this->session->set_flashdata('flash', 'Diubah');
			redirect('admin/masjid');
		}	
	}

    public function hapus_masjid($id)
	{
		$this->Masjid_model->hapusdata($id);
		$this->session->set_flashdata('flash' , 'Dihapus');
		redirect('admin/masjid');
	}

    public function jamaah()
    {
        $data['menu'] = 'menu-9';
        $data['title'] = 'Data Jamaah';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['jamaah']=$this->Jamaah_model->getAllJamaah()->result_array();

            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/jamaah', $data);
            $this->load->view('template/footer_admin');
        
    }

    public function tambah_jamaah()
	{
        $data['menu'] = 'menu-9';
        $data['title'] = 'Data Jamaah';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['jamaah']=$this->Jamaah_model->getAllJamaah()->result_array();

        $this->form_validation->set_rules('nama', 'Nama Jamaah', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('blok', 'Blok', 'required');
        $this->form_validation->set_rules('no', 'Nomor', 'required');
        $this->form_validation->set_rules('status', 'Status');

		if ($this->form_validation->run() == FALSE ) {
			$this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/tambah_jamaah', $data);
            $this->load->view('template/footer_admin');	
		} else {
			$this->Jamaah_model->tambahdata($data);
			$this->session->set_flashdata('flash', 'Ditambahkan');
			redirect('admin/jamaah');
		}	
	}
    

    public function ubah_jamaah($id)
	{
        $data['menu'] = 'menu-9';
        $data['title'] = 'Data Jamaah';
        $data['web'] =  $this->db->get('website')->row_array();
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['jamaah']=$this->Jamaah_model->getJamaahById($id);

        $this->form_validation->set_rules('nama', 'Nama Jamaah', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('blok', 'Blok', 'required');
        $this->form_validation->set_rules('no', 'Nomor', 'required');
        $this->form_validation->set_rules('status', 'Status');

		if ($this->form_validation->run() == FALSE ) {
			$this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/ubah_jamaah', $data);
            $this->load->view('template/footer_admin');	
		} else {
			$this->Jamaah_model->ubahdata($data);
			$this->session->set_flashdata('flash', 'Diubah');
			redirect('admin/jamaah');
		}	
	}

    public function hapus_jamaah($id)
	{
		$this->Jamaah_model->hapusdata($id);
		$this->session->set_flashdata('flash' , 'Dihapus');
		redirect('admin/jamaah');
	}

    public function karyawan()
    {
        $data['menu'] = 'menu-9';
        $data['title'] = 'Data Karyawan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->where('role_id !=', 1);
        $data['karyawan'] =  $this->db->get('karyawan')->result_array();

        $data['kelas'] = $this->db->get("data_kelas")->result_array();
        $data['divisi'] = $this->db->get("data_divisi")->result_array();
        $data['pendidikan'] = $this->db->get("data_pendidikan")->result_array();

        $this->db->order_by('nama', 'asc');
        $data['prov'] = $this->db->get('provinsi')->result_array();
        $data['kab'] = $this->db->get('kabupaten')->result_array();

        $this->form_validation->set_rules('nama', 'Nama Karyawan', 'required');
        $this->form_validation->set_rules('email', 'Email Karyawan', 'required');
        $this->form_validation->set_rules('password', 'Pasword Karyawan', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat Karyawan', 'required');
        $this->form_validation->set_rules('telp', 'Nomor Hp', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/karyawan', $data);
            $this->load->view('template/footer_admin');
        } else {
            $tgl = date('Y-m-d');
            $nama = $this->input->post('nama');
            $data = [
                'id_fingerprint' => $this->input->post('id_fp'),
                'nama' => $nama,
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'alamat' => $this->input->post('alamat'),
                'telp' => $this->input->post('telp'),
                'id_divisi' => $this->input->post('divisi'),
                'intensif' => $this->input->post('intensif'),
                'jam_mengajar' => $this->input->post('jam_mengajar'),
                'nominal_jam' => $this->input->post('nominal_jam'),
                'bpjs' => $this->input->post('bpjs'),
                'koperasi' => $this->input->post('koperasi'),
                'simpanan' => $this->input->post('simpanan'),
                'tabungan' => $this->input->post('tabungan'),
                'id_pend' => $this->input->post('pendidikan'),
                'id_kelas' => $this->input->post('kelas'),
                'role_id' => $this->input->post('level'),
                'status' => '1',
                'date_created' => $tgl
            ];

            $this->db->insert('karyawan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Data karyawan <strong>' . $nama . '</strong> berhasil ditambahkan!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('admin/karyawan');
        }
    }

    public function tambah_karyawan()
    {
        $data['menu'] = 'menu-9';
        $data['title'] = 'Tambah Karyawan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['pendidikan'] = $this->db->get('data_pendidikan')->result_array();
        $data['divisi'] = $this->db->get('data_divisi')->result_array();

        $this->form_validation->set_rules('nik', 'NIK', 'required|is_unique[karyawan.nik]', [
            'is_unique' => 'Nik ini sudah terdaftar!',
            'required' => 'Nik tidak boleh kosong!'
        ]);
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[karyawan.email]', [
            'is_unique' => 'Email ini sudah terdaftar!',
            'required' => 'Email tidak boleh kosong!'
        ]);
        $this->form_validation->set_rules('jk', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('ttl', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/tambah_karyawan', $data);
            $this->load->view('template/footer_admin');
        } else {

            $tgl = date('Y-m-d');
            $nama = $this->input->post('nama');

            $data = [
                'id_fingerprint' => $this->input->post('id_fp'),
                'nik' => $this->input->post('nik'),
                'nama' => $nama,
                'jk' => $this->input->post('jk'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'ttl' => $this->input->post('ttl'),
                'telp' => $this->input->post('no_telp'),
                'alamat' => $this->input->post('alamat'),
                'id_divisi' => $this->input->post('divisi'),
                'intensif' => $this->input->post('intensif'),
                'jam_mengajar' => $this->input->post('jam_mengajar'),
                'nominal_jam' => $this->input->post('nominal_jam'),
                'bpjs' => $this->input->post('bpjs'),
                'koperasi' => $this->input->post('koperasi'),
                'simpanan' => $this->input->post('simpanan'),
                'tabungan' => $this->input->post('tabungan'),
                'id_pend' => $this->input->post('pendidikan'),
                'id_kelas' => $this->input->post('kelas'),
                'role_id' => $this->input->post('level'),
                'status' => 1,
                'date_created' => $tgl,
            ];

            $this->db->insert('karyawan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Data Karyawan <strong>' . $nama . '</strong> berhasil ditambahkan!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('admin/karyawan');
        }
    }


    public function update_karyawan()
    {
        $id      = $this->input->get('id');

        $data['menu'] = 'menu-9';
        $data['title'] = 'Update Karyawan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['pendidikan'] = $this->db->get('data_pendidikan')->result_array();
        $data['kelas'] = $this->db->get('data_kelas')->result_array();
        $data['karyawan'] =  $this->db->get_where('karyawan', ['id' => $id])->row_array();
        $data['divisi'] =  $this->db->get('data_divisi')->result_array();

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/edit_karyawan', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('nama');
            $data = [
                'id_fingerprint' => $this->input->post('id_fp'),
                'nik' => $this->input->post('nik'),
                'nama' => $nama,
                'jk' => $this->input->post('jk'),
                'email' => $this->input->post('email'),
                'ttl' => $this->input->post('ttl'),
                'telp' => $this->input->post('no_telp'),
                'alamat' => $this->input->post('alamat'),
                'id_divisi' => $this->input->post('divisi'),
                'intensif' => $this->input->post('intensif'),
                'jam_mengajar' => $this->input->post('jam_mengajar'),
                'nominal_jam' => $this->input->post('nominal_jam'),
                'bpjs' => $this->input->post('bpjs'),
                'koperasi' => $this->input->post('koperasi'),
                'simpanan' => $this->input->post('simpanan'),
                'tabungan' => $this->input->post('tabungan'),
                'id_pend' => $this->input->post('pendidikan'),
                'id_kelas' => $this->input->post('kelas'),
                'role_id' => $this->input->post('level'),
                'status' => $this->input->post('status')
            ];

            $this->db->where('id', $id);
            $this->db->update('karyawan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data karyawan <strong>' . $nama . '</strong> berhasil diupdate :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
            redirect('admin/update_karyawan?id=' . $id . '');
        }
    }


    public function tagline()
    {

        $data['menu'] = 'home';
        $data['title'] = 'Tagline';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['tagline'] =  $this->db->get('tagline')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/tagline', $data);
            $this->load->view('template/footer_admin');
        } else {
            $id     = $this->input->post('id');
            $img        = $_FILES['gambar'];
            $nama     = $this->input->post('nama');
            $deskripsi     = $this->input->post('deskripsi');

            if ($img['name'] == '') {

                $data = [
                    'nama' => $nama,
                    'deskripsi' => $deskripsi
                ];
            } else {
                $config['upload_path'] = './assets/img/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size']  = '8048';
                $config['remove_space'] = TRUE;

                $this->load->library('upload', $config); // Load konfigurasi uploadnya
                if (!$this->upload->do_upload('gambar')) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar Gagal di Upload :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                    redirect('admin/tagline');
                } else {
                    $this->db->where('id', $id);
                    $g =  $this->db->get('tagline')->row_array();
                    unlink("./assets/img/" . $g['img']);
                    $gambar = $this->upload->data('file_name');

                    $data = [
                        'nama' => $nama,
                        'deskripsi' => $deskripsi,
                        'img' => $gambar
                    ];
                }
            }

            $this->db->where('id', $id);
            $this->db->update('tagline', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Tagline <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/tagline');
        }
    }


    public function kontak()
    {

        $data['menu'] = 'kontak';
        $data['title'] = 'Data Kontak';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['kontak'] =  $this->db->get('kontak')->result_array();

        $this->db->where('status', 1);
        $kontak =  $this->db->get('kontak')->row_array();


        $this->db->set('status', 2);
        $this->db->update('kontak');


        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/website/kontak', $data);
        $this->load->view('template/footer_admin');
    }


    public function tambah_acara()
    {

        $data['menu'] = 'acara';
        $data['title'] = 'Tambah Acara';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['acara'] =  $this->db->get('acara')->result_array();
        $data['kategori'] =  $this->db->get('kategori_acara')->result_array();

        $this->form_validation->set_rules('judul', 'Judul', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/acara/tambah_acara', $data);
            $this->load->view('template/footer_admin');
        } else {

            $judul = $this->input->post('judul');
            $img        = $_FILES['gambar'];

            if ($img['name'] == '') {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gambar Tidak Boleh Kosong :)
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                redirect('admin/tambah_acara');
            } else {
                $config['upload_path'] = './assets/img/blog/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size']  = '8048';
                $config['remove_space'] = TRUE;

                $this->load->library('upload', $config); // Load konfigurasi uploadnya
                if (!$this->upload->do_upload('gambar')) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar Gagal di Upload :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                    redirect('admin/tambah_acara');
                } else {

                    $gambar = $this->upload->data('file_name');

                    $data = [
                        'judul' => $judul,
                        'deskripsi'   => $this->input->post('isi'),
                        'id_kat' => $this->input->post('kategori'),
                        'img' => $gambar,
                        'tempat' => $this->input->post('tempat'),
                        'tgl' => $this->input->post('tgl'),
                        'jam' => $this->input->post('jam'),
                        'id_peng' => $data['user']['id']
                    ];
                }
            }

            $this->db->insert('acara', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Acara <strong>' . $judul . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/acara');
        }
    }



    public function acara()
    {

        $data['menu'] = 'acara';
        $data['title'] = 'Data Acara';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['acara'] =  $this->db->get('acara')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/acara/acara', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('nama');
            $uniq  = strtolower($nama);
            $data = [
                'nama' => $nama,
                'uniq' => preg_replace("/[^A-Za-z0-9 ]/", "", $uniq)
            ];
            $this->db->insert('acara', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Acara <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/acara');
        }
    }


    public function kategori_acara()
    {
        $segmen = $this->uri->segment(3);
        $data['menu'] = 'acara';
        $data['title'] = 'Kategori Acara';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['acara'] =  $this->db->get('kategori_acara')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/acara/kategori', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('nama');
            $this->db->where('nama', $nama);
            $cek_data =  $this->db->get('kategori_acara')->row_array();

            if ($cek_data['nama'] == $nama) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data Kategori <strong>' . $nama . '</strong> Sudah Ada :(
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                if ($segmen == 'tambah') {
                    redirect('admin/tambah_acara');
                } elseif ($segmen == 'edit') {
                    redirect('admin/edit_acara?id=' . $this->uri->segment(4));
                } else {
                    redirect('admin/kategori_acara');
                }
            }
            $uniq  = strtolower($nama);
            $data = [
                'nama' => $nama,
                'uniq' => preg_replace("/[^A-Za-z0-9 ]/", "", $uniq)
            ];
            $this->db->insert('kategori_acara', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kategori Acara <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            if ($segmen == 'tambah') {
                redirect('admin/tambah_acara');
            } elseif ($segmen == 'edit') {
                redirect('admin/edit_acara?id=' . $this->uri->segment(4));
            } else {
                redirect('admin/kategori_acara');
            }
        }
    }


    public function gallery()
    {

        $data['menu'] = 'gallery';
        $data['title'] = 'Data Gallery';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['gallery'] =  $this->db->get('gallery')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/gallery/gallery', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('nama');
            $uniq  = strtolower($nama);
            $data = [
                'nama' => $nama,
                'uniq' => preg_replace("/[^A-Za-z0-9 ]/", "", $uniq)
            ];
            $this->db->insert('gallery', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Gallery <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/gallery');
        }
    }


    public function kategori_gallery()
    {
        $segmen = $this->uri->segment(3);
        $data['menu'] = 'gallery';
        $data['title'] = 'Kategori Gallery';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['gallery'] =  $this->db->get('kategori_gallery')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/gallery/kategori', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('nama');
            $this->db->where('nama', $nama);
            $cek_data =  $this->db->get('kategori_gallery')->row_array();

            if ($cek_data['nama'] == $nama) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data Kategori <strong>' . $nama . '</strong> Sudah Ada :(
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                if ($segmen == 'tambah') {
                    redirect('admin/tambah_gallery');
                } elseif ($segmen == 'edit') {
                    redirect('admin/edit_gallery?id=' . $this->uri->segment(4));
                } else {
                    redirect('admin/kategori_gallery');
                }
            }
            $uniq  = strtolower($nama);
            $data = [
                'nama' => $nama,
                'uniq' => preg_replace("/[^A-Za-z0-9 ]/", "", $uniq)
            ];
            $this->db->insert('kategori_gallery', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kategori Gallery <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            if ($segmen == 'tambah') {
                redirect('admin/tambah_gallery');
            } elseif ($segmen == 'edit') {
                redirect('admin/edit_gallery?id=' . $this->uri->segment(4));
            } else {
                redirect('admin/kategori_gallery');
            }
        }
    }

    public function tambah_gallery()
    {

        $data['menu'] = 'gallery';
        $data['title'] = 'Tambah Gallery';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['gallery'] =  $this->db->get('gallery')->result_array();
        $data['kategori'] =  $this->db->get('kategori_gallery')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/gallery/tambah_gallery', $data);
            $this->load->view('template/footer_admin');
        } else {

            $judul = $this->input->post('nama');
            $img        = $_FILES['gambar'];

            if ($img['name'] == '') {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gambar Utama Tidak Boleh Kosong :)
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                redirect('admin/tambah_gallery');
            } else {
                $config['upload_path'] = './assets/img/gallery/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size']  = '8048';
                $config['remove_space'] = TRUE;

                $this->load->library('upload', $config); // Load konfigurasi uploadnya
                if (!$this->upload->do_upload('gambar')) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar Gagal di Upload :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                    redirect('admin/tambah_gallery');
                } else {

                    $gambar = $this->upload->data('file_name');

                    if ($this->upload->do_upload('gambar1')) {
                        $img1  = $this->upload->data('file_name');
                    } else {
                        $img1  = '';
                    }
                    if ($this->upload->do_upload('gambar2')) {
                        $img2  = $this->upload->data('file_name');
                    } else {
                        $img2  = '';
                    }
                    if ($this->upload->do_upload('gambar3')) {
                        $img3  = $this->upload->data('file_name');
                    } else {
                        $img3  = '';
                    }

                    $data = [
                        'nama' => $judul,
                        'deskripsi'   => $this->input->post('isi'),
                        'id_kat' => $this->input->post('kategori'),
                        'id_peng' => $data['user']['id'],
                        'img' => $gambar,
                        'img1' => $img1,
                        'img2' => $img2,
                        'img3' => $img3,
                        'tgl' => date('Y-m-d')
                    ];

                    $this->db->insert('gallery', $data);

                    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data Gallery <strong>' . $judul . '</strong> berhasil ditambahkan :)
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      </div>');
                    redirect('admin/gallery');
                }
            }
        }
    }

    public function email_sender()
    {
        $data['menu'] = 'website';
        $data['title'] = 'Email Sender';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['email_sender'] =  $this->db->get('email_sender')->result_array();

        $this->form_validation->set_rules('email', 'Email', 'required');

        $id = $this->input->post('id');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/email_sender', $data);
            $this->load->view('template/footer_admin');
        } else {
            $data = [
                'protocol' => $this->input->post('protocol'),
                'host' => $this->input->post('host'),
                'port' => $this->input->post('port'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'charset' => $this->input->post('charset')
            ];

            $this->db->where('id', $id);
            $this->db->update('email_sender', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Update data Email Sender berhasil!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('admin/email_sender');
        }
    }


    // ---------------- SEND EMAIL SENDER ----------------- //

    private function sendEmail($id, $email, $subjek, $pesan, $type)
    {
        // PHPMailer object
        $response = false;
        $mail = new PHPMailer(true);

        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $data['kontak'] =  $this->db->get_where('kontak', ['id' => $id])->row_array();
        $data['link_web'] = base_url();
        $data['email'] = $email;
        $data['pesan'] = $pesan;
        $web = $data['web'];

        $esen =  $this->db->get('email_sender')->row_array();


        // SMTP configuration
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();
        $mail->Host     = $esen['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $esen['email']; // user email
        $mail->Password = $esen['password']; // password email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port     = $esen['port'];

        $mail->SMTPKeepAlive = true;

        $mail->setFrom($esen['email'], ''); // user email
        $mail->addReplyTo($esen['email'], ''); //user email
        $mail->IsHTML(true);

        // Add a recipient
        $mail->addAddress($email); //email tujuan pengiriman email

        // Email subject
        // $mail->Subject = $subjek; 

        // Set email format to HTML
        $mail->isHTML(true);

        // Email body content
        $body_test = $this->load->view('email/test', $data, true);
        $body_balas = $this->load->view('email/balas', $data, true);

        // $body_test = 'test';

        if ($type == 'test') {
            $mail->Subject = $subjek . ' - ' . $web['nama'];
            $mail->Body = $body_test;
        } else if ($type == 'balas') {
            $mail->Subject = $subjek . ' - ' . $web['nama'];
            $mail->Body = $body_balas;
        }

        // $mail->Body = $mailContent;

        // Send email
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return true;
        }


        // $config = [
        //     'protocol'  => $esen['protocol'],
        //     'smtp_host' => $esen['host'],
        //     'smtp_user' => $esen['email'],
        //     'smtp_pass' => $esen['password'],
        //     'smtp_port' => $esen['port'],
        //     'smtp_crypto' => 'ssl',
        //     'mailtype'  => 'html',
        //     'charset'   => $esen['charset'],
        //     'newline'   => "\r\n"
        // ];

        // $this->load->library('email', $config);
        // $this->email->set_newline("\r\n");
        // $this->email->set_header('Content-Type', 'text/html');

        // $this->email->from($esen['email'], $web['nama']);
        // $this->email->to($email);

        // $data['link_web'] = base_url();
        // $data['email'] = $email;
        // $data['pesan']   = $pesan;

        // $body_test = $this->load->view('email/test', $data, TRUE);
        // $body_balas = $this->load->view('email/balas', $data, TRUE);

        // if ($type == 'test') {
        //     $this->email->subject($subjek . ' - ' . $web['nama']);
        //     $this->email->message($body_test);
        // } else if ($type == 'balas') {
        //     $this->email->subject($subjek . ' - ' . $web['nama']);
        //     $this->email->message($body_balas);
        // }

        // if ($this->email->send()) {
        //     return true;
        // } else {
        //     echo $this->email->print_debugger();
        //     die;
        // }
    }


    public function test_email_sender()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            redirect('admin/email_sender');
        } else {
            $id = NULL;
            $email = $this->input->post('email');
            $subjek = $this->input->post('subjek');
            $pesan = $this->input->post('pesan');

            $this->sendEmail($id, $email, $subjek, $pesan, 'test');

            $this->session->set_flashdata(
                'messageTest',
                '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> Email berhasil di kirim ke' . $email . '.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>'
            );
            redirect('admin/email_sender');
        }
    }


    public function balas_kontak()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            redirect('admin/kontak');
        } else {

            $id = $this->input->post('id');
            $email = $this->input->post('email');
            $subjek = $this->input->post('subjek');
            $pesan = $this->input->post('pesan');

            $this->sendEmail($id, $email, $subjek, $pesan, 'balas');

            $this->db->set('status', 3);
            $this->db->where('id', $id);
            $this->db->update('kontak');

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> Email berhasil di kirim ke' . $email . '.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>'
            );
            redirect('admin/kontak');
        }
    }

    public function edit_gallery()
    {
        $id = $this->input->get('id');
        $data['menu'] = 'gallery';
        $data['title'] = 'Edit Gallery';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->where('id', $id);
        $data['gallery'] =  $this->db->get('gallery')->result_array();
        $data['kategori'] =  $this->db->get('kategori_gallery')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/gallery/edit_gallery', $data);
            $this->load->view('template/footer_admin');
        } else {
            $judul = $this->input->post('nama');
            $config['upload_path'] = './assets/img/gallery/';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config); // Load konfigurasi uploadnya

            $this->db->where('id', $id);
            $g =  $this->db->get('gallery')->row_array();


            if ($this->upload->do_upload('gambar')) {
                $gambar  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img']);
            } else {
                $gambar  = $g['img'];
            }
            if ($this->upload->do_upload('gambar1')) {
                $img1  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img1']);
            } else {
                $img1  = $g['img1'];
            }
            if ($this->upload->do_upload('gambar2')) {
                $img2  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img2']);
            } else {
                $img2  = $g['img2'];
            }
            if ($this->upload->do_upload('gambar3')) {
                $img3  = $this->upload->data('file_name');
                unlink("./assets/img/gallery/" . $g['img3']);
            } else {
                $img3  = $g['img3'];
            }

            $data = [
                'nama' => $judul,
                'deskripsi'   => $this->input->post('isi'),
                'id_kat' => $this->input->post('kategori'),
                'img' => $gambar,
                'img1' => $img1,
                'img2' => $img2,
                'img3' => $img3
            ];

            $this->db->where('id', $id);
            $this->db->update('gallery', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Gallery <strong>' . $judul . '</strong> berhasil di Update :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/edit_gallery?id=' . $id);
        }
    }


    public function edit_acara()
    {
        $id = $this->input->get('id');
        $data['menu'] = 'acara';
        $data['title'] = 'Edit Acara';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->where('id', $id);
        $data['acara'] =  $this->db->get('acara')->result_array();
        $data['kategori'] =  $this->db->get('kategori_acara')->result_array();

        $this->form_validation->set_rules('judul', 'Judul', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/website/acara/edit_acara', $data);
            $this->load->view('template/footer_admin');
        } else {
            $judul = $this->input->post('judul');
            $id     = $this->input->post('id');
            $img        = $_FILES['gambar'];

            if ($img['name'] == '') {
                $data = [
                    'judul' => $judul,
                    'deskripsi'   => $this->input->post('isi'),
                    'id_kat' => $this->input->post('kategori'),
                    'tempat' => $this->input->post('tempat'),
                    'tgl' => $this->input->post('tgl'),
                    'jam' => $this->input->post('jam')
                ];
            } else {
                $config['upload_path'] = './assets/img/blog/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size']  = '8048';
                $config['remove_space'] = TRUE;

                $this->load->library('upload', $config); // Load konfigurasi uploadnya
                if (!$this->upload->do_upload('gambar')) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar Gagal di Upload :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                    redirect('admin/edit_acara?id=' . $id);
                } else {
                    $this->db->where('id', $id);
                    $g =  $this->db->get('acara')->row_array();
                    unlink("./assets/img/blog/" . $g['img']);
                    $gambar = $this->upload->data('file_name');

                    $data = [
                        'judul' => $judul,
                        'deskripsi'   => $this->input->post('isi'),
                        'id_kat' => $this->input->post('kategori'),
                        'img' => $gambar,
                        'tempat' => $this->input->post('tempat'),
                        'tgl' => $this->input->post('tgl'),
                        'jam' => $this->input->post('jam')
                    ];
                }
            }

            $this->db->where('id', $id);
            $this->db->update('acara', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Acara <strong>' . $judul . '</strong> berhasil di Update :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/edit_acara?id=' . $id);
        }
    }

    public function export_data()
    {
        $segmen = $this->uri->segment(3);
        //ambil data
        if ($segmen == 'ppdb') {
            $get    = $this->Export_model->getPPDB();
        } else {
            $get    = $this->Export_model->getAll();
        }
        //validasi jumlah data
        if ($get->num_rows() > 0) {
            $writer = WriterEntityFactory::createXLSXWriter();

            if ($segmen == 'ppdb') {
                $writer->openToBrowser("data_ppdb.xlsx");
            } else {
                $writer->openToBrowser("data_jamaah.xlsx");
            }

            //silahkan sobat sesuaikan dengan data yang ingin sobat tampilkan
            $header = [
                WriterEntityFactory::createCell('No'),
                WriterEntityFactory::createCell('Nama Jamaah'),
                WriterEntityFactory::createCell('Alamat'),
                WriterEntityFactory::createCell('Blok'),
                WriterEntityFactory::createCell('Nomor'),
                WriterEntityFactory::createCell('Status')
            ];

            /** Tambah row satu kali untuk header */
            $singleRow = WriterEntityFactory::createRow($header);
            $writer->addRow($singleRow); //tambah row untuk header data

            $data   = array(); //siapkan variabel array untuk menampung data
            $no     = 1;

            //looping pembacaan data
            foreach ($get->result() as $key) {
                //masukkan data dari database ke variabel array
                //silahkan sobat sesuaikan dengan nama field pada tabel database
                $jamaah    = array(
                    WriterEntityFactory::createCell($no++),
                    WriterEntityFactory::createCell($key->nama),
                    WriterEntityFactory::createCell($key->alamat),
                    WriterEntityFactory::createCell($key->blok),
                    WriterEntityFactory::createCell($key->no),
                    WriterEntityFactory::createCell($key->status),
                );

                array_push($data, WriterEntityFactory::createRow($jamaah)); //masukkan variabel array siswa ke variabel array data
            }

            $writer->addRows($data); // tambahkan row untuk data siswa

            $writer->close(); //tutup spout writer
        } else {
            echo "Data tidak ditemukan";
        }
    }
}
