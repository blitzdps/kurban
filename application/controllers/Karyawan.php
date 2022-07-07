<?php
defined('BASEPATH') or exit('No direct script access allowed');
//load Spout Library
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Karyawan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Import_model');
        $this->load->helper('tgl_indo');

        $users = $this->session->userdata('email');

        $user = $this->db->get_where('karyawan', ['email' => $users])->row_array();
        if ($user['role_id'] == '1') {
            redirect('admin');
        } elseif ($user['role_id'] == '5') {
            redirect('siswa');
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
        $data['menu'] = '';
        $data['title'] = 'Dashboard';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_peng = $data['user']['id'];
        $id_rib = $data['user']['id_pend'];

        $this->db->where('id_pend', $id_rib);
        $data['sum_siswa'] = $this->db->get("siswa")->num_rows();

        $data['tot_siswa'] = $this->db->get("siswa")->num_rows();

        $this->db->where('id_peng', $id_peng);
        $data['kelas'] = $this->db->get("data_kelas")->num_rows();

        $this->db->where('id_peng', $id_peng);
        $data['data_kelas'] = $this->db->get("data_kelas")->result_array();

        $data['about'] = $this->db->get("about")->row_array();

        $this->db->where('id_peng', $id_peng);
        $data['sum_konsel'] = $this->db->get("konseling")->num_rows();

        $this->db->where('id_pend', $id_rib);
        $data['sum_izin'] = $this->db->get("perizinan")->num_rows();

        $this->db->where('id_pend', $id_rib);
        $data['sum_takzir'] = $this->db->get("pelanggaran")->num_rows();

        $data['sum_kontak'] = $this->db->get("kontak")->num_rows();

        $this->db->where('id_peng', $id_peng);
        $data['sum_gallery'] = $this->db->get("gallery")->num_rows();

        $this->db->where('id_peng', $id_peng);
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
        $this->load->view('template/sidebar_karyawan', $data);
        $this->load->view('template/topbar_karyawan', $data);
        $this->load->view('karyawan/index', $data);
        $this->load->view('template/footer_karyawan');
    }

    public function daftar_siswa()
    {
        $data['menu'] = 'menu-1';
        $data['title'] = 'Daftar siswa';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_rib = $data['user']['id_pend'];

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

        $this->db->where('id_pend', $id_rib);
        $this->db->order_by('id', 'DESC');
        $data['siswa'] =  $this->db->get('siswa')->result_array();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_karyawan', $data);
        $this->load->view('template/topbar_karyawan', $data);
        $this->load->view('karyawan/daftar_siswa', $data);
        $this->load->view('template/footer_karyawan');


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

                //looping pembacaat sheet dalam file        
                foreach ($reader->getSheetIterator() as $sheet) {
                    $numRow = 1;

                    //siapkan variabel array kosong untuk menampung variabel array data
                    $save   = array();

                    //looping pembacaan row dalam sheet

                    //looping pembacaan row dalam sheet
                    foreach ($sheet->getRowIterator() as $row) {

                        if ($numRow > 1) {
                            //ambil cell
                            $cells = $row->getCells();

                            $data = array(
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
                    redirect('karyawan/daftar_siswa');
                }
            } else {
                //tampilkan pesan error jika file gagal diupload
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> ' . $this->upload->display_errors() . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>9
              </div>');
                redirect('karyawan/daftar_siswa');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/tambah_siswa', $data);
            $this->load->view('template/footer_karyawan');
        } else {
            $tgl = date('Y-m-d');
            $nama = $this->input->post('nama');
            $id_prov = $this->input->post('prov');

            $provinsi =  $data['prov'] = $this->db->get_where('provinsi', ['id_prov' => $id_prov])->row_array();
            $data = [
                'nik' => $this->input->post('nik'),
                'nis' => $this->input->post('nis'),
                'nama' => $nama,
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('nis'), PASSWORD_DEFAULT),
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
                'date_created' => $tgl,
                'status' => 1,
                'role_id' => 5
            ];

            $this->db->insert('siswa', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Data siswa <strong>' . $nama . '</strong> berhasil ditambahkan!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
            redirect('karyawan/daftar_siswa');
        }
    }



    public function kelas()
    {

        $data['menu'] = 'menu-3';
        $data['title'] = 'Data kelas';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_pend = $data['user']['id_pend'];

        $data['kelas'] =  $this->db->get_where('data_kelas', ['id_pend' => $id_pend])->result_array();
        $data['pendidikan'] =  $this->db->get('data_pendidikan')->result_array();

        $this->form_validation->set_rules('kelas', 'Kelas', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/kelas', $data);
            $this->load->view('template/footer_karyawan');
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
                redirect('karyawan/kelas');
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
                redirect('karyawan/kelas');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/pelanggaran', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/pelanggaran');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/setting', $data);
            $this->load->view('template/footer_karyawan');
        } else {
            $id = $this->input->post('id');
            $nama = $this->input->post('nama');

            $edit = [
                'nama' => $nama,
                'alamat' => $this->input->post('alamat'),
                'telp' => $this->input->post('no_hp')
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
            redirect('karyawan/setting');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/setting', $data);
            $this->load->view('template/footer_karyawan');
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
                redirect('karyawan/setting');
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
                    redirect('karyawan/setting');
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
                    redirect('karyawan/setting');
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
        $this->load->view('template/sidebar_karyawan', $data);
        $this->load->view('template/topbar_karyawan', $data);
        $this->load->view('karyawan/view_kelas', $data);
        $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/view_kelas/' . $id_kelas);
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
            redirect('karyawan/view_kelas/' . $id_kelas);
        }
    }


    public function perizinan()
    {

        $data['menu'] = 'menu-3';
        $data['title'] = 'Daftar Perizinan';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_pend = $data['user']['id_pend'];

        $this->db->order_by('id', 'DESC');
        $data['perizinan'] =  $this->db->get_where('perizinan', ['id_pend' => $id_pend])->result_array();

        $data['siswa'] =  $this->db->get_where('siswa', ['id_pend' => $id_pend])->result_array();
        $data['data_izin'] =  $this->db->get('data_perizinan')->result_array();
        $data['pendidikan'] =  $this->db->get('data_pendidikan')->result_array();

        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/perizinan', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/perizinan');
        }
    }


    public function konseling()
    {
        $data['menu'] = 'menu-3';
        $data['title'] = 'Daftar Konseling';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_peng = $data['user']['id'];

        $data['konseling'] =  $this->db->get_where('konseling', ['id_peng' => $id_peng])->result_array();
        $data['konsel']    =  $this->db->get_where('balas_konseling', ['id_peng' => $id_peng])->row_array();

        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        $this->form_validation->set_rules('topik', 'Topik', 'required');
        $this->form_validation->set_rules('solusi', 'Solusi', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/konseling', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/konseling');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/balas_konseling', $data);
            $this->load->view('template/footer_karyawan');
        } else {

            $this->db->set('status', 'Respon');
            $this->db->where('id', $id_konseling);
            $this->db->update('konseling');

            $id = $this->input->post('id');
            $tgl = date('Y-m-d');
            $data = [
                'pengirim'  => 'Karyawan',
                'id_peng'  => $this->input->post('nama'),
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
            redirect('karyawan/balas_konseling?id=' . $id_konseling . '');
        }
    }


    public function daftar_absen()
    {
        $data['menu'] = 'menu-3';
        $data['title'] = 'Daftar Absen';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_pend = $data['user']['id_pend'];

        $data['kelas_data'] =  $this->db->get_where('data_kelas', ['id_pend' => $id_pend])->result_array();

        $data['pendidikan'] = $this->db->get("data_pendidikan")->result_array();

        $this->db->order_by('id', 'desc');
        $data['absen'] =  $this->db->get_where('daftar_absen', ['id_pend' => $id_pend])->result_array();

        $this->db->order_by('id', 'desc');
        $data['absen1'] =  $this->db->get_where('daftar_absen', ['id_pend' => $id_pend]);

        $this->form_validation->set_rules('kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/daftar_absen', $data);
            $this->load->view('template/footer_karyawan');
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
                redirect('karyawan/daftar_absen');
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
                redirect('karyawan/absen/' . $tgl . '?id=' . $absen['id'] . '');
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
        $this->load->view('template/sidebar_karyawan', $data);
        $this->load->view('template/topbar_karyawan', $data);
        $this->load->view('karyawan/absen', $data);
        $this->load->view('template/footer_karyawan');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/acara/tambah_acara', $data);
            $this->load->view('template/footer_karyawan');
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
                redirect('karyawan/tambah_acara');
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
                    redirect('karyawan/tambah_acara');
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
            redirect('karyawan/acara');
        }
    }



    public function acara()
    {

        $data['menu'] = 'acara';
        $data['title'] = 'Data Acara';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_peng = $data['user']['id'];
        $this->db->where('id_peng', $id_peng);
        $this->db->order_by('id', 'DESC');
        $data['acara'] =  $this->db->get('acara')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/acara/acara', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/acara');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/acara/kategori', $data);
            $this->load->view('template/footer_karyawan');
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
                    redirect('karyawan/tambah_acara');
                } elseif ($segmen == 'edit') {
                    redirect('karyawan/edit_acara?id=' . $this->uri->segment(4));
                } else {
                    redirect('karyawan/kategori_acara');
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
                redirect('karyawan/tambah_acara');
            } elseif ($segmen == 'edit') {
                redirect('karyawan/edit_acara?id=' . $this->uri->segment(4));
            } else {
                redirect('karyawan/kategori_acara');
            }
        }
    }


    public function gallery()
    {

        $data['menu'] = 'gallery';
        $data['title'] = 'Data Gallery';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $id_peng = $data['user']['id'];
        $this->db->where('id_peng', $id_peng);
        $this->db->order_by('id', 'DESC');
        $data['gallery'] =  $this->db->get('gallery')->result_array();

        $this->form_validation->set_rules('nama', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/gallery/gallery', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/gallery');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/gallery/kategori', $data);
            $this->load->view('template/footer_karyawan');
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
                    redirect('karyawan/tambah_gallery');
                } elseif ($segmen == 'edit') {
                    redirect('karyawan/edit_gallery?id=' . $this->uri->segment(4));
                } else {
                    redirect('karyawan/kategori_gallery');
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
                redirect('karyawan/tambah_gallery');
            } elseif ($segmen == 'edit') {
                redirect('karyawan/edit_gallery?id=' . $this->uri->segment(4));
            } else {
                redirect('karyawan/kategori_gallery');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/gallery/tambah_gallery', $data);
            $this->load->view('template/footer_karyawan');
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
                redirect('karyawan/tambah_gallery');
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
                    redirect('karyawan/tambah_gallery');
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
                    redirect('karyawan/gallery');
                }
            }
        }
    }


    // ---------------- SEND EMAIL SENDER ----------------- //

    private function sendEmail($id, $email, $subjek, $pesan, $type)
    {
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();
        $data['kontak'] =  $this->db->get_where('kontak', ['id' => $id])->row_array();

        $web = $data['web'];

        $esen =  $this->db->get('email_sender')->row_array();

        $config = [
            'protocol'  => $esen['protocol'],
            'smtp_host' => $esen['host'],
            'smtp_user' => $esen['email'],
            'smtp_pass' => $esen['password'],
            'smtp_port' => $esen['port'],
            'mailtype'  => 'html',
            'charset'   => $esen['charset'],
            'newline'   => "\r\n"
        ];

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->set_header('Content-Type', 'text/html');

        $this->email->from($esen['email'], $web['nama']);
        $this->email->to($email);

        $data['link_web'] = base_url();
        $data['email'] = $email;
        $data['pesan']   = $pesan;

        $body_test = $this->load->view('email/test', $data, TRUE);
        $body_balas = $this->load->view('email/balas', $data, TRUE);

        if ($type == 'test') {
            $this->email->subject($subjek . ' - ' . $web['nama']);
            $this->email->message($body_test);
        } else if ($type == 'balas') {
            $this->email->subject($subjek . ' - ' . $web['nama']);
            $this->email->message($body_balas);
        }

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
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


            $this->db->set('status', 2);
            $this->db->update('kontak');

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar_karyawan', $data);
        $this->load->view('template/topbar_karyawan', $data);
        $this->load->view('karyawan/kontak', $data);
        $this->load->view('template/footer_karyawan');
    }


    public function balas_kontak()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            redirect('karyawan/kontak');
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
            redirect('karyawan/kontak');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/edit_siswa', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/update_siswa?id=' . $id . '');
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/gallery/edit_gallery', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/edit_gallery?id=' . $id);
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
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/acara/edit_acara', $data);
            $this->load->view('template/footer_karyawan');
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
                    redirect('karyawan/edit_acara?id=' . $id);
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
            redirect('karyawan/edit_acara?id=' . $id);
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
        $this->load->view('template/sidebar_karyawan', $data);
        $this->load->view('template/topbar_karyawan', $data);
        $this->load->view('karyawan/ppdb/ppdb', $data);
        $this->load->view('template/footer_karyawan');
    }


    public function edit_ppdb()
    {
        $id      = $this->input->get('id');
        $data['menu'] = 'ppdb';
        $data['title'] = 'Kelola PPDB';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->order_by('id', 'DESC');
        $data['siswa'] =  $this->db->get_where('ppdb', ['id' => $id])->row_array();

        $this->form_validation->set_rules('email', 'Email', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/ppdb/edit_ppdb', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/edit_ppdb');
        }
    }


    public function penggajian()
    {
        $data['menu'] = 'gaji';
        $data['title'] = 'Penggajian';
        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $this->db->where('id_peng', $data['user']['id']);
        $this->db->order_by('id', 'DESC');
        $data['penggajian'] =  $this->db->get('penggajian')->result_array();

        $this->form_validation->set_rules('tgl_awal', 'Tanggal Awal', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar_karyawan', $data);
            $this->load->view('template/topbar_karyawan', $data);
            $this->load->view('karyawan/penggajian', $data);
            $this->load->view('template/footer_karyawan');
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
            redirect('karyawan/penggajian');
        }
    }
}
