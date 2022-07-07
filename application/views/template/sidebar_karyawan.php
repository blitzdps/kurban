<?php
$notif_izin      = $this->db->get_where('perizinan', ['status' => 'Pending', 'id_pend' => $user['id_pend']])->num_rows();
$notif_konseling = $this->db->get_where('konseling', ['status' => 'Pending', 'id_peng' => $user['id']])->num_rows();
$notif_ppdb = $this->db->get_where('ppdb', ['status' => '0'])->num_rows();
$notif_kontak = $this->db->get_where('kontak', ['status' => 1])->num_rows();
?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('karyawan'); ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-home"></i>
        </div>
        <?php if ($user['role_id'] == 2) : ?>
            <div class="sidebar-brand-text mx-3">Kesiswaan</div>
        <?php elseif ($user['role_id'] == 3) : ?>
            <div class="sidebar-brand-text mx-3">Guru</div>
        <?php elseif ($user['role_id'] == 4) : ?>
            <div class="sidebar-brand-text mx-3">Karyawan</div>
        <?php endif ?>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="<?= base_url('karyawan'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menu Madrasah
    </div>
    <?php if ($user['role_id'] == 2) : ?>
        <?php if ($menu == 'ppdb') : ?>
            <li class="nav-item active">
            <?php else : ?>
            <li class="nav-item">
            <?php endif; ?>
            <a class="nav-link" href="<?= base_url('karyawan/ppdb'); ?>">
                <i class="fas fa-fw fa-address-card"></i>
                <span>PPDB </span> &nbsp;
                <?php if ($notif_ppdb) : ?>
                    <span class="badge badge-danger" style="font-size: 10px;"><?= $notif_ppdb ?></span>
                <?php endif ?>
            </a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <?php if ($menu == 'menu-1') : ?>
                <li class="nav-item active">
                <?php else : ?>
                <li class="nav-item">
                <?php endif; ?>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>siswa</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilih menu:</h6>
                        <a class="collapse-item" href="<?= base_url('karyawan/daftar_siswa'); ?>">Daftar siswa</a>
                        <a class="collapse-item" href="<?= base_url('karyawan/tambah_siswa'); ?>">Pendaftaran siswa</a>
                    </div>
                </div>
                </li>
            <?php endif ?>

            <?php if ($user['role_id'] !== '4') : ?>

                <?php if ($user['role_id'] == '2') : ?>
                    <!-- Nav Item - Utilities Collapse Menu -->
                    <?php if ($menu == 'menu-3') : ?>
                        <li class="nav-item active">
                        <?php else : ?>
                        <li class="nav-item">
                        <?php endif; ?>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUU" aria-expanded="true" aria-controls="collapseUU">
                            <i class="fas fa-fw fa-list-alt"></i>
                            <span>Data Madrasah </span>
                            <?php $sum_notif = $notif_konseling + $notif_izin; ?>
                            <?php if ($notif_konseling || $notif_izin) : ?><span class="badge badge-danger" style="font-size: 10px;"> New <?= $sum_notif ?></span><?php endif ?>
                        </a>
                        <div id="collapseUU" class="collapse" aria-labelledby="headingUU" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <h6 class="collapse-header">Pilih Menu:</h6>
                                <a class="collapse-item" href="<?= base_url('karyawan/konseling'); ?>">Konseling <?php if ($notif_konseling) : ?><span class="badge badge-danger"><?= $notif_konseling ?></span><?php endif ?></a>
                                <a class="collapse-item" href="<?= base_url('karyawan/perizinan'); ?>">Perizinan <?php if ($notif_izin) : ?><span class="badge badge-danger"><?= $notif_izin ?></span><?php endif ?></a>
                                <a class="collapse-item" href="<?= base_url('karyawan/kelas'); ?>">Daftar Kelas</a>
                                <a class="collapse-item" href="<?= base_url('karyawan/daftar_absen'); ?>">Presensi</a>
                                <a class="collapse-item" href="<?= base_url('karyawan/pelanggaran'); ?>">Pelanggaran</a>
                            </div>
                        </div>
                        </li>

                    <?php else : ?>
                        <?php if ($menu == 'menu-3') : ?>
                            <li class="nav-item active">
                            <?php else : ?>
                            <li class="nav-item">
                            <?php endif; ?>
                            <a class="nav-link" href="<?= base_url('karyawan/daftar_absen'); ?>">
                                <i class="fas fa-fw fa-list-alt"></i>
                                <span>Presensi </span>
                            </a>
                            </li>
                        <?php endif ?>

                    <?php endif ?>

                    <?php if ($menu == 'gaji') : ?>
                        <li class="nav-item active">
                        <?php else : ?>
                        <li class="nav-item">
                        <?php endif; ?>
                        <a class="nav-link" href="<?= base_url('karyawan/penggajian'); ?>">
                            <i class="fas fa-fw fa-address-card"></i>
                            <span>Hist Penggajian </span>
                        </a>
                        </li>

                        <?php if ($user['role_id'] == 2) : ?>
                            <!-- Divider -->
                            <hr class="sidebar-divider">

                            <!-- Heading -->
                            <div class="sidebar-heading">
                                Website
                            </div>



                            <!-- Nav Item - Pages Collapse Menu -->
                            <?php if ($menu == 'acara') : ?>
                                <li class="nav-item active">
                                <?php else : ?>
                                <li class="nav-item">
                                <?php endif; ?>
                                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAcara" aria-expanded="true" aria-controls="collapseAcara">
                                    <i class="fas fa-fw fa-calendar-day"></i>
                                    <span>Acara</span>
                                </a>
                                <div id="collapseAcara" class="collapse" aria-labelledby="headingAcara" data-parent="#accordionSidebar">
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        <h6 class="collapse-header">Pilih Menu:</h6>
                                        <a class="collapse-item" href="<?= base_url('karyawan/tambah_acara'); ?>">Tambah Acara</a>
                                        <a class="collapse-item" href="<?= base_url('karyawan/acara'); ?>">Data Acara</a>
                                        <a class="collapse-item" href="<?= base_url('karyawan/kategori_acara'); ?>">Data Kategori</a>
                                    </div>
                                </div>
                                </li>


                                <!-- Nav Item - Pages Collapse Menu -->
                                <?php if ($menu == 'gallery') : ?>
                                    <li class="nav-item active">
                                    <?php else : ?>
                                    <li class="nav-item">
                                    <?php endif; ?>
                                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGallery" aria-expanded="true" aria-controls="collapseGallery">
                                        <i class="fas fa-fw fa-image"></i>
                                        <span>Gallery</span>
                                    </a>
                                    <div id="collapseGallery" class="collapse" aria-labelledby="headingGallery" data-parent="#accordionSidebar">
                                        <div class="bg-white py-2 collapse-inner rounded">
                                            <h6 class="collapse-header">Pilih Menu:</h6>
                                            <a class="collapse-item" href="<?= base_url('karyawan/tambah_gallery'); ?>">Tambah Gallery</a>
                                            <a class="collapse-item" href="<?= base_url('karyawan/gallery'); ?>">Data Gallery</a>
                                            <a class="collapse-item" href="<?= base_url('karyawan/kategori_gallery'); ?>">Data Kategori</a>
                                        </div>
                                    </div>
                                    </li>


                                    <?php if ($menu == 'kontak') : ?>
                                        <li class="nav-item active">
                                        <?php else : ?>
                                        <li class="nav-item">
                                        <?php endif; ?>
                                        <a class="nav-link" href="<?= base_url('karyawan/kontak'); ?>">
                                            <i class="fas fa-fw fa-address-book"></i>
                                            <span>Kontak</span>
                                            <?php $notif_kontak; ?>
                                            <?php if ($notif_kontak) : ?>
                                                <span class="badge badge-danger" style="font-size: 10px;"> <?= $notif_kontak ?></span>
                                            <?php endif ?>
                                        </a>
                                        </li>

                                    <?php endif ?>
                                    <!-- Divider -->
                                    <hr class="sidebar-divider">

                                    <!-- Heading -->
                                    <div class="sidebar-heading">
                                        Pengaturan
                                    </div>


                                    <?php if ($menu == 'menu-5') : ?>
                                        <li class="nav-item active">
                                        <?php else : ?>
                                        <li class="nav-item">
                                        <?php endif; ?>
                                        <a class="nav-link" href="<?= base_url('karyawan/setting'); ?>">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>Setting Akun</span>
                                        </a>
                                        </li>

                                        <!-- Nav Item - Tables -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="" data-toggle="modal" data-target="#logoutModal">
                                                <i class="fas fa-fw fa-sign-out-alt"></i>
                                                <span>Keluar</span>
                                            </a>
                                        </li>

                                        <!-- Divider -->
                                        <hr class="sidebar-divider d-none d-md-block">

                                        <!-- Sidebar Toggler (Sidebar) -->
                                        <div class="text-center d-none d-md-inline">
                                            <button class="rounded-circle border-0" id="sidebarToggle"></button>
                                        </div>

</ul>
<!-- End of Sidebar -->