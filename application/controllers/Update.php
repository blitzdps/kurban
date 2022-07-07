<?php
ob_start();
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Update extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        require APPPATH . 'libraries/phpmailer/src/Exception.php';
        require APPPATH . 'libraries/phpmailer/src/PHPMailer.php';
        require APPPATH . 'libraries/phpmailer/src/SMTP.php';

        $this->load->helper('tgl_indo');
    }

    public function update_kelas()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');
        $nama  = $this->input->post('kelas');
        $peng = $this->input->post('karyawan');

        if (!empty($peng)) {
            $karyawan = $this->input->post('karyawan');
        } else {
            $karyawan = $this->input->post('karyawan1');
        }
        $data = [
            'nama' => $nama,
            'id_pend' => $this->input->post('pendidikan'),
            'id_peng' => $karyawan
        ];
        $this->db->where('id', $id);
        $this->db->update('data_kelas', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kelas <strong>' . $nama . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/kelas');
        } else {
            redirect('admin/kelas');
        }
    }


    public function update_pelanggaran()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');
        $jen  = $this->input->post('jenis');
        $id_pelang_lama = $this->input->post('id_pelang_lama');

        $pelang = $this->db->get_where('data_pelanggaran', ['id' => $jen])->row_array();

        $takzir = $this->db->get_where('pelanggaran', ['id' => $id])->row_array();
        $nama = $this->db->get_where('siswa', ['id' => $takzir['id_siswa']])->row_array();

        $this->db->set('id_pelang', $jen);
        $this->db->set('tgl', $this->input->post('tanggal'));
        $this->db->where('id', $id);
        $this->db->update('pelanggaran');

        $pelang_lama = $this->db->get_where('data_pelanggaran', ['id' => $id_pelang_lama['id']])->row_array();
        //Mengurangi Point
        $point = $this->db->get_where('siswa', ['id' => $takzir['id_siswa']])->row_array();
        $this->db->set('point', $point['point'] + $pelang_lama['point'] - $pelang['point']);
        $this->db->where('id', $takzir['id_siswa']);
        $this->db->update('siswa');

        //Update Jumlah data pelanggaran
        $this->db->set('jumlah', $pelang_lama['jumlah'] - 1);
        $this->db->where('id', $pelang_lama['id']);
        $this->db->update('data_pelanggaran');

        //Update Jumlah data pelanggaran
        $top_pelang = $this->db->get_where('data_pelanggaran', ['id' => $pelang['id']])->row_array();
        $this->db->set('jumlah', $top_pelang['jumlah'] + 1);
        $this->db->where('id', $pelang['id']);
        $this->db->update('data_pelanggaran');

        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Pelanggaran <strong>' . $nama['nama'] . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/pelanggaran');
        } else {
            redirect('admin/pelanggaran');
        }
    }


    public function tutup_absen_kelas()
    {
        $segmen = $this->uri->segment(3);
        $seg_daftar = $this->uri->segment(4);
        $id      = $this->input->get('id');

        $absen  = $this->db->get_where('daftar_absen', ['id' => $id])->row_array();
        $cek  = $this->db->get_where('absen', ['role_absen' => $id, 'status' => 'Belum Absen'])->row_array();
        $kelas  = $this->db->get_where('data_kelas', ['id' => $absen['id_kelas']])->row_array();

        if ($cek['status']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Oops!</strong> Masih ada yang <strong>Belum Absen</strong>.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            if ($seg_daftar == 'daftar_absen') {
                if ($segmen == 'karyawan') {
                    redirect('karyawan/daftar_absen');
                } elseif ($segmen == 'admin') {
                    redirect('admin/daftar_absen');
                }
            } else {
                if ($segmen == 'karyawan') {
                    redirect('karyawan/absen/' . $absen['tgl'] . '?id=' . $id . '');
                } elseif ($segmen == 'admin') {
                    redirect('admin/absen/' . $absen['tgl'] . '?id=' . $id . '');
                }
            }
        } else {
            $data = [
                'status' => 'Selesai'
            ];
            $this->db->where('id', $id);
            $this->db->update('daftar_absen', $data);

            if ($seg_daftar == 'daftar_absen') {
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Data absen tanggal <strong>' . mediumdate_indo(date($absen['tgl'])) . '</strong> dari kelas <b>' . $kelas['nama'] . '</b> berhasil ditutup.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                if ($segmen == 'karyawan') {
                    redirect('karyawan/daftar_absen');
                } elseif ($segmen == 'admin') {
                    redirect('admin/daftar_absen');
                }
            } else {
                $this->session->set_flashdata('messageA', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Data absen tanggal <strong>' . mediumdate_indo(date($absen['tgl'])) . '</strong> dari kelas <b>' . $kelas['nama'] . '</b> berhasil ditutup.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                if ($segmen == 'karyawan') {
                    redirect('karyawan/absen/' . $absen['tgl'] . '?id=' . $id . '');
                } elseif ($segmen == 'admin') {
                    redirect('admin/absen/' . $absen['tgl'] . '?id=' . $id . '');
                }
            }
        }
    }

    public function update_perizinan()
    {
        $url_seg      = $this->uri->segment(3);
        $id           = $this->input->post('id');

        $id_san = $this->input->post('siswa');
        $jenis = $this->input->post('jenis');
        $cek = $this->db->get_where('siswa', ['id' => $id_san])->row_array();
        $cek_izin = $this->db->get_where('data_perizinan', ['id' => $jenis])->row_array();

        $data = [
            'id_izin' => $jenis,
            'keterangan' => $this->input->post('keterangan'),
            'tgl' => $this->input->post('tanggal'),
            'expired' => $this->input->post('expired')
        ];
        $this->db->where('id', $id);
        $this->db->update('perizinan', $data);
        if ($url_seg == 'siswa') {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Perizinan <strong>' . $cek_izin['nama'] . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('siswa/perizinan');
        } elseif ($url_seg == 'karyawan') {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Perizinan <strong>' . $cek_izin['nama'] . '</strong> dari siswa <strong>' . $cek['nama'] . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('karyawan/perizinan');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Perizinan <strong>' . $cek_izin['nama'] . '</strong> dari siswa <strong>' . $cek['nama'] . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/perizinan');
        }
    }

    public function update_pending_izin()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');
        $nama  = $this->input->post('nama');
        $izin  = $this->input->post('izin');
        $data = [
            'status' => 'Proses'
        ];
        $this->db->where('id', $id);
        $this->db->update('perizinan', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Data perizinan <strong>' . $nama . '</strong> dengan izin <strong>' . $izin . '</strong> berhasil diproses.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/perizinan');
        } else {
            redirect('admin/perizinan');
        }
    }

    public function update_proses_izin()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');
        $nama  = $this->input->post('nama');
        $izin  = $this->input->post('izin');
        $data = [
            'status' => 'Success'
        ];
        $this->db->where('id', $id);
        $this->db->update('perizinan', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Data perizinan <strong>' . $nama . '</strong> dengan izin <strong>' . $izin . '</strong> berhasil diupdate.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/perizinan');
        } else {
            redirect('admin/perizinan');
        }
    }

    public function update_data_perizinan()
    {
        $id    = $this->input->post('id');
        $nama  = $this->input->post('izin');
        $data = [
            'nama' => $nama
        ];
        $this->db->where('id', $id);
        $this->db->update('data_perizinan', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data perizinan <strong>' . $nama . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/data_perizinan');
    }

    public function update_data_pendidikan()
    {
        $id    = $this->input->post('id');
        $nama  = $this->input->post('pendidikan');

        $data = [
            'nama' => $nama,
        ];
        $this->db->where('id', $id);
        $this->db->update('data_pendidikan', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Pendidikan <strong>' . $nama . '</strong> berhasil diubah
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/data_pendidikan');
    }

    public function update_status_absen()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');
        $status  = $this->input->post('absen');

        $cek = $this->db->get_where('absen', ['id' => $id])->row_array();

        if ($status == $cek['status']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        siswa <strong>' . $cek['siswa'] . '</strong> sudah hadir.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      </div>');
            if ($segmen == 'karyawan') {
                redirect('karyawan/absen/' . $cek['tgl'] . '?id=' . $cek['role_absen'] . '');
            } else {
                redirect('admin/absen/' . $cek['tgl'] . '?id=' . $cek['role_absen'] . '');
            }
        }

        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $this->db->update('absen');

        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data siswa <strong>' . $cek['siswa'] . '</strong> berhasil diupdate dengan status <strong>' . $status . '</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/absen/' . $cek['tgl'] . '?id=' . $cek['role_absen'] . '');
        } else {
            redirect('admin/absen/' . $cek['tgl'] . '?id=' . $cek['role_absen'] . '');
        }
    }

    public function tutup_konseling()
    {
        $segmen = $this->uri->segment(3);
        $tgl = date('Y-m-d');
        $id    = $this->input->post('id');
        $nama  = $this->input->post('siswa');
        $topik  = $this->input->post('topik');
        $penutup = $this->input->post('penutup');
        $data = [
            'tgl_tutup' => $tgl,
            'penutup' => $penutup,
            'status' => 'Selesai'
        ];
        $this->db->where('id', $id);
        $this->db->update('konseling', $data);

        if ($segmen == 'admin') {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Data Konseling <strong>' . $topik . '</strong> dari <strong>' . $nama . '</strong> berhasil ditutup.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
            redirect('admin/konseling');
        } else {
            if ($penutup == 'Karyawan') {
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Data Konseling <strong>' . $topik . '</strong> dari <strong>' . $nama . '</strong> berhasil ditutup.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>');
                redirect('karyawan/konseling');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Konseling <strong>' . $topik . '</strong> berhasil ditutup.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
                redirect('siswa/konseling');
            }
        }
    }


    public function update_data_pelanggaran()
    {
        $id    = $this->input->post('id');
        $nama  = $this->input->post('jenis');
        $data = [
            'kode' => $this->input->post('kode'),
            'nama' => $nama,
            'point' => $this->input->post('point')
        ];
        $this->db->where('id', $id);
        $this->db->update('data_pelanggaran', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Jenis pelanggaran <strong>' . $nama . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/data_pelanggaran');
    }

    public function update_data_kursi()
    {
        $id     = $this->input->post('id');
        $nama   = $this->input->post('kursi');
        $kelas = $this->input->post('kelas');

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
                'id_kelas'  => $kelas
            ];
            $this->db->where('id', $id);
            $this->db->update('data_kursi', $data);

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data kursi <strong>' . $nama . '</strong> di kelas <strong>' . $cek_kelas['nama'] . '</strong> berhasil diupdate.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      </div>');
            redirect('admin/data_kursi');
        }
    }

    public function update_isi_kursi()
    {
        $segmen = $this->uri->segment(3);
        $id     = $this->input->post('id');
        $id_san   = $this->input->post('siswa');

        $cek = $this->db->get_where('data_kursi', ['id_siswa' => $id_san])->row_array();
        $nama = $this->db->get_where('siswa', ['id' => $id_san])->row_array();
        $cek1 = $this->db->get_where('data_kursi', ['id' => $id])->row_array();
        $kelas1 = $this->db->get_where('data_kelas', ['id' => $cek1['id_kelas']])->row_array();
        $id_kelas = $kelas1['id'];

        if ($cek['siswa']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            siswa <strong>' . $nama['nama'] . '</strong> sudah punya kursi.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
            redirect('admin/data_kursi');
        } else {

            $this->db->set('id_siswa', $id_san);
            $this->db->where('id', $id);
            $this->db->update('data_kursi');

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Isi kursi dengan nama <strong>' . $nama['nama'] . '</strong> berhasil diubah.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>'
            );
            if ($segmen == 'karyawan') {
                redirect('karyawan/view_kelas/' . $id_kelas . '');
            } elseif ($segmen == 'admin') {
                redirect('admin/data_kursi');
            } else {
                redirect('admin/view_kelas/' . $id_kelas . '');
            }
        }
    }

    public function update_isi_kursi_kosong()
    {
        $segmen = $this->uri->segment(3);
        $id     = $this->input->post('id');

        $cek1 = $this->db->get_where('data_kursi', ['id' => $id])->row_array();
        $kelas1 = $this->db->get_where('data_kelas', ['id' => $cek1['id_kelas']])->row_array();
        $id_kelas = $kelas1['id'];

        $this->db->set('id_siswa', 0);
        $this->db->where('id', $id);
        $this->db->update('data_kursi');

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Isi kursi berhasil di kosongkan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>'
        );
        if ($segmen == 'karyawan') {
            redirect('karyawan/view_kelas/' . $id_kelas . '');
        } elseif ($segmen == 'admin') {
            redirect('admin/data_kursi');
        } else {
            redirect('admin/view_kelas/' . $id_kelas . '');
        }
    }

    public function update_konseling()
    {
        $segmen = $this->uri->segment(3);
        $id     = $this->input->post('id');

        $data = [
            'topik' => $this->input->post('topik'),
            'solusi' => $this->input->post('solusi')
        ];
        $this->db->where('id', $id);
        $this->db->update('konseling', $data);

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data konseling <strong>' . $this->input->post('topik') . '</strong> berhasil diubah.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>'
        );
        if ($segmen == 'karyawan') {
            redirect('karyawan/konseling');
        } elseif ($segmen == 'siswa') {
            redirect('siswa/konseling');
        } else {
            redirect('admin/konseling');
        }
    }

    public function Logo()
    {
        $gambar        = $_FILES['gambar'];

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './assets/img/';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config); // Load konfigurasi uploadnya
            if (!$this->upload->do_upload('gambar')) {
                $this->session->set_flashdata('messagelogo', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Logo Header gagal diubah :(
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/website');
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }
        $g =  $this->db->get('website')->row_array();
        //hapus gambar di path
        unlink("./assets/img/" . $g['logo']);
        $data = [
            'logo' => $gambar
        ];

        $this->db->update('website', $data);
        $this->session->set_flashdata('messagelogo', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Logo Header baru berhasil diubah :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/website');
    }

    public function LogoFav()
    {
        $gambar = $_FILES['fav'];

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './assets/img/';
            $config['allowed_types'] = 'jpg|png|ico|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config); // Load konfigurasi uploadnya
            if (!$this->upload->do_upload('fav')) {
                $this->session->set_flashdata('messagelogo', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Logo Favicon gagal diubah :(
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/website');
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }
        $g =  $this->db->get('website')->row_array();
        //hapus gambar di path
        unlink("./assets/img/" . $g['fav']);
        $data = [
            'fav' => $gambar
        ];

        $this->db->update('website', $data);
        $this->session->set_flashdata('messagelogo', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Logo Favicon baru berhasil diubah :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/website');
    }

    public function imgUser()
    {
        $gambar = $_FILES['imgUser'];

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './assets/img/';
            $config['allowed_types'] = 'jpg|png|ico|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config); // Load konfigurasi uploadnya
            if (!$this->upload->do_upload('imgUser')) {
                $this->session->set_flashdata('messagelogo', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar login user gagal diubah :(
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/website');
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }
        $g =  $this->db->get('website')->row_array();
        //hapus gambar di path
        unlink("./assets/img/" . $g['img_login']);
        $data = [
            'img_login' => $gambar
        ];

        $this->db->update('website', $data);
        $this->session->set_flashdata('messagelogo', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Gambar login user baru berhasil diubah :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/website');
    }

    public function imgAdmin()
    {
        $gambar = $_FILES['imgAdmin'];

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './assets/img/';
            $config['allowed_types'] = 'jpg|png|ico|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config); // Load konfigurasi uploadnya
            if (!$this->upload->do_upload('imgAdmin')) {
                $this->session->set_flashdata('messagelogo', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar login admin gagal diubah :(
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/website');
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }
        $g =  $this->db->get('website')->row_array();
        //hapus gambar di path
        unlink("./assets/img/" . $g['img_login_admin']);
        $data = [
            'img_login_admin' => $gambar
        ];

        $this->db->update('website', $data);
        $this->session->set_flashdata('messagelogo', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Gambar login admin baru berhasil diubah :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/website');
    }



    public function update_karyawan()
    {
        $id    = $this->input->post('id');
        $this->form_validation->set_rules('nama', 'Nama Karyawan', 'required');

        $nama = $this->input->post('nama');
        $data = [
            'id_fingerprint' => $this->input->post('id_fp'),
            'nama' => $nama,
            'email' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'alamat' => $this->input->post('alamat'),
            'telp' => $this->input->post('telp'),
            'id_divisi' => $this->input->post('divisi'),
            'dept' => $this->input->post('dept'),
            'intensif' => $this->input->post('intensif'),
            'jam_mengajar' => $this->input->post('jam_mengajar'),
            'nominal_jam' => $this->input->post('nominal_jam'),
            'bpjs' => $this->input->post('bpjs'),
            'koperasi' => $this->input->post('koperasi'),
            'simpanan' => $this->input->post('simpanan'),
            'tabungan' => $this->input->post('tabungan'),
            'id_pend' => $this->input->post('pendidikan'),
            'id_kelas' => $this->input->post('kelas'),
            'status' => $this->input->post('status'),
            'role_id' => $this->input->post('level')

        ];
        $this->db->where('id', $id);
        $this->db->update('karyawan', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           Data karyawan <strong>' . $nama . '</strong> berhasil diupdate!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
        redirect('admin/karyawan');
    }



    public function update_kategori_acara()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');
        $nama  = $this->input->post('nama');
        $uniq  = strtolower($nama);
        $data = [
            'nama' => $nama,
            'uNiq' => preg_replace("/[^A-Za-z0-9 ]/", "", $uniq)
        ];
        $this->db->where('id', $id);
        $this->db->update('kategori_acara', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kategori Acara <strong>' . $nama . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/kategori_acara');
        } else {
            redirect('admin/kategori_acara');
        }
    }



    public function update_kategori_gallery()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');
        $nama  = $this->input->post('nama');
        $uniq  = strtolower($nama);
        $data = [
            'nama' => $nama,
            'uNiq' => preg_replace("/[^A-Za-z0-9 ]/", "", $uniq)
        ];
        $this->db->where('id', $id);
        $this->db->update('kategori_gallery', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kategori Acara <strong>' . $nama . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/kategori_gallery');
        } else {
            redirect('admin/kategori_gallery');
        }
    }


    public function gambar_utama()
    {
        $gambar        = $_FILES['gambar'];
        $id = $this->input->post('id');

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './assets/img/';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config); // Load konfigurasi uploadnya
            if (!$this->upload->do_upload('gambar')) {
                $this->session->set_flashdata('messageimg', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar About Gagal di Ubah :(
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/utama');
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }
        $g =  $this->db->get('home')->row_array();
        //hapus gambar di path
        unlink("./assets/img/" . $g['img']);
        $data = [
            'img' => $gambar
        ];

        $this->db->update('home', $data);
        $this->session->set_flashdata('messageimg', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Gambar baru berhasil diubah :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/utama');
    }


    public function img_about()
    {
        $gambar        = $_FILES['gambar'];
        $id = $this->input->post('id');

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './assets/img/';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size']  = '8048';
            $config['remove_space'] = TRUE;

            $this->load->library('upload', $config); // Load konfigurasi uploadnya
            if (!$this->upload->do_upload('gambar')) {
                $this->session->set_flashdata('messageimg', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gambar About Gagal di Ubah :(
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  </div>');
                redirect('admin/about');
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }
        $g =  $this->db->get('about')->row_array();
        //hapus gambar di path
        unlink("./assets/img/" . $g['img']);

        $this->db->set('img', $gambar);
        $this->db->where('id', $id);
        $this->db->update('about');
        $this->session->set_flashdata('messageimg', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Gambar baru berhasil diubah :)
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/about');
    }


    public function link_sosmed()
    {
        $id    = $this->input->post('id');

        $data = [
            'link_fb' => $this->input->post('link_fb'),
            'link_ig' => $this->input->post('link_ig'),
            'link_tw' => $this->input->post('link_tw')
        ];
        $this->db->where('id', $id);
        $this->db->update('website', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
           <strong>Success!</strong>  Data link sosmed website berhasil diupdate!
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
          </div>');
        redirect('admin/website');
    }



    private function ppdbEmail($id)
    {
        // PHPMailer object
        $response = false;
        $mail = new PHPMailer(true);

        $data['user'] = $this->db->get_where('karyawan', ['email' => $this->session->userdata('email')])->row_array();
        $data['web'] =  $this->db->get('website')->row_array();

        $data['ppdb'] =  $this->db->get_where('ppdb', ['id' => $id])->row_array();
        $email = $data['ppdb']['email'];

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

        $this->email->from($esen['email'], $web['nama']);
        $this->email->to($email);

        $data['link_web'] = base_url();
        $data['email'] = $email;

        $body = $this->load->view('email/ppdb', $data, TRUE);

        $mail->Subject = 'Selamat Anda sudah di terima';
        $mail->Body = $body;

        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return true;
        }
    }

    public function konfirmasi_ppdb()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->get('id');

        $this->db->set('status', 1);
        $this->db->where('id', $id);
        $this->db->update('ppdb');


        $this->ppdbEmail($id);


        $this->session->set_flashdata('messageInfo', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data ppdb berhasil di konfirmasi.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/edit_ppdb?id=' . $id);
        } else if ($segmen == 'admin') {
            redirect('admin/edit_ppdb?id=' . $id);
        }
    }


    public function batal_konfirmasi_ppdb()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->get('id');

        $this->db->set('status', 0);
        $this->db->where('id', $id);
        $this->db->update('ppdb');
        $this->session->set_flashdata('messageInfo', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data ppdb berhasil di batalkan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/edit_ppdb?id=' . $id);
        } else if ($segmen == 'admin') {
            redirect('admin/edit_ppdb?id=' . $id);
        }
    }

    public function tolak_ppdb()
    {
        $segmen = $this->uri->segment(3);
        $id    = $this->input->post('id');

        $data = [
            'alasan' => $this->input->post('alasan'),
            'status' => 2
        ];

        $this->db->where('id', $id);
        $this->db->update('ppdb', $data);
        $this->session->set_flashdata('messageInfo', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data ppdb berhasil di batalkan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'karyawan') {
            redirect('karyawan/edit_ppdb?id=' . $id);
        } else if ($segmen == 'admin') {
            redirect('admin/edit_ppdb?id=' . $id);
        }
    }

    public function update_data_divisi()
    {
        $id    = $this->input->post('id');
        $nama  = $this->input->post('nama');
        $data = [
            'nama' => $nama,
            'gaji' => $this->input->post('gaji'),
            'tunjangan' => $this->input->post('tunjangan'),
            'role_id' => $this->input->post('level')
        ];
        $this->db->where('id', $id);
        $this->db->update('data_divisi', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data divisi <strong>' . $nama . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/data_Divisi');
    }


    public function update_data_cicilan()
    {
        $id    = $this->input->post('id');
        $nama = $this->input->post('nama');
        $peng = $this->input->post('karyawan');
        if (!empty($peng)) {
            $karyawan = $this->input->post('karyawan');
        } else {
            $karyawan = $this->input->post('karyawan1');
        }

        $this->db->where('id', $karyawan);
        $cek_peng =  $this->db->get('karyawan')->row_array();

        $data = [
            'nama' => $nama,
            'id_peng' => $karyawan,
            'nominal' =>  $this->input->post('nominal'),
            'tenor' =>  $this->input->post('tenor')
        ];
        $this->db->where('id', $id);
        $this->db->update('data_cicilan', $data);

        //Status Lunas
        $get_cicilan = $this->db->get_where('data_cicilan', ['id' => $id])->row_array();
        if ($get_cicilan['tenor'] == 0) {
            $tgl = date('Y-m-d');
            $this->db->set('tgl_lunas', $tgl);
            $this->db->set('status', 'Lunas');
            $this->db->where('id', $id);
            $this->db->update('data_cicilan');
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data cicilan <strong>' . $nama . '</strong> dengan nama <strong>' . $cek_peng['nama'] . '</strong> berhasil diupdate
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/data_cicilan');
    }

    public function tutup_absen_pegawai()
    {
        $segmen = $this->uri->segment(3);
        $tgll = $this->uri->segment(4);
        $id    = $this->input->get('id');

        $this->db->set('status', 1);
        $this->db->where('id', $id);
        $this->db->update('data_absen_pegawai');
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen pegawai tanggal <strong>' . mediumdate_indo(date($tgll)) . '</strong> berhasil di tutup.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        if ($segmen == 'daftar_absen') {
            redirect('admin/absen_pegawai');
        } else if ($segmen == 'view_absen') {
            redirect('admin/view_absen_pegawai/' . $tgll . '?id=' . $id . '');
        }
    }

    public function batal_absen_pegawai()
    {
        $tgl = $this->uri->segment(3);
        $id    = $this->input->get('id');

        $this->db->set('status', 0);
        $this->db->where('id', $id);
        $this->db->update('data_absen_pegawai');
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen pegawai tanggal <strong>' . mediumdate_indo(date($tgl)) . '</strong> berhasil di batalkan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/absen_pegawai');
    }


    public function update_status_absen_pegawai()
    {
        $id    = $this->input->post('id');
        $status  = $this->input->post('absen');

        $cek = $this->db->get_where('absen_pegawai', ['id' => $id])->row_array();
        $nama = $this->db->get_where('karyawan', ['id' => $cek['id_peng']])->row_array();

        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $this->db->update('absen_pegawai');

        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen pegawai <strong>' . $nama['nama'] . '</strong> berhasil di update.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/view_absen_pegawai/' . $cek['tgl'] . '?id=' . $cek['role_absen'] . '');
    }

    public function update_time_absen_pegawai()
    {
        $id    = $this->input->post('id');
        $jam_keluar  = $this->input->post('jam_keluar');

        $cek = $this->db->get_where('absen_pegawai', ['id' => $id])->row_array();
        $nama = $this->db->get_where('karyawan', ['id' => $cek['id_peng']])->row_array();

        $date1 = new DateTime($cek['tgl'] . 'T' . $cek['jam_masuk']);
        $date2 = new DateTime($cek['tgl'] . 'T' . $jam_keluar);

        $diff = $date2->diff($date1);

        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24);

        $this->db->set('jam_keluar', $jam_keluar);
        $this->db->set('sum_jam', $hours);
        $this->db->where('id', $id);
        $this->db->update('absen_pegawai');

        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen pegawai <strong>' . $nama['nama'] . '</strong> berhasil di update.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/view_absen_pegawai/' . $cek['tgl'] . '?id=' . $cek['role_absen'] . '');
    }

    public function batal_penggajian()
    {
        $id    = $this->input->get('id');
        $cek = $this->db->get_where('penggajian', ['id' => $id])->row_array();
        $nama = $this->db->get_where('karyawan', ['id' => $cek['id_peng']])->row_array();

        $this->db->set('status', 0);
        $this->db->where('id', $id);
        $this->db->update('penggajian');

        //Status Lunas
        $get_cicilan = $this->db->get_where('data_cicilan', ['id_peng' => $cek['id_peng'], 'tenor' => 0])->row_array();
        if ($get_cicilan['tenor'] == 0) {
            $this->db->set('tgl_lunas', 0);
            $this->db->set('status', 'Belum Lunas');
            $this->db->where('id_peng', $nama['id']);
            $this->db->where('tenor', 0);
            $this->db->update('data_cicilan');
        }


        $this->db->set('tenor', 'tenor+1', FALSE);
        $this->db->where('id_peng', $nama['id']);
        $this->db->update('data_cicilan');

        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data penggajian <strong>' . $nama['nama'] . '</strong> berhasil di batalkan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/penggajian');
    }

    public function update_penggajian()
    {
        $id = $this->input->post('id');
        $id_peng = $this->input->post('id_peng');
        $nama = $this->db->get_where('karyawan', ['id' => $id_peng])->row_array();

        //sum hdir
        $get_penggajian = $this->db->get_where('penggajian', ['id' => $id])->row_array();
        $tgl_akhir = $get_penggajian['tgl_akhir'];

        $this->db->set('tgl_akhir', $tgl_akhir);
        $this->db->set('status', 1);
        $this->db->where('id', $id);
        $this->db->update('penggajian');

        $this->db->set('tenor', 'tenor-1', FALSE);
        $this->db->where('id_peng', $nama['id']);
        $this->db->update('data_cicilan');

        //Status Lunas
        $get_cicilan = $this->db->get_where('data_cicilan', ['id_peng' => $id_peng, 'tenor' => 0])->row_array();
        if ($get_cicilan['tenor'] == 0) {
            $tgl = date('Y-m-d');
            $this->db->set('tgl_lunas', $tgl);
            $this->db->set('status', 'Lunas');
            $this->db->where('id_peng', $nama['id']);
            $this->db->where('tenor', 0);
            $this->db->update('data_cicilan');
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        Data Penggajian <strong>' . $nama['nama'] . '</strong> berhasil diupdate :)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      </div>');
        redirect('admin/penggajian');
    }


    public function password_karyawan()
    {
        $id    = $this->input->post('id');
        $psswrd = $this->input->post('password');

        $this->db->set('password', password_hash($psswrd, PASSWORD_DEFAULT));
        $this->db->where('id', $id);
        $this->db->update('karyawan');
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Password <strong>Karyawan</strong> berhasil di Update.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
        redirect('admin/update_karyawan?id=' . $id);
    }
}
