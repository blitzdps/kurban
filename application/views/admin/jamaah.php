<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4 class="mb-5 header-title"><i class="fas fa-list"></i> <?= $title ?>
                        <div class="float-right mr-1">
                            <a href="" class="btn btn-block btn-success btn-sm" data-toggle="modal" data-target="#exportData"><i class="fa fa-file-export"></i> Export</a>
                        </div>
                        <div class="float-right pr-1">
                            <a href="" class="btn btn-block btn-primary btn-sm" data-toggle="modal" data-target="#importData"><i class="fa fa-file-import"></i> Import</a>
                        </div>
                    </h4>
                    <?= $this->session->flashdata('message') ?>
                    <form style="margin: 20px 0;" action="<?= base_url() . 'admin/tambah_jamaah'; ?>" method="post">
                        <div class="form-row">
                            <div class="form-group col-lg-3">
                                <a href="<?= base_url('admin/tambah_jamaah'); ?>" class="btn btn-block btn-info"><i class="fa fa-plus-circle"></i> Pendaftaran jamaah</a>
                            </div>
                            
                    </form>
                </div>

               
                <div style="width:100%; overflow-x:scroll">
                    <table class="table table-hover display" id="mytable" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Jamaah</th>
                                <th scope="col">Alamat</th>
                                <th scope="col">Blok</th>
                                <th scope="col">No</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php foreach ( $jamaah as $key => $jamaah ) : ?>
                                <tr>
                                    <td><?php echo $key+1 ?></td>
                                    <td><?php echo $jamaah['nama']; ?></td>
                                    <td><?php echo $jamaah['alamat']; ?></td>
                                    <td><?php echo $jamaah['blok']; ?></td>
                                    <td><?php echo $jamaah['no']; ?></td>
                                    <td><?php echo $jamaah['status']; ?></td>
                                <td>
                                            <a href="<?= base_url('admin/ubah_jamaah/') ?><?= $jamaah['id_jamaah'] ?>" class="badge badge-success">Edit</a>
                                            <a href="<?= base_url('admin/hapus_jamaah/') ?><?= $jamaah['id_jamaah'] ?>" class="badge badge-success">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach ; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Data Hapus  -->

    </div>

    <div class="modal fade" id="importData" role="dialog" aria-labelledby="addNewDataLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewDataLabel">Import Data siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <?= form_open_multipart(); ?>

                    <div class="form-group files">
                        <label>Upload File Excel</label>
                        <input type="file" class="form-control" multiple="" name="excel">
                    </div>


                    <label>Contoh data excel
                        <a href="<?= base_url('assets/contoh/Contoh_data_siswa.xlsx') ?>" class="badge badge-pill badge-success" download>Download</a></label>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" name="submit" value="upload" class="btn btn-success"><i class="fa fa-file-import"></i> Import</button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exportData" role="dialog" aria-labelledby="addNewDataLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewDataLabel">Export Jamaah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin mengexport data semua Jammah</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <a href="<?= base_url('admin/export_data') ?>" class="btn btn-success"><i class="fa fa-file-export"></i> Export</a>
                </div>

            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
