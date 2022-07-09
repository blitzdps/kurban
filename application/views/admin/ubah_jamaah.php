<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->

    <div class="row">

        <div class="col-md-12 text-center">
            <h1 class="h3 mb-4 text-gray-800"><i class="fa fa-user-plus fa-fw"></i> Ubah Jamaah Baru</h1>
            <hr />
        </div>
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="pt-2 fa fa-list-alt fa-fw"></i> Form Ubah data

                        <div class="float-right">
                            <a href="<?= base_url('admin/ubah_jamaah') ?>" class="btn btn-block btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Data Jamaah</a>
                        </div>
                    </h6>
                </div>
                <div class="card-body">
                    <?= form_error('menu', '<div class="alert alert-danger" role="alert">', '</div>') ?>
                    <?= $this->session->flashdata('message') ?>
                    <form action="" method="post">
                    <input type="hidden" name="id_jamaah" value="<?php echo $jamaah['id_jamaah'];?>">

                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>Nama Jamaah</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Jamaah" value="<?php echo $jamaah['nama'];?>">
                                    <?= form_error('nama', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>
                            
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" value="<?php echo $jamaah['alamat'];?>">
                                    <?= form_error('alamat', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>

                                <div class="form-group">
                                    <label>Blok</label>
                                    <input type="text" class="form-control" id="blok" name="blok" placeholder="Blok" value="<?php echo $jamaah['blok'];?>">
                                    <?= form_error('blok', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>

                                <div class="form-group">
                                    <label>Nomor</label>
                                    <input type="number" class="form-control" id="no" name="no" placeholder="Nomor" value="<?php echo $jamaah['no'];?>">
                                    <?= form_error('no', '<small class="text-danger pl-3">', ' </small>') ?>
                                </div>

                                <div class="form-group">
                                <label>Status</label>
                                    <div class="input-group-addon">
                                    <select class="form-control" name="status" id="status">
                                    <option value="">--Pilih Status--</option>
                                        <option value="Sudah Terima" <?php if($jamaah['status'] == 'Sudah Terima'){echo "selected";} ?>>Sudah Terima</option>
                                        <option value="Belum" <?php if($jamaah['status'] == 'Belum'){echo "selected";} ?>>Belum</option>
                                    </select>
                                    <small class="form-text text-danger"> <?php echo form_error('status');?> </small>
                                    </div>
                                </div>


                                <div class="pt-3 form-group row">
                            <div class="col-md-12">
                                <button type="submit" name="ubah_jamaah" class="btn-block btn btn-primary">Ubah Data</button>
                            </div>
                            <div class="col-md-12">
                            <a href="<?php echo site_url('admin/jamaah');?>"> <button type="button" class="btn-block btn btn-primary">Batal</button></a>
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