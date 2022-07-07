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

        $data['kelas'] = $this->db->get("data_kelas")->num_rows();
        $data['pendidikan'] = $this->db->get("data_pendidikan")->result_array();
        $data['about'] = $this->db->get("about")->row_array();

        $data['sum_siswa'] = $this->db->get("siswa")->num_rows();
        $data['sum_peng'] = $this->db->get("karyawan")->num_rows();
        $data['sum_konsel'] = $this->db->get("konseling")->num_rows();

        $data['sum_izin'] = $this->db->get("perizinan")->num_rows();
        $data['sum_takzir'] = $this->db->get("pelanggaran")->num_rows();
        $data['sum_kontak'] = $this->db->get("kontak")->num_rows();
        $data['sum_gallery'] = $this->db->get("gallery")->num_rows();
        $data['sum_acara'] = $this->db->get("acara")->num_rows();

        $this->db->where('jk', 'L');
        $data['sum_pria'] = $this->db->get("siswa")->num_rows();

        $this->db->where('jk', 'P');
        $data['sum_wanita'] = $this->db->get("siswa")->num_rows();

        $this->db->where('point !=', 100);
        $this->db->order_by('point', 'ASC');
        $data['siswa'] = $this->db->get('siswa', 7)->result_array();
        $this->db->where('jumlah !=', 0);
        $this->db->order_by('jumlah', 'DESC');
        $data['pelanggaran'] = $this->db->limit(7)->get('data_pelanggaran')->result_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('template/footer_admin');
    }


    public function daftar_siswa()
    {
        $data['menu'] = 'menu-1';
        $data['title'] = 'Daftar siswa';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $filter   = $this->input->post('filter');
        $id_prov     = $this->input->post('prov');
        $kab      = $this->input->post('kab');

        $prov =  $data['prov'] = $this->db->get_where('provinsi', ['id_prov' => $id_prov])->row_array();


        $this->db->order_by('nama', 'asc');
        $data['prov'] = $this->db->get('provinsi')->result_array();
        $data['kab'] = $this->db->get('kabupaten')->result_array();

        if ($filter) {
            if (!empty($id_prov)) {
                $this->db->where('prov', $prov['nama']);
            }
            if (!empty($kab)) {
                $this->db->where('kab', $kab);
                $kota = ' kabupaten <strong>' . $kab . '</strong>';
            } else {
                $kota = '';
            }

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           <strong>Success!</strong> Sortir siswa dari provinsi <strong>' . $prov['nama'] . '</strong>' . $kota . '.
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
        }

        $this->db->order_by('point', 'ASC');
        $data['siswa'] =  $this->db->get('siswa')->result_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/daftar_siswa', $data);
        $this->load->view('template/footer_admin');


        if ($this->input->post('submit', TRUE) == 'upload') {
            $config['upload_path']      = './temp_doc/'; //siapkan path untuk upload file
            $config['allowed_types']    = 'xlsx|xls'; //siapkan format file
            $config['file_name']        = 'doc' . time(); //rename file yang diupload

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('excel')) {
                //fetch data upload
                $file   = $this->upload->data();

                $reader = ReaderEntityFactory::createXLSXReader(); //buat xlsx reader
                $reader->open('./temp_doc/' . $file['file_name']); //open file xlsx yang baru saja diunggah

                //looping pembacaan sheet dalam file        
                foreach ($reader->getSheetIterator() as $sheet) {
                    $numRow = 1;

                    //siapkan variabel array kosong untuk menampung variabel array data
                    $save   = array();

                    //looping pembacaan row dalam sheet
                    foreach ($sheet->getRowIterator() as $row) {

                        if ($numRow > 1) {
                            //ambil cell
                            $cells = $row->getCells();

                            $data = array(
                                'point'         => '100',
                                'nik'           => $cells[0],
                                'nis'           => $cells[1],
                                'nama'          => $cells[2],
                                'email'         => $cells[3],
                                'no_hp'         => $cells[4],
                                'password'      => password_hash($cells[1], PASSWORD_DEFAULT),
                                'jk'            => $cells[5],
                                'ttl'           => $cells[6],
                                'prov'          => $cells[7],
                                'kab'           => $cells[8],
                                'alamat'        => $cells[9],
                                'nama_ayah'     => $cells[10],
                                'nama_ibu'      => $cells[11],
                                'pek_ayah'      => $cells[12],
                                'pek_ibu'       => $cells[13],
                                'nama_wali'     => $cells[14],
                                'pek_wali'      => $cells[15],
                                'peng_ortu'     => $cells[16],
                                'no_telp'       => $cells[17],
                                'thn_msk'       => $cells[18],
                                'sekolah_asal'  => $cells[19],
                                'kelas'         => $cells[20],
                                'diniyah'       => $cells[21],
                                'status'        => 1,
                                'role_id'       => 5
                            );

                            //tambahkan array $data ke $save
                            array_push($save, $data);
                        }

                        $numRow++;
                    }

                    //simpan data ke database
                    $this->Import_model->simpan($save);

                    //tutup spout reader
                    $reader->close();

                    //hapus file yang sudah diupload
                    unlink('./temp_doc/' . $file['file_name']);

                    //tampilkan pesan success dan redirect ulang ke index controller import
                    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> berhasil mengimport data :)
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      </div>');
                    redirect('admin/daftar_siswa');
                }
            } else {
                //tampilkan pesan error jika file gagal diupload
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> ' . $this->upload->display_errors() . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/daftar_siswa');
            }
        }
    }

    public function tambah_siswa()
    {

        $data['menu'] = 'menu-1';
        $data['title'] = 'Tambah siswa';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('nama', 'asc');
        $data['prov'] = $this->db->get('provinsi')->result_array();
        $data['pendidikan'] = $this->db->get('data_pendidikan')->result_array();

        $this->form_validation->set_rules('nik', 'NIK', 'required|is_unique[siswa.nik]', [
            'is_unique' => 'Nik ini sudah terdaftar!',
            'required' => 'Nik tidak boleh kosong!'
        ]);
        $this->form_validation->set_rules('nis', 'NIS', 'required|is_unique[siswa.nis]', [
            'is_unique' => 'Nis ini sudah terdaftar!',
            'required' => 'Nis tidak boleh kosong!'
        ]);
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[siswa.email]', [
            'is_unique' => 'Email ini sudah terdaftar!',
            'required' => 'Email tidak boleh kosong!'
        ]);
        $this->form_validation->set_rules('jk', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('ttl', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('prov', 'Provinsi', 'required');
        $this->form_validation->set_rules('kab', 'Kota', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('nama_ayah', 'Nama Ayah', 'required');
        $this->form_validation->set_rules('nama_ibu', 'Nama ibu', 'required');
        $this->form_validation->set_rules('pek_ayah', 'Pekerjaan Ayah', 'required');
        $this->form_validation->set_rules('pek_ibu', 'Pekerjaan Ibu', 'required');
        $this->form_validation->set_rules('no_telp', 'Nomor Telepon', 'required');
        $this->form_validation->set_rules('thn_msk', 'Tahun Masuk', 'required');
        $this->form_validation->set_rules('sekolah_asal', 'Sekolah Asal', 'required');
        $this->form_validation->set_rules('diniyah', 'Diniyah', 'required');
        $this->form_validation->set_rules('pendidikan', 'Pendidikan', 'required');
        $this->form_validation->set_rules('kelas', 'Kelas', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/tambah_siswa', $data);
            $this->load->view('template/footer_admin');
        } else {

            $tgl = date('Y-m-d');
            $nama = $this->input->post('nama');
            $id_prov = $this->input->post('prov');

            $provinsi =  $data['prov'] = $this->db->get_where('provinsi', ['id_prov' => $id_prov])->row_array();
            $data = [
                'point'         => '100',
                'nik'           => $this->input->post('nik'),
                'nis'           => $this->input->post('nis'),
                'nama'          => $nama,
                'email'         => $this->input->post('email'),
                'password'      => password_hash($this->input->post('nis'), PASSWORD_DEFAULT),
                'jk'            => $this->input->post('jk'),
                'ttl'           => $this->input->post('ttl'),
                'prov'          => $provinsi['nama'],
                'kab'           => $this->input->post('kab'),
                'alamat'        => $this->input->post('alamat'),
                'nama_ayah'     => $this->input->post('nama_ayah'),
                'nama_ibu'      => $this->input->post('nama_ibu'),
                'pek_ayah'      => $this->input->post('pek_ayah'),
                'pek_ibu'       => $this->input->post('pek_ibu'),
                'nama_wali'     => $this->input->post('nama_wali'),
                'pek_wali'      => $this->input->post('pek_wali'),
                'peng_ortu'     => $this->input->post('peng_ortu'),
                'no_telp'       => $this->input->post('no_telp'),
                'thn_msk'       => $this->input->post('thn_msk'),
                'sekolah_asal'  => $this->input->post('sekolah_asal'),
                'kelas'         => $this->input->post('old_kelas'),
                'diniyah'       => $this->input->post('diniyah'),
                'id_pend'       => $this->input->post('pendidikan'),
                'id_kelas'      => $this->input->post('kelas'),
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
            redirect('admin/daftar_siswa');
        }
    }



    public function daftar_absen()
    {
        $data['menu'] = 'menu-3';
        $data['title'] = 'Daftar Absen';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $data['kelas_data'] =  $this->db->get('data_kelas')->result_array();

        $data['pendidikan'] = $this->db->get("data_pendidikan")->result_array();

        $this->db->order_by('id', 'desc');
        $data['absen'] =  $this->db->get_where('daftar_absen')->result_array();

        $this->db->order_by('id', 'desc');
        $data['absen1'] =  $this->db->get_where('daftar_absen');

        $this->form_validation->set_rules('kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/daftar_absen', $data);
            $this->load->view('template/footer_admin');
        } else {
            $id_kam = $this->input->post('kelas');
            $tgl = $this->input->post('tanggal');

            $kelas = $this->db->get_where('data_kelas', ['id' => $id_kam])->row_array();
            $cek_daftar = $this->db->get_where('daftar_absen', ['id_kelas' => $id_kam, 'tgl' => $tgl])->row_array();

            if ($cek_daftar['id_kelas'] == $id_kam || $cek_daftar['tgl'] == $tgl) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data absen kelas <strong>' . $kelas['nama'] . '</strong> tanggal <strong>' . mediumdate_indo(date($tgl)) . '</strong> sudah ada.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                redirect('admin/daftar_absen');
            } else {
                $data = [
                    'id_pend' => $this->input->post('pendidikan'),
                    'id_kelas' => $id_kam,
                    'tgl' => $tgl,
                    'status' => 'Belum Selesai'
                ];
                $this->db->insert('daftar_absen', $data);

                $absen  =  $this->db->get_where('daftar_absen', ['id_kelas' => $id_kam, 'tgl' => $tgl])->row_array();

                $cek_kelas = $this->db->get_where('siswa', ['status' => '1', 'id_kelas' => $id_kam])->result_array();

                foreach ($cek_kelas as $a) {
                    $data2 = [
                        'id_siswa' => $a['id'],
                        'tgl' => $tgl,
                        'waktu' => date('h:i:s'),
                        'id_kelas' => $a['id_kelas'],
                        'status' => 'Hadir',
                        'role_absen' => $absen['id']
                    ];
                    $this->db->insert('absen', $data2);
                }

                $this->session->set_flashdata('messageA', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen kelas <strong>' . $kelas['nama'] . '</strong> tanggal <strong>' . $tgl . '</strong> berhasil dibuat :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
                redirect('admin/absen/' . $tgl . '?id=' . $absen['id'] . '');
            }
        }
    }

    public function absen()
    {
        $id_absen  = $this->input->get('id');
        $absen     = $this->db->get_where('daftar_absen', ['id' => $id_absen])->row_array();

        $data['id_absen'] = $this->input->get('id');
        $data['tgl_absen'] = $this->uri->segment(3);
        $data['menu'] = 'menu-8';
        $data['title'] = 'Absen kelas ' . $absen['id_kelas'] . '';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_peng = $data['user']['id'];

        $this->db->order_by('id', 'desc');
        $data['absen'] =  $this->db->get_where('absen', ['role_absen' => $id_absen])->result_array();

        $data['kelas_data'] = $this->db->get_where('data_kelas', ['id_peng' => $id_peng])->result_array();
        $data['daftar_absen'] = $this->db->get_where('daftar_absen', ['id' => $id_absen])->row_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/absen', $data);
        $this->load->view('template/footer_admin');
    }


    public function kelas()
    {

        $data['menu'] = 'menu-3';
        $data['title'] = 'Data kelas';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'desc');
        $data['kelas'] =  $this->db->get('data_kelas')->result_array();
        $data['pendidikan'] =  $this->db->get('data_pendidikan')->result_array();

        $this->form_validation->set_rules('kelas', 'Kelas', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/kelas', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('kelas');

            $cek = $this->db->get_where('data_kelas', ['nama' => $nama])->row_array();
            $cek_rib = $this->db->get_where('data_pendidikan', ['id' => $cek['id_pend']])->row_array();

            if ($cek['kelas']) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data Kelas <strong>' . $nama . '</strong> sudah ada di Pendidikan <strong>' . $cek_rib['nama'] . '</strong>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                redirect('admin/kelas');
            } else {
                $data = [
                    'nama'   => $nama,
                    'id_pend'  => $this->input->post('pendidikan')
                ];
                $this->db->insert('data_kelas', $data);
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kelas <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
                redirect('admin/kelas');
            }
        }
    }


    public function pelanggaran()
    {

        $data['menu'] = 'menu-3';
        $data['title'] = 'Daftar Pelanggaran';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['pelanggaran'] =  $this->db->get('pelanggaran')->result_array();
        $data['data_pelanggaran'] =  $this->db->get('data_pelanggaran')->result_array();
        $data['pendidikan'] =  $this->db->get('data_pendidikan')->result_array();

        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/pelanggaran', $data);
            $this->load->view('template/footer_admin');
        } else {
            $id_san = $this->input->post('siswa');
            $jenis = $this->input->post('jenis');

            $cek = $this->db->get_where('siswa', ['id' => $id_san])->row_array();
            $pelang = $this->db->get_where('data_pelanggaran', ['id' => $jenis])->row_array();

            $data3 = [
                'id_siswa' => $id_san,
                'id_pelang' => $jenis,
                'tgl' => $this->input->post('tanggal'),
                'id_pend' => $cek['id_pend'],
                'id_kelas' => $cek['id_kelas'],
            ];
            $this->db->insert('pelanggaran', $data3);

            //Mengurangi Point
            $point = $this->db->get_where('siswa', ['id' => $id_san])->row_array();
            $this->db->set('point', $point['point'] - $pelang['point']);
            $this->db->where('id', $id_san);
            $this->db->update('siswa');

            //Tambah jumlah data pelanggaran
            $top_pelang = $this->db->get_where('data_pelanggaran', ['id' => $pelang['id']])->row_array();

            if (!empty($top_pelang['jumlah'])) {
                $this->db->set('jumlah', $top_pelang['jumlah'] + 1);
                $this->db->where('id', $top_pelang['id']);
                $this->db->update('data_pelanggaran');
            } else {
                $this->db->set('jumlah', 1);
                $this->db->where('id', $top_pelang['id']);
                $this->db->update('data_pelanggaran');
            }

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Pelanggaran <strong>' . $cek['nama'] . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/pelanggaran');
        }
    }

    public function data_kursi()
    {
        $data['menu'] = 'menu-4';
        $data['title'] = 'Data kursi';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['kursi'] =  $this->db->get('data_kursi')->result_array();
        $data['pendidikan'] = $this->db->get("data_pendidikan")->result_array();
        $data['kelas'] =  $this->db->get('data_kelas')->result_array();


        $this->form_validation->set_rules('kursi', 'kursi', 'required');
        $this->form_validation->set_rules('tipe', 'kursi', 'required');
        $this->form_validation->set_rules('kelas', 'Kelas', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/data/kursi', $data);
            $this->load->view('template/footer_admin');
        } else {

            $kelas = $this->input->post('kelas');
            $nama = $this->input->post('kursi');

            $cek_kelas = $this->db->get_where('data_kelas', ['id' => $kelas])->row_array();

            $cek = $this->db->get_where('data_kursi', ['nama' => $nama, 'id_kelas' => $kelas])->row_array();

            if ($cek['nama']) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Data kursi <strong>' . $nama . '</strong> di kelas <strong>' . $cek_kelas['nama'] . '</strong> sudah ada.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                redirect('admin/data_kursi');
            } else {
                $data = [
                    'nama' => $nama,
                    'tipe' => $this->input->post('tipe'),
                    'id_kelas' => $kelas
                ];

                $this->db->insert('data_kursi', $data);
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data kursi <strong>' . $nama . '</strong> di kelas <strong>' . $cek_kelas['nama'] . '</strong> berhasil ditambahkan :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                redirect('admin/data_kursi');
            }
        }
    }


    public function data_pelanggaran()
    {
        $data['menu'] = 'menu-4';
        $data['title'] = 'Jenis Pelanggaran';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['data_pelanggaran'] =  $this->db->get('data_pelanggaran')->result_array();

        $this->form_validation->set_rules('jenis', 'Jenis Pelanggaran', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/data/pelanggaran', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('jenis');
            $data = [
                'kode' => $this->input->post('kode'),
                'nama' => $nama,
                'point' => $this->input->post('point')
            ];
            $this->db->insert('data_pelanggaran', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Jenis pelanggaran <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_pelanggaran');
        }
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



    public function view_kelas()
    {
        $data['menu'] = 'menu-2';
        $data['title'] = 'Daftar Kelas';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $id_kelas = $this->uri->segment(3);
        $data['kelas']  =  $this->db->get_where('data_kelas', ['id' => $id_kelas])->row_array();
        $data['kursi_a'] =  $this->db->get_where('data_kursi', ['tipe' => 'kursi A', 'id_kelas' => $data['kelas']['id']])->result_array();
        $data['kursi_b'] =  $this->db->get_where('data_kursi', ['tipe' => 'kursi B', 'id_kelas' => $data['kelas']['id']])->result_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/view_kelas', $data);
        $this->load->view('template/footer_admin');
    }


    public function perizinan()
    {

        $data['menu'] = 'menu-3';
        $data['title'] = 'Daftar Perizinan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['perizinan'] =  $this->db->get('perizinan')->result_array();

        $data['siswa'] =  $this->db->get('siswa')->result_array();
        $data['data_izin'] =  $this->db->get('data_perizinan')->result_array();
        $data['pendidikan'] =  $this->db->get('data_pendidikan')->result_array();

        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/perizinan', $data);
            $this->load->view('template/footer_admin');
        } else {
            $id_san = $this->input->post('siswa');
            $cek = $this->db->get_where('siswa', ['id' => $id_san])->row_array();

            $data = [
                'id_siswa' => $id_san,
                'id_izin' => $this->input->post('jenis'),
                'keterangan' => $this->input->post('keterangan'),
                'tgl' => $this->input->post('tanggal'),
                'expired' => $this->input->post('expired'),
                'status' => 'Proses',
                'id_pend' => $cek['id_pend'],
                'id_kelas' => $cek['id_kelas']
            ];

            $this->db->insert('perizinan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data perizinan <strong>' . $cek['nama'] . '</strong> berhasil dibuat :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/perizinan');
        }
    }

    public function data_perizinan()
    {

        $data['menu'] = 'menu-4';
        $data['title'] = 'Data Perizinan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['perizinan'] =  $this->db->get('data_perizinan')->result_array();

        $this->form_validation->set_rules('izin', 'Perizinan', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/data/perizinan', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('izin');

            $data = [
                'nama' => $nama,
            ];
            $this->db->insert('data_perizinan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data perizinan <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_perizinan');
        }
    }


    public function konseling()
    {
        $data['menu'] = 'menu-3';
        $data['title'] = 'Daftar Konseling';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_peng = $data['user']['id'];

        $data['konseling'] =  $this->db->get_where('konseling')->result_array();
        $data['konsel']    =  $this->db->get_where('balas_konseling')->row_array();

        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        $this->form_validation->set_rules('topik', 'Topik', 'required');
        $this->form_validation->set_rules('solusi', 'Solusi', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/konseling', $data);
            $this->load->view('template/footer_admin');
        } else {
            $id_san = $this->input->post('siswa');
            $topik = $this->input->post('topik');
            $cek_kelas = $this->db->get_where('siswa', ['id' => $id_san])->row_array();

            $data = [
                'id_siswa' => $id_san,
                'id_peng' => $id_peng,
                'id_kelas' => $cek_kelas['id_kelas'],
                'topik' => $topik,
                'solusi' => $this->input->post('solusi'),
                'tgl_pengajuan' => date('Y-m-d'),
                'pembuka' => 'Karyawan',
                'status' => 'Respon',
            ];

            $this->db->insert('konseling', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        Data konseling <strong>' . $topik . '</strong> berhasil dibuat :)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      </div>');
            redirect('admin/konseling');
        }
    }


    public function balas_konseling()
    {
        $id_konseling = $this->input->get('id');
        $data['menu'] = 'menu-7';
        $data['title'] = 'Konseling';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['konseling'] =  $this->db->get_where('konseling', ['id' => $id_konseling])->row_array();
        $data['balas_konseling'] =  $this->db->get_where('balas_konseling', ['role_konseling' => $id_konseling]);

        if ($data['konseling']['status']  !== 'Respon') {
            $this->db->set('status', 'Terbaca');
            $this->db->where('id', $id_konseling);
            $this->db->update('konseling');
        }

        $this->form_validation->set_rules('balasan', 'Balasan', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/balas_konseling', $data);
            $this->load->view('template/footer_admin');
        } else {

            $this->db->set('status', 'Respon');
            $this->db->where('id', $id_konseling);
            $this->db->update('konseling');

            $id = $this->input->post('id');
            $id_siswa = $data['konseling']['id_siswa'];

            $tgl = date('Y-m-d');
            $data = [
                'pengirim'  => 'Karyawan',
                'id_peng'   => $this->input->post('nama'),
                'id_siswa' => $id_siswa,
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
            redirect('admin/balas_konseling?id=' . $id_konseling . '');
        }
    }


    public function data_pendidikan()
    {

        $data['menu'] = 'menu-4';
        $data['title'] = 'Data Pendidikan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['pendidikan'] =  $this->db->get('data_pendidikan')->result_array();

        $this->form_validation->set_rules('pendidikan', 'Nama Pendidikan', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/data/pendidikan', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('pendidikan');
            $data = [
                'nama' => $nama
            ];
            $this->db->insert('data_pendidikan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Pendidikan <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_pendidikan');
        }
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


    public function update_siswa()
    {
        $id      = $this->input->get('id');

        $data['menu'] = 'menu-1';
        $data['title'] = 'Update siswa';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('nama', 'asc');
        $data['prov'] = $this->db->get('provinsi')->result_array();
        $data['pendidikan'] = $this->db->get('data_pendidikan')->result_array();
        $data['kelas'] = $this->db->get('data_kelas')->result_array();
        $data['siswa'] =  $this->db->get_where('siswa', ['id' => $id])->row_array();

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/edit_siswa', $data);
            $this->load->view('template/footer_admin');
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
                'kelas' => $this->input->post('old_kelas'),
                'diniyah' => $this->input->post('diniyah'),
                'id_pend' => $this->input->post('pendidikan'),
                'id_kelas' => $this->input->post('kelas'),
                'status' => $this->input->post('status')
            ];

            $this->db->where('id', $id);
            $this->db->update('siswa', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data siswa <strong>' . $nama . '</strong> berhasil diupdate :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
            redirect('admin/update_siswa?id=' . $id . '');
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


    public function tambah_kursi()
    {
        $id_kelas = $this->input->get('id');
        $this->form_validation->set_rules('kursi', 'kursi', 'required');
        $this->form_validation->set_rules('tipe', 'kursi', 'required');

        $nama = $this->input->post('kursi');

        $cek_kelas = $this->db->get_where('data_kelas', ['id' => $id_kelas])->row_array();

        $cek = $this->db->get_where('data_kursi', ['nama' => $nama, 'id_kelas' => $id_kelas])->row_array();

        if ($cek['nama']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Data kursi <strong>' . $nama . '</strong> di kelas <strong>' . $cek_kelas['nama'] . '</strong> sudah ada.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            redirect('admin/view_kelas/' . $id_kelas);
        } else {
            $data = [
                'nama'     => $nama,
                'tipe'     => $this->input->post('tipe'),
                'id_kelas' => $id_kelas
            ];

            $this->db->insert('data_kursi', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data kursi <strong>' . $nama . '</strong> di kelas <strong>' . $cek_kelas['nama'] . '</strong> berhasil ditambahkan :)
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            redirect('admin/view_kelas/' . $id_kelas);
        }
    }



    public function ppdb()
    {
        $data['menu'] = 'ppdb';
        $data['title'] = 'Daftar PPDB';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['siswa'] =  $this->db->get('ppdb')->result_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/ppdb/ppdb', $data);
        $this->load->view('template/footer_admin');
    }


    public function edit_ppdb()
    {
        $id      = $this->input->get('id');
        $data['menu'] = 'ppdb';
        $data['title'] = 'Kelola PPDB';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('nama', 'asc');
        $data['prov'] = $this->db->get('provinsi')->result_array();
        $this->db->order_by('id', 'DESC');
        $data['siswa'] =  $this->db->get_where('ppdb', ['id' => $id])->row_array();

        $this->form_validation->set_rules('email', 'Email', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/ppdb/edit_ppdb', $data);
            $this->load->view('template/footer_admin');
        } else {

            $id_prov = $this->input->post('prov');

            $provinsi =  $data['prov'] = $this->db->get_where('provinsi', ['id_prov' => $id_prov])->row_array();

            $email = $this->input->post('email');
            $data = [
                'nik' => $this->input->post('nik'),
                'nis' => $this->input->post('nis'),
                'nama' => $this->input->post('nama'),
                'email' => $email,
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
            $this->db->update('ppdb', $data);

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Data pendaftaran berhasil di update.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>');
            redirect('admin/edit_ppdb?id=' . $id);
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
                $writer->openToBrowser("data_siswa.xlsx");
            }

            //silahkan sobat sesuaikan dengan data yang ingin sobat tampilkan
            $header = [
                WriterEntityFactory::createCell('No'),
                WriterEntityFactory::createCell('Nik'),
                WriterEntityFactory::createCell('Nis'),
                WriterEntityFactory::createCell('Nama'),
                WriterEntityFactory::createCell('Email'),
                WriterEntityFactory::createCell('No Hp'),
                WriterEntityFactory::createCell('Jenis Kelamin'),
                WriterEntityFactory::createCell('Tanggal lahir'),
                WriterEntityFactory::createCell('Provinsi'),
                WriterEntityFactory::createCell('Kabupaten'),
                WriterEntityFactory::createCell('Alamat'),
                WriterEntityFactory::createCell('Nama Ayah'),
                WriterEntityFactory::createCell('Nama Ibu'),
                WriterEntityFactory::createCell('Pek Ayah'),
                WriterEntityFactory::createCell('Pek Ibu'),
                WriterEntityFactory::createCell('Nama Wali'),
                WriterEntityFactory::createCell('Pek Wali'),
                WriterEntityFactory::createCell('Peng Ortu/Wali'),
                WriterEntityFactory::createCell('Nomor telepon'),
                WriterEntityFactory::createCell('Tahun Masuk'),
                WriterEntityFactory::createCell('Sekolah Asal'),
                WriterEntityFactory::createCell('Kelas'),
                WriterEntityFactory::createCell('Diniyah')
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
                $siswa    = array(
                    WriterEntityFactory::createCell($no++),
                    WriterEntityFactory::createCell($key->nis),
                    WriterEntityFactory::createCell($key->nik),
                    WriterEntityFactory::createCell($key->nama),
                    WriterEntityFactory::createCell($key->email),
                    WriterEntityFactory::createCell($key->no_hp),
                    WriterEntityFactory::createCell($key->jk),
                    WriterEntityFactory::createCell($key->ttl),
                    WriterEntityFactory::createCell($key->prov),
                    WriterEntityFactory::createCell($key->kab),
                    WriterEntityFactory::createCell($key->alamat),
                    WriterEntityFactory::createCell($key->nama_ayah),
                    WriterEntityFactory::createCell($key->nama_ibu),
                    WriterEntityFactory::createCell($key->pek_ayah),
                    WriterEntityFactory::createCell($key->pek_ibu),
                    WriterEntityFactory::createCell($key->nama_wali),
                    WriterEntityFactory::createCell($key->pek_wali),
                    WriterEntityFactory::createCell($key->peng_ortu),
                    WriterEntityFactory::createCell($key->no_telp),
                    WriterEntityFactory::createCell($key->thn_msk),
                    WriterEntityFactory::createCell($key->sekolah_asal),
                    WriterEntityFactory::createCell($key->kelas),
                    WriterEntityFactory::createCell($key->diniyah),
                );

                array_push($data, WriterEntityFactory::createRow($siswa)); //masukkan variabel array siswa ke variabel array data
            }

            $writer->addRows($data); // tambahkan row untuk data siswa

            $writer->close(); //tutup spout writer
        } else {
            echo "Data tidak ditemukan";
        }
    }

    public function data_divisi()
    {
        $data['menu'] = 'menu-4';
        $data['title'] = 'Level Divisi';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['gaji'] =  $this->db->get('data_divisi')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('gaji', 'Gaji', 'required');
        $this->form_validation->set_rules('tunjangan', 'Tunjangan', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/data/divisi', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('nama');
            $this->db->where('nama', $nama);
            $cek_data =  $this->db->get('data_divisi')->row_array();

            if ($cek_data['nama'] == $nama) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data gaji <strong>' . $nama . '</strong> Sudah Ada :(
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                redirect('admin/data_divisi');
            }

            $data = [
                'nama' => $nama,
                'gaji' => $this->input->post('gaji'),
                'tunjangan' => $this->input->post('tunjangan'),
                'role_id' => $this->input->post('level')

            ];
            $this->db->insert('data_divisi', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Potongan <strong>' . $nama . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_divisi');
        }
    }


    public function penggajian()
    {
        $data['menu'] = 'gaji';
        $data['title'] = 'Penggajian';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['penggajian'] =  $this->db->get('penggajian')->result_array();

        $this->form_validation->set_rules('tgl_awal', 'Tanggal Awal', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/penggajian/penggajian', $data);
            $this->load->view('template/footer_admin');
        } else {
            $tgl_awal = $this->input->post('tgl_awal');
            $tgl_akhir = $this->input->post('tgl_akhir');

            $cek_kar = $this->db->get_where('karyawan', ['status' => '1', 'role_id !=' => 1])->result_array();

            foreach ($cek_kar as $a) {
                $data = [
                    'id_peng' => $a['id'],
                    'id_divisi' => $a['id_divisi'],
                    'tgl_awal' => $tgl_awal,
                    'tgl_akhir' => $tgl_akhir,
                    'status' => 0,
                ];
                $this->db->insert('penggajian', $data);
            }

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Penggajian berhasil di tambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/penggajian');
        }
    }


    public function data_cicilan()
    {
        $data['menu'] = 'gaji';
        $data['title'] = 'Data cicilan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['cicilan'] =  $this->db->get('data_cicilan')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('karyawan', 'Nama Karyawan', 'required');
        $this->form_validation->set_rules('nominal', 'Nominal', 'required');
        $this->form_validation->set_rules('tenor', 'Tenor', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/penggajian/data_cicilan', $data);
            $this->load->view('template/footer_admin');
        } else {
            $nama = $this->input->post('nama');
            $id_peng = $this->input->post('karyawan');
            $this->db->where('nama', $nama);
            $this->db->where('id_peng', $id_peng);
            $cek_data =  $this->db->get('data_cicilan')->row_array();

            $this->db->where('id', $id_peng);
            $cek_peng =  $this->db->get('karyawan')->row_array();

            if ($cek_data['nama'] == $nama) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data cicilan <strong>' . $nama . '</strong> dengan nama <strong>' . $cek_peng['nama'] . '</strong> Sudah Ada :(
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                redirect('admin/data_cicilan');
            }

            $data = [
                'nama' => $nama,
                'id_peng' => $id_peng,
                'nominal' =>  $this->input->post('nominal'),
                'tenor' =>  $this->input->post('tenor')
            ];
            $this->db->insert('data_cicilan', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data cicilan <strong>' . $nama . '</strong> dengan nama <strong>' . $cek_peng['nama'] . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_cicilan');
        }
    }

    public function absen_pegawai()
    {
        $data['menu'] = 'absen';
        $data['title'] = 'Absensi Pegawai';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('tgl', 'DESC');
        $data['absen_pegawai'] =  $this->db->get('data_absen_pegawai')->result_array();
        $data['absen'] =  $this->db->get('absen_pegawai')->result_array();

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/absen/absen_pegawai', $data);
            $this->load->view('template/footer_admin');
        } else {
            $tgl = $this->input->post('tanggal');

            $data = [
                'tgl' => $tgl,
                'status' => 0
            ];
            $this->db->insert('data_absen_pegawai', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen pegawai tanggal <strong>' . mediumdate_indo(date($tgl)) . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/absen_pegawai');
        }



        if ($this->input->post('submit', TRUE) == 'upload') {
            $config['upload_path']      = './temp_doc/'; //siapkan path untuk upload file
            $config['allowed_types']    = 'xlsx|xls'; //siapkan format file
            $config['file_name']        = 'doc' . time(); //rename file yang diupload

            $this->load->library('upload', $config);

            //looping pembacaan sheet dalam file        
            if ($this->upload->do_upload('excel')) {
                //fetch data upload
                $file   = $this->upload->data();

                $reader = ReaderEntityFactory::createXLSXReader(); //buat xlsx reader
                $reader->open('./temp_doc/' . $file['file_name']); //open file xlsx yang baru saja diunggah

                foreach ($reader->getSheetIterator() as $sheet1) {
                    $numRow1 = 1;
                    $save1   = array();

                    //looping pembacaan row dalam sheet
                    foreach ($sheet1->getRowIterator() as $row1) {

                        if ($numRow1 > 1) {
                            // ambil cell
                            $cells1 = $row1->getCells();

                            $data_tgl = $this->db->get_where('data_absen_pegawai', ['tgl' => $cells1[1]])->num_rows();

                            if ($data_tgl == 0) {
                                $insert1 = [
                                    'tgl'      => $cells1[1],
                                    'status'      => 1
                                ];

                                $this->db->insert('data_absen_pegawai', $insert1);
                            }
                        }
                        $numRow1++;
                    }
                }

                //looping pembacaan sheet dalam file        
                foreach ($reader->getSheetIterator() as $sheet) {
                    $numRow = 1;

                    //siapkan variabel array kosong untuk menampung variabel array data
                    $save   = array();

                    //looping pembacaan row dalam sheet
                    foreach ($sheet->getRowIterator() as $row) {

                        if ($numRow > 1) {
                            //ambil cell
                            $cells = $row->getCells();

                            $data_tgl = $this->db->get_where('data_absen_pegawai', ['tgl' => $cells[1]])->row_array();

                            $cek_p = $this->db->get_where(
                                'karyawan',
                                ['id_fingerprint' => $cells[0]]
                            )->row_array();
                            $data = array(
                                'id_peng'      => $cek_p['id'],
                                'tgl'          => $cells[1],
                                'jam_masuk'    => $cells[2],
                                'sum_jam'      => $cek_p['jam_mengajar'],
                                'status'       => 1,
                                'role_absen'   => $data_tgl['id']
                            );

                            //tambahkan array $data ke $save
                            array_push($save, $data);
                        }

                        $numRow++;
                    }

                    //simpan data ke database
                    $this->Import_model->simpan_absen($save);

                    //tutup spout reader
                    $reader->close();

                    //hapus file yang sudah diupload
                    unlink('./temp_doc/' . $file['file_name']);

                    //tampilkan pesan success dan redirect ulang ke index controller import
                    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> berhasil mengimport data :)
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      </div>');
                    redirect('admin/absen_pegawai');
                }
            } else {
                //tampilkan pesan error jika file gagal diupload
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> ' . $this->upload->display_errors() . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/absen_pegawai');
            }
        }
    }


    public function data_absensi()
    {
        $data['menu'] = 'absen';
        $data['title'] = 'Data Absensi';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        // $this->db->order_by('tgl', 'DESC');
        $data['absen'] =  $this->db->get('absen_pegawai')->result_array();
        $this->db->where('role_id !=', '1');
        $data['karyawan'] =  $this->db->get('karyawan')->result_array();
        $data['pendidikan'] =  $this->db->get('data_pendidikan')->result_array();
        $data['data_divisi'] =  $this->db->get('data_divisi')->result_array();

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_admin', $data);
            $this->load->view('template/topbar_admin', $data);
            $this->load->view('admin/absen/data_absensi', $data);
            $this->load->view('template/footer_admin');
        } else {
            $tgl = $this->input->post('tanggal');

            $data = [
                'tgl' => $tgl,
                'status' => 0
            ];
            $this->db->insert('data_absen_pegawai', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen pegawai tanggal <strong>' . mediumdate_indo(date($tgl)) . '</strong> berhasil ditambahkan :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_absensi');
        }
    }


    public function view_absen_pegawai()
    {
        $id_absen  = $this->input->get('id');
        $absen     = $this->db->get_where('data_absen_pegawai', ['id' => $id_absen])->row_array();

        $data['id_absen'] = $this->input->get('id');
        $data['tgl_absen'] = $this->uri->segment(3);

        $data['menu'] = 'absen';
        $data['title'] = 'Absen Pegawai';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'desc');
        $this->db->where('tgl', $this->uri->segment(3));
        $data['absen'] =  $this->db->get_where('absen_pegawai', ['role_absen' => $id_absen])->result_array();

        $data['daftar_absen'] = $absen;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_admin', $data);
        $this->load->view('template/topbar_admin', $data);
        $this->load->view('admin/absen/view_absen', $data);
        $this->load->view('template/footer_admin');
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
}
