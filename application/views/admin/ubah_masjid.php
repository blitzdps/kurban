<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->

    <div class="row">

        <div class="col-md-12 text-center">
            <h1 class="h3 mb-4 text-gray-800"><i class="fa fa-user-plus fa-fw"></i> Ubah Masjid</h1>
            <hr />
        </div>
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="pt-2 fa fa-list-alt fa-fw"></i> Form Ubah

                        <div class="float-right">
                            <a href="<?= base_url('admin/ubah_masjid') ?>" class="btn btn-block btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Data Masjid</a>
                        </div>
                    </h6>
                </div>
                <div class="card-body">
                    <?= form_error('menu', '<div class="alert alert-danger" role="alert">', '</div>') ?>
                    <?= $this->session->flashdata('message') ?>
                    <form action="" method="post">
                    <input type="hidden" name="id_masjid" value="<?php echo $masjid['id_masjid'];?>">

                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>Nama Masjid</label>
                                    <input type="text" class="form-control" id="nama_masjid" name="nama_masjid" placeholder="Nama Masjid" value="<?php echo $masjid['nama_masjid'];?>">
                                    <?= form_error('nama_masjid', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>
                            
                                <div class="form-group">
                                    <label>Jumlah Sapi</label>
                                    <input type="number" class="form-control" id="sapi" name="sapi" placeholder="Jumlah Sapi" value="<?php echo $masjid['sapi'];?>">
                                    <?= form_error('sapi', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>

                                <div class="form-group">
                                    <label>Jumlah Kambing</label>
                                    <input type="number" class="form-control" id="kambing" name="kambing" placeholder="Jumlah Kambing" value="<?php echo $masjid['kambing'];?>">
                                    <?= form_error('kambing', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>

                                <div class="form-group">
                                    <label>Total Hewan Kurban</label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Total hewan kurban" value="<?php echo $masjid['jumlah'];?>">
                                    <?= form_error('jumlah', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>

                            </div>

                          

                        </div>
                        <div class="pt-3 form-group row">
                            <div class="col-md-12">
                                <button type="submit" name="ubah_masjid" class="btn-block btn btn-primary">Ubah Data</button>
                            </div>
                            <div class="col-md-12">
                            <a href="<?php echo site_url('admin/masjid');?>"> <button type="button" class="btn-block btn btn-primary">Batal</button></a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /.container-fluid -->

</div>
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
                    $('#kelas').html(response);
                }
            });
        });
    });
</script>