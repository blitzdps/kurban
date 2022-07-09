<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4 class="mb-5 header-title"><i class="fas fa-list"></i> <?= $title ?>
                        <div class="float-right mr-1">
                            <a href="<?= base_url('admin/tambah_masjid') ?>" class="btn btn-block btn-success btn-sm"><i class="fa fa-plus-circle"></i> Tambah</a>
                        </div>
                    </h4>
                    <?= $this->session->flashdata('message') ?>

                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover display" id="mytable" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Masjid</th>
                                    <th scope="col">Jumlah Sapi</th>
                                    <th scope="col">Jumlah Kambing</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $masjid as $key => $masjid ) : ?>
                                <tr>
                                    <td><?php echo $key+1 ?></td>
                                    <td><?php echo $masjid['nama_masjid']; ?></td>
                                    <td><?php echo $masjid['sapi']; ?></td>
                                    <td><?php echo $masjid['kambing']; ?></td>
                                    <td><?php echo $masjid['jumlah']; ?></td>
                                <td>
                                            <a href="<?= base_url('admin/ubah_masjid/') ?><?= $masjid['id_masjid'] ?>" class="badge badge-success">Edit</a>
                                            <a href="<?= base_url('admin/hapus_masjid/') ?><?= $masjid['id_masjid'] ?>" class="badge badge-success">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach ; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Hapus  -->

    </div>


</div>
<!-- /.container-fluid -->