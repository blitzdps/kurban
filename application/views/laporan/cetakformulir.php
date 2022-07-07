<style type="text/css">
	html {
		font-size: 10px;
		-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
	}

	body {
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 12px;
		line-height: 1.42857143;
		color: #000;
		background-color: #fff;
	}

	table {
		border-spacing: 0;
		border-collapse: collapse
	}

	td,
	th {
		padding: 0;
	}

	.text-left {
		text-align: left;
	}

	.text-right {
		text-align: right;
	}

	.text-center {
		text-align: center;
	}

	.text-justify {
		text-align: justify;
	}

	.text-nowrap {
		white-space: nowrap;
	}

	.text-lowercase {
		text-transform: lowercase;
	}

	.text-uppercase {
		text-transform: uppercase;
	}

	.text-capitalize {
		text-transform: capitalize;
	}

	.pull-right {
		float: right !important;
	}

	.pull-left {
		float: left !important;
	}

	.kiri {
		padding-left: 4px;
	}

	.kanan {
		padding-right: 4px;
	}

	.btop {
		border-top: 1px solid black;
	}

	.bbottom {
		border-bottom: 1px solid black;
	}

	.bleft {
		border-left: 1px solid black;
	}

	.bright {
		border-right: 1px solid black;
	}

	.ball {
		border: 1px solid black;
	}
</style>
<p class="text-center">
	
	<span style="font-weight: bold; font-size: 22px">"<?= $web['nama'] ?>"</span>
	<br>
	<span style="font-size: 15px;">Alamat : <?= $web['alamat'] ?></span>
	<br>
	<span style="font-size: 15px;">No Telp : <?= $web['telp'] ?> E-mail : <?= $web['email'] ?></span>
	<br>
	<hr>
	<p class="text-center">
	<span style="font-weight: bold; font-size: 17px">FORMULIR PENDAFTARAN</span>
	<br>
	<span style="font-size: 12px">PESERTA DIDIK BARU/PINDAHAN TA. <?= date("Y"); ?>/<?= date("Y") + 1; ?></span>
	<br></p>
</p>
<br>
<table width="100%" style="font-size: 14px;">
	<tr>
<td>
<table width="70%" style="font-size: 14px;" cellspacing="2">
	<tr>
		<td width="20%">Nomor REG</td>
		<td width="3%">:</td>
		<td width="50%"><?= $ppdb['no_daftar'] ?></td>
	</tr>
	<tr>
		<td width="20%">Tanggal</td>
		<td width="3%">:</td>
		<td width="50%"><?= mediumdate_indo(date($ppdb['date_created'])); ?></td>
	</tr>
	<tr>
		<td>Sekolah Asal</td>
		<td>:</td>
		<td><?= strtoupper($ppdb['sekolah_asal']); ?></td>
	</tr>
		<tr>
		<td>NISN</td>
		<td>:</td>
		<td><?= $ppdb['nis'] ?></td>
	</tr>
</table>	
</tr>
</td>
<td>
    <!--<img src="<?= base_url('assets/img/data/' . $ppdb['img_siswa']); ?>" width="100" height="85">-->
</td>
</table>

<br>
    <p class="text-center">
	<span style="font-weight: bold; font-size: 12px">IDENTITAS PESERTA DIDIK</span>
	</p>
	    <br>
<table width="70%" style="font-size: 14px;" cellspacing="2">	    
	<tr>
	    <td align="">a. </td>
		<td>Nama Lengkap</td>
		<td>:</td>
		<td><?= strtoupper($ppdb['nama']); ?></td>
	</tr>
	<tr>
		<td align="">b. </td>
		<td>NIK</td>
		<td>:</td>
		<td><?= $ppdb['nik'] ?></td>
	</tr>
	<tr>
	    <?php if($ppdb['jk'] == 'L'){
	        $jenis_k = 'Laki - Laki';
	    }elseif($ppdb['jk'] == 'P'){
	        $jenis_k = 'Perempuan';
	    }?>
		<td align="">c. </td>
		<td>Jenis Kelamin</td>
		<td>:</td>
		<td><?= $jenis_k ?></td>
	</tr>
	<tr>
		<td align="">d. </td>
		<td>Tempat Lahir</td>
		<td>:</td>
		<td><?= $ppdb['kab'] ?></td>
	</tr>
	<tr>
		<td align="">e. </td>
		<td>Tanggal Lahir</td>
		<td>:</td>
		<td><?= mediumdate_indo(date($ppdb['ttl'])) ?></td>
	</tr>
	<tr>
		<td align="">f. </td>
		<td>Alamat</td>
		<td>:</td>
		<td><?= $ppdb['alamat'] ?></td>
	</tr>
	<tr>
		<td align="">g. </td>
		<td>Email</td>
		<td>:</td>
		<td><?= $ppdb['email'] ?></td>
	</tr>
	<tr>
		<td align="">h. </td>
		<td>No HP</td>
		<td>:</td>
		<td><?= $ppdb['no_hp'] ?></td>
	</tr>
</table>
   <p class="text-center">
	<span style="font-weight: bold; font-size: 12px">DATA ORANG TUA</span>
	</p>
	<br>
<table width="70%" style="font-size: 14px;" cellspacing="2">
	<tr>
		<td align="">i. </td>
		<td>Nama Ayah</td>
		<td>:</td>
		<td><?= $ppdb['nama_ayah'] ?></td>
	</tr>
	<tr>
		<td align="">j. </td>
		<td>Nama Ibu</td>
		<td>:</td>
		<td><?= $ppdb['nama_ibu'] ?></td>
	</tr>
	<tr>
		<td align="">k. </td>
		<td>Penghasilan Ortu</td>
		<td>:</td>
		<td><?= $ppdb['peng_ortu'] ?></td>
	</tr>
</table>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<table width="100%" style="font-size: 14px;">
	<tr>
		<td width="50%" align=""><span style="font-weight: bold; font-size: 9px">*Bertanggung jawab secara hukum terhadap kebenaran data yang tercantum</span></td>
		<td width="50%" align="center"><span style="font-weight: bold; font-size: 12px"><?= longdate_indo(date(date('Y-m-d'))); ?></span></td>
	</tr>
	<tr>
		<td width="50%" align="center">Orang Tua/Wali atau Siswa <br><br><br><br><br>( ................................... )</td>
		<td width="50%" align="center">Panitia PPDB <?= date("Y"); ?>/<?= date("Y") + 1; ?><br><br><br><br><br>( ................................... )</td>
	</tr>
</table>