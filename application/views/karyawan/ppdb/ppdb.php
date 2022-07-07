<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4 class="mb-5 header-title"><i class="fas fa-list"></i> <?= $title ?></h4>
                    <?= $this->session->flashdata('message') ?>

                    <div style="width:100%; overflow-x:scroll">
                        <table class="table table-hover display" id="mytable" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Lengkap</th>
                                    <th scope="col">NIK</th>
                                    <th scope="col">NIS</th>
                                    <th scope="col">Alamat</th>
                                    <th scope="col">Tanggal Lahir</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($siswa as $d) : ?>
                                    <?php
                                    $ttl = $d['ttl'];
                                    $lahir    = new DateTime($ttl);
                                    $today        = new DateTime();
                                    $diff = $today->diff($lahir);
                                    ?>
                                    <tr>
                                        <th scope="row"><?= $i ?></th>
                                        <td width="180"><?= $d['nama'] ?></td>
                                        <td width="150"><?= $d['nik'] ?></td>
                                        <td width="100"><?= $d['nis'] ?></td>
                                        <td width="320"><?= $d['alamat'] ?></td>
                                        <td><?= mediumdate_indo(date($ttl)) ?>
                                            <span class="badge badge-info">
                                                <font size="1.5px"> <?php echo 'Umur ' . $diff->y . ' Tahun';  ?></font>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('karyawan/edit_ppdb?id=') ?><?= $d['id'] ?>" class="badge badge-success"><i class="fa fa-redo"></i> Kelola</a>
                                            <a href="" class="badge badge-danger" data-toggle="modal" data-target="#deleteData<?= $d['id'] ?>"><i class="fa fa-trash"></i> Hapus</a>

                                        </td>
                                    </tr>
                                <?php $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Data Hapus  -->

            <?php foreach ($siswa as $d) : ?>
                <!--delete Data-->
                <div class="modal fade" id="deleteData<?= $d['id'] ?>" role="dialog" aria-labelledby="addNewDataLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addNewDataLabel">Hapus PPDB</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Anda yakin ingin menghapus data <?= $d['nama'] ?></p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <a href="<?= base_url('hapus/hapus_ppdb/karyawan?id=') ?><?= $d['id'] ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach ?>

        </div>

    </div>

</div>
<!-- /.container-fluid -->