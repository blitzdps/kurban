<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4 class="mb-5 header-title"><i class="fas fa-list"></i> <?= $title ?>
                        <div class="float-right mr-1">
                            <a href="<?= base_url('admin/tambah_karyawan') ?>" class="btn btn-block btn-success btn-sm"><i class="fa fa-plus-circle"></i> Tambah</a>
                        </div>
                    </h4>
                    <?= $this->session->flashdata('message') ?>

                    <div style="width:100%; overflow-x:scroll">
                        <table class="table table-hover display" id="mytable" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Lengkap</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Telepon</th>
                                    <th scope="col">Penempatan</th>
                                    <th scope="col">Kelas</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($karyawan as $d) : ?>
                                    <?php $rib = $this->db->get_where('data_pendidikan', ['id' => $d['id_pend']])->row_array(); ?>
                                    <?php $kel = $this->db->get_where('data_kelas', ['id' => $d['id_kelas']])->row_array(); ?>
                                    <tr>
                                        <th scope="row"><?= $i ?></th>
                                        <td><?= $d['nama'] ?></td>
                                        <td><?= $d['email'] ?></td>
                                        <td><?= $d['telp'] ?></td>
                                        <td><?= $rib['nama'] ?></td>
                                        <td><?= $kel['nama'] ?></td>
                                        <td>
                                            <?php if ($d['role_id'] == '2') : ?>
                                                <span class="badge badge-success">Kesiswaan</span>
                                            <?php elseif ($d['role_id'] == '3') : ?>
                                                <span class="badge badge-primary">Guru</span>
                                            <?php elseif ($d['role_id'] == '4') : ?>
                                                <span class="badge badge-warning">Karyawan Umum</span>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/update_karyawan?id=') ?><?= $d['id'] ?>" class="badge badge-success">Edit</a>
                                            <a href="" class="badge badge-danger" data-toggle="modal" data-target="#deleteData<?= $d['id'] ?>">Hapus</a>

                                        </td>
                                    </tr>
                                <?php $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Hapus  -->

        <?php foreach ($karyawan as $d) : ?>
            <!--delete Data-->
            <div class="modal fade" id="deleteData<?= $d['id'] ?>" role="dialog" aria-labelledby="addNewDataLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNewDataLabel">Hapus Karyawan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Anda yakin ingin menghapus data <?= $d['nama'] ?></p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <a href="<?= base_url('hapus/hapus_karyawan?id=') ?><?= $d['id'] ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</a>
                        </div>

                    </div>
                </div>
            </div>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('#pendidikan<?= $d['id'] ?>').change(function() {
                        $.ajax({
                            type: 'POST',
                            url: '<?= site_url('get/get_kelas'); ?>',
                            data: {
                                pendidikan: this.value
                            },
                            cache: false,
                            success: function(response) {
                                $('#kelas<?= $d['id'] ?>').html(response);
                            }
                        });
                    });
                });
            </script>

        <?php endforeach ?>

    </div>


</div>
<!-- /.container-fluid -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#pendidikan').change(function() {
            $.ajax({
                type: 'POST',
                url: '<?= site_url('get/get_kelas'); ?>',
                data: {
                    pendidikan: this.value
                },
                cache: false,
                success: function(response) {
                    $('#kelas').html();
                    $('#kelas').html(response);
                }
            });
        });
    });
</script>