<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hapus extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function hapus_siswa()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');

    $this->db->delete('siswa', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data siswa berhasil dihapus
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

  public function hapus_ppdb()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');

    $g =  $this->db->get('ppdb')->row_array();

    if (!empty($g['img_siswa'])) {
      unlink("./assets/img/data/" . $g['img_siswa']);
    }
    if (!empty($g['img_kk'])) {
      unlink("./assets/img/data/" . $g['img_kk']);
    }
    if (!empty($g['img_ijazah'])) {
      unlink("./assets/img/data/" . $g['img_ijazah']);
    }
    if (!empty($g['img_ktp'])) {
      unlink("./assets/img/data/" . $g['img_ktp']);
    }

    $this->db->delete('ppdb', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data PPDB berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    if ($segmen == 'karyawan') {
      redirect('karyawan/ppdb');
    } elseif ($segmen == 'admin') {
      redirect('admin/ppdb');
    }
  }

  public function hapus_pelanggaran()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $id_pelang = $this->uri->segment(4);

    $sis = $this->db->get_where('pelanggaran', ['id' => $id])->row_array();
    $top_pelang = $this->db->get_where('data_pelanggaran', ['id' => $id_pelang])->row_array();
    //Menambah Point
    $point = $this->db->get_where('siswa', ['id' => $sis['id_siswa']])->row_array();

    $this->db->set('point', $point['point'] + $top_pelang['point']);
    $this->db->where('id', $sis['id_siswa']);
    $this->db->update('siswa');
    //mengurangi jumlah
    $this->db->set('jumlah', $top_pelang['jumlah'] - 1);
    $this->db->where('id', $id_pelang);
    $this->db->update('data_pelanggaran');

    $this->db->delete('pelanggaran', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Pelanggaran berhasil dihapus
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

  public function hapus_data_pelanggaran()
  {
    $id = $this->input->get('id');
    $this->db->delete('data_pelanggaran', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data jenis Pelanggaran berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    redirect('admin/data_pelanggaran');
  }

  public function hapus_daftar_absen()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');

    $this->db->delete('absen', array('role_absen' => $id));
    $this->db->delete('daftar_absen', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data daftar Absen berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    if ($segmen == 'karyawan') {
      redirect('karyawan/daftar_absen');
    } else {
      redirect('admin/daftar_absen');
    }
  }

  public function hapus_absen_siswa()
  {
    $segmen = $this->uri->segment(3);
    $tgl = $this->uri->segment(4);
    $id = $this->input->get('id');
    $absen =  $this->db->get_where('absen', ['id' => $id])->row_array();

    $this->db->delete('absen', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absen siswa <strong>' . $absen['siswa'] . '</strong> berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    if ($segmen == 'karyawan') {
      redirect('karyawan/absen/' . $tgl . '?id=' . $absen['role_absen'] . '');
    } else {
      redirect('admin/absen/' . $tgl . '?id=' . $absen['role_absen'] . '');
    }
  }

  public function hapus_perizinan()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $id_izin = $this->uri->segment(4);

    $this->db->delete('perizinan', array('id' => $id));

    $this->db->where('id', $id_izin);
    $this->db->update('data_perizinan');

    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data perizinan berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    if ($segmen == 'karyawan') {
      redirect('karyawan/perizinan');
    } elseif ($segmen == 'siswa') {
      redirect('siswa/perizinan');
    } else {
      redirect('admin/perizinan');
    }
  }

  public function hapus_data_perizinan()
  {
    $id = $this->input->get('id');
    $this->db->delete('data_perizinan', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data perizinan berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    redirect('admin/data_perizinan');
  }

  public function hapus_data_pendidikan()
  {
    $id = $this->input->get('id');
    $this->db->delete('data_pendidikan', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Pendidikan berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    redirect('admin/data_pendidikan');
  }
  public function hapus_data_kursi()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');

    $id3 = $this->db->get_where('data_kursi', ['id' => $id])->row_array();
    $kelas1 = $this->db->get_where('data_kelas', ['id' => $id3['id_kelas']])->row_array();

    $this->db->delete('data_kursi', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data kursi berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    if ($segmen == 'karyawan') {
      redirect('karyawan/view_kelas/' . $kelas1['id'] . '');
    } elseif ($segmen == 'admin') {
      redirect('admin/view_kelas/' . $kelas1['id'] . '');
    } else {
      redirect('admin/data_kursi');
    }
  }

  public function hapus_kelas()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');

    $this->db->delete('data_kelas', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kelas berhasil dihapus
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

  public function hapus_konseling()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $this->db->delete('konseling', array('id' => $id));
    $this->db->delete('balas_konseling', array('role_konseling' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Konseling berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    if ($segmen == 'siswa') {
      redirect('siswa/konseling');
    } elseif ($segmen == 'karyawan') {
      redirect('karyawan/konseling');
    } else {
      redirect('admin/konseling');
    }
  }

  public function hapus_isi_kursi()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');

    $cek1 = $this->db->get_where('data_kursi', ['id' => $id])->row_array();
    $id2 = $cek1['kelas'];
    $kelas1 = $this->db->get_where('kelas', ['kelas' => $id2])->row_array();
    $id_kelas = $kelas1['id'];

    $data = [
      'siswa' => '',
    ];
    $this->db->where('id', $id);
    $this->db->update('data_kursi', $data);

    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data isi kursi berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');

    if ($segmen == 'karyawan') {
      redirect('karyawan/view_kelas/' . $id_kelas . '');
    } else {
      redirect('admin/view_kelas/' . $id_kelas . '');
    }
  }

  public function hapus_karyawan()
  {
    $id = $this->input->get('id');
    $this->db->delete('karyawan', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Karyawan berhasil dihapus
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');

    redirect('admin/karyawan');
  }

  public function hapus_acara()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $this->db->where('id', $id);
    $g =  $this->db->get('acara')->row_array();
    unlink("./assets/img/blog/" . $g['img']);

    $this->db->delete('acara', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Acara berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');

    if ($segmen == 'karyawan') {
      redirect('karyawan/acara');
    } else {
      redirect('admin/acara');
    }
  }

  public function hapus_kategori_acara()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $this->db->delete('kategori_acara', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kategori Acara berhasil dihapus.
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

  public function hapus_gallery()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $this->db->where('id', $id);
    $g =  $this->db->get('gallery')->row_array();
    unlink("./assets/img/gallery/" . $g['img']);

    if (!empty($g['img1'])) {
      unlink("./assets/img/gallery/" . $g['img1']);
    }
    if (!empty($g['img2'])) {
      unlink("./assets/img/gallery/" . $g['img2']);
    }
    if (!empty($g['img3'])) {
      unlink("./assets/img/gallery/" . $g['img3']);
    }

    $this->db->delete('gallery', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Acara berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');

    if ($segmen == 'karyawan') {
      redirect('karyawan/gallery');
    } else {
      redirect('admin/gallery');
    }
  }

  public function hapus_foto_gallery()
  {
    $segmen = $this->uri->segment(3);
    $foto = $this->uri->segment(4);
    $id = $this->input->get('id');

    $this->db->where('id', $id);
    $g =  $this->db->get('gallery')->row_array();


    if ($foto == 'satu') {
      unlink("./assets/img/gallery/" . $g['img1']);
      $this->db->set('img1', '');
    } elseif ($foto == 'dua') {
      unlink("./assets/img/gallery/" . $g['img2']);
      $this->db->set('img2', '');
    } elseif ($foto == 'tiga') {
      unlink("./assets/img/gallery/" . $g['img3']);
      $this->db->set('img3', '');
    }

    $this->db->where('id', $id);
    $this->db->update('gallery');
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Gambar ' . $foto . ' berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');

    if ($segmen == 'karyawan') {
      redirect('karyawan/edit_gallery?id=' . $id);
    } elseif ($segmen == 'admin') {
      redirect('admin/edit_gallery?id=' . $id);
    }
  }

  public function hapus_kategori_gallery()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $this->db->delete('kategori_gallery', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kategori Acara berhasil dihapus.
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

  public function hapus_kontak()
  {
    $segmen = $this->uri->segment(3);
    $id = $this->input->get('id');
    $this->db->delete('kontak', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Kontak berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    if ($segmen == 'karyawan') {
      redirect('karyawan/kontak');
    } else {
      redirect('admin/kontak');
    }
  }

  public function hapus_data_divisi()
  {
    $id = $this->input->get('id');
    $this->db->delete('data_divisi', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Divisi berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    redirect('admin/data_divisi');
  }

  public function hapus_data_cicilan()
  {
    $id = $this->input->get('id');
    $this->db->delete('data_cicilan', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data cicilan berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    redirect('admin/data_cicilan');
  }

  public function hapus_penggajian()
  {
    $id = $this->input->get('id');
    $this->db->delete('penggajian', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data cicilan berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    redirect('admin/penggajian');
  }

  public function hapus_absen_pegawai()
  {
    $id = $this->input->get('id');
    $this->db->delete('data_absen_pegawai', array('id' => $id));
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Data absensi pegawai berhasil dihapus.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>');
    redirect('admin/absen_pegawai');
  }
}
