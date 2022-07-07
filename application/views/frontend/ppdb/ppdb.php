<!-- Custom styles for this template-->
<link href="<?= base_url('assets/'); ?>css/sb-admin-2.min.css" rel="stylesheet">

<style type="text/css">
    img[src=""] {
        display: none;
    }

    .pointer {
        cursor: pointer;
    }
</style>
<main id="main">

    <!-- ======= Breadcrumbs ======= -->
    <section class="breadcrumbs">
        <div class="container">

            <ol>
                <li><a href="<?= base_url('home'); ?>">Home</a></li>
                <li>PPDB</li>
            </ol>
            <h2>PPDB</h2>

        </div>
    </section><!-- End Breadcrumbs -->

    <section class="inner-page">
        <div class="container" style="padding-left:3px;padding-right:3px">

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h5 class="m-0 font-weight-bold text-success"><i class="fa fa-list-alt fa-fw"></i> <b>Form Pendaftaran</b>
                                    <div class="float-right">
                                        <a href="<?= base_url('ppdb/login') ?>" class="btn btn-block btn-sm btn-primary"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                                    </div>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?= form_error('menu', '<div class="alert alert-danger" role="alert">', '</div>') ?>
                                <?= $this->session->flashdata('message') ?>

                                <?= form_open_multipart('ppdb'); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NIK</label>
                                            <input type="number" class="form-control" id="nik" name="nik" placeholder="Nomor Induk Kependudukan" value="<?= set_value('nik') ?>" require>
                                            <?= form_error('nik', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>NIS</label>
                                            <input type="text" class="form-control" id="nis" name="nis" placeholder="Nomor Induk siswa" value="<?= set_value('nis') ?>" require>
                                            <?= form_error('nis', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group form-box">
                                            <label>Password </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div onclick="myFunction()" class="input-group-text pointer"><i id="icon" class="bi bi-eye"></i></div>
                                                </div>
                                                <input type="text" class="active form-control" id="password" name="password" placeholder="Password" value="<?= set_value('password') ?>" require>
                                            </div>
                                            <?= form_error('password', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Lengkap</label>
                                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" value="<?= set_value('nama') ?>" require>
                                            <?= form_error('nama', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?= set_value('email') ?>" require>
                                            <?= form_error('email', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Nomor Hp</label>
                                            <input type="number" class="form-control" id="no_hp" name="no_hp" placeholder="Nomor Hp" value="<?= set_value('no_hp') ?>" require>
                                            <?= form_error('no_hp', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="jk" class="col-form-label">Jenis Kelamin :</label>
                                            <select class="form-control" id="jk" name="jk" value="<?= set_value('jk') ?>">
                                                <option value="">- Jenis Kelamin -</option>
                                                <option value="L">Laki-Laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>
                                            <?= form_error('jk', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="ttl" name="ttl" value="<?= set_value('ttl') ?>">
                                            <?= form_error('ttl', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Provinsi</label>
                                            <select class="form-control" id="prov" name="prov">
                                                <option>- Pilih Provinsi -</option>
                                                <?php foreach ($prov as $v) : ?>
                                                    <option value="<?= $v['id_prov'] ?>"><?= $v['nama'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <?= form_error('prov', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Kota</label>
                                            <select class="form-control" id="kab" name="kab">
                                                <option>- Pilih provinsi dahulu -</option>
                                            </select>
                                            <?= form_error('kab', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <textarea type="text" rows="4" class="form-control" id="alamat" name="alamat" placeholder="Alamat Lengkap"><?= set_value('alamat') ?></textarea>
                                            <?= form_error('alamat', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Ayah</label>
                                            <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" placeholder="Nama Orang Tua" value="<?= set_value('nama_ayah') ?>">
                                            <?= form_error('nama_ayah', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Ibu</label>
                                            <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" placeholder="Nama Orang Tua" value="<?= set_value('nama_ibu') ?>">
                                            <?= form_error('nama_ibu', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label>Nama Wali</label>
                                            <input type="text" class="form-control" id="nama_wali" name="nama_wali" placeholder="Nama Wali" value="<?= set_value('nama_wali') ?>">
                                            <small class="text-info">* Kosongkan jika tidak ada.</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Pekerjaan Ayah</label>
                                            <select class="form-control" id="pek_ayah" name="pek_ayah" value="<?= set_value('pek_ayah') ?>">
                                                <option value="">- Pekerjaan Ayah -</option>
                                                <option value="Wiraswasta">Wiraswasta</option>
                                                <option value="Pedagang">Pedagang</option>
                                                <option value="Buruh">Buruh</option>
                                                <option value="Pensiunan">Pensiunan</option>
                                                <option value="Guru">Guru</option>
                                                <option value="Honorer">Honorer</option>
                                                <option value="PNS">PNS</option>
                                            </select>
                                            <?= form_error('pek_ayah', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Pekerjaan Ibu</label>
                                            <select class="form-control" id="pek_ibu" name="pek_ibu" value="<?= set_value('pek_ibu') ?>">
                                                <option value="">- Pekerjaan Ibu -</option>
                                                <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                                <option value="Wiraswasta">Wiraswasta</option>
                                                <option value="Pedagang">Pedagang</option>
                                                <option value="Buruh">Buruh</option>
                                                <option value="Pensiunan">Pensiunan</option>
                                                <option value="Guru">Guru</option>
                                                <option value="Honorer">Honorer</option>
                                                <option value="PNS">PNS</option>
                                            </select>
                                            <?= form_error('pek_ibu', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Pekerjaan Wali</label>
                                            <select class="form-control" id="pek_wali" name="pek_wali">
                                                <option value="">- Pekerjaan Wali -</option>
                                                <option value="Tidak ada wali">Tidak ada wali</option>
                                                <option value="Wiraswasta">Wiraswasta</option>
                                                <option value="Buruh">Buruh</option>
                                                <option value="Pensiunan">Pensiunan</option>
                                                <option value="Guru">Guru</option>
                                                <option value="Honorer">Honorer</option>
                                                <option value="PNS">PNS</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Penghasilan Ortu / Wali</label>
                                            <select class="form-control" id="peng_ortu" name="peng_ortu">
                                                <option value="">- Penghasilan / Bulan -</option>
                                                <option value="< Rp.1.000.000">
                                                    << Rp.1.000.000</option>
                                                <option value="Rp.1.000.000 - Rp.2.000.000">Rp.1.000.000 - Rp.2.000.000</option>
                                                <option value="Rp.2.000.000 - Rp.3.000.000">Rp.2.000.000 - Rp.3.000.000</option>
                                                <option value="Rp.3.000.000 - Rp.4.000.000">Rp.3.000.000 - Rp.4.000.000</option>
                                                <option value="Rp.4.000.000 - Rp.5.000.000">Rp.4.000.000 - Rp.5.000.000</option>
                                                <option value="Rp.5.000.000 >">
                                                    Rp.5.000.000 >></option>
                                            </select>
                                            <?= form_error('peng_ortu', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Nomor Telepon Ortu / Wali</label>
                                            <input type="text" class="form-control" id="no_telp" name="no_telp" placeholder="Nomor Telepon" value="<?= set_value('no_telp') ?>">
                                            <?= form_error('no_telp', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Tahun Masuk</label>
                                            <input type="number" class="form-control" id="thn_msk" name="thn_msk" placeholder="Tahun Masuk" value="<?= set_value('thn_msk') ?>">
                                            <?= form_error('thn_msk', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Sekolah Asal</label>
                                            <input type="text" class="form-control" id="sekolah_asal" name="sekolah_asal" placeholder="Sekolah Asal" value="<?= set_value('sekolah_asal') ?>">
                                            <?= form_error('sekolah_asal', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Kelas</label>
                                            <input type="text" class="form-control" id="kelas" name="kelas" placeholder="Kelas" value="<?= set_value('kelas') ?>">
                                            <?= form_error('kelas', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Diniyah</label>
                                            <input type="text" class="form-control" id="diniyah" name="diniyah" placeholder="Diniyah" value="<?= set_value('diniyah') ?>">
                                            <?= form_error('diniyah', '<small class="text-danger pl-3">', ' </small>') ?>
                                        </div>


                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <img src="" width="100" height="85" id="preview" class="img-thumbnail">
                                            </div>
                                            <div class="col-sm-9">
                                                <input hidden type="file" name="img_siswa" class="file" accept="image/*" id="imgInp">
                                                <div class="input-group my-3">
                                                    <input type="text" class="form-control" disabled placeholder="Foto siswa" id="file">
                                                    <div class="input-group-append">
                                                        <button type="button" class="browse btn btn-primary">Browse</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <img src="" width="100" height="85" id="preview1" class="img-thumbnail">
                                            </div>
                                            <div class="col-sm-9">
                                                <input hidden type="file" name="img_kk" class="file1" accept="image/*" id="imgInp1">
                                                <div class="input-group my-3">
                                                    <input type="text" class="form-control" disabled placeholder="Foto KK (Kartu keluarga)" id="file1">
                                                    <div class="input-group-append">
                                                        <button type="button" class="browse1 btn btn-primary">Browse</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <img src="" width="100" height="85" id="preview2" class="img-thumbnail">
                                            </div>
                                            <div class="col-sm-9">
                                                <input hidden type="file" name="img_ijazah" class="file2" accept="image/*" id="imgInp2">
                                                <div class="input-group my-3">
                                                    <input type="text" class="form-control" disabled placeholder="Foto Ijazah" id="file2">
                                                    <div class="input-group-append">
                                                        <button type="button" class="browse2 btn btn-primary">Browse</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <img src="" width="100" height="85" id="preview3" class="img-thumbnail">
                                            </div>
                                            <div class="col-sm-9">
                                                <input hidden type="file" name="img_ktp" class="file3" accept="image/*" id="imgInp3">
                                                <div class="input-group my-3">
                                                    <input type="text" class="form-control" disabled placeholder="Foto Akte / KTP" id="file3">
                                                    <div class="input-group-append">
                                                        <button type="button" class="browse3 btn btn-primary">Browse</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="pt-3 form-group row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn-block btn btn-success">Kirim Pendaftaran</button>
                                    </div>
                                    <div class="col-md-12 text-center mt-5">
                                        <p>Sudah mendaftar? <a href="<?= base_url('ppdb/login') ?>">Login</a> ke dashboard.</p>
                                    </div>
                                </div>

                                <?php form_close() ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.container-fluid -->


        </div>
    </section>

</main><!-- End #main -->

<script type="text/javascript">
    var input = document.getElementById('password'),
        icon = document.getElementById('icon');

    icon.onclick = function() {

        if (input.className == 'active form-control') {
            input.setAttribute('type', 'text');
            icon.className = 'bi bi-eye';
            input.className = 'form-control';

        } else {
            input.setAttribute('type', 'password');
            icon.className = 'bi bi-eye-slash';
            input.className = 'active form-control';
        }

    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#prov').change(function() {
            $.ajax({
                type: 'POST',
                url: '<?= site_url('get/get_kota'); ?>',
                data: {
                    prov: this.value
                },
                cache: false,
                success: function(response) {
                    $('#kab').html(response);
                }
            });
        });
    });
</script>


<script type="text/javascript">
    $(document).on("click", ".browse", function() {
        var file = $(this).parents().find(".file");
        file.trigger("click");
    });

    $(document).on("click", ".browse1", function() {
        var file = $(this).parents().find(".file1");
        file.trigger("click");
    });

    $(document).on("click", ".browse2", function() {
        var file = $(this).parents().find(".file2");
        file.trigger("click");
    });

    $(document).on("click", ".browse3", function() {
        var file = $(this).parents().find(".file3");
        file.trigger("click");
    });

    $('#imgInp').change(function(e) {
        var fileName = e.target.files[0].name;
        $("#file").val(fileName);

        var reader = new FileReader();
        reader.onload = function(e) {
            // get loaded data and render thumbnail.
            document.getElementById("preview").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    });

    $('#imgInp1').change(function(e) {
        var fileName = e.target.files[0].name;
        $("#file1").val(fileName);

        var reader = new FileReader();
        reader.onload = function(e) {
            // get loaded data and render thumbnail.
            document.getElementById("preview1").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    });

    $('#imgInp2').change(function(e) {
        var fileName = e.target.files[0].name;
        $("#file2").val(fileName);

        var reader = new FileReader();
        reader.onload = function(e) {
            // get loaded data and render thumbnail.
            document.getElementById("preview2").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    });

    $('#imgInp3').change(function(e) {
        var fileName = e.target.files[0].name;
        $("#file3").val(fileName);

        var reader = new FileReader();
        reader.onload = function(e) {
            // get loaded data and render thumbnail.
            document.getElementById("preview3").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    });
</script>