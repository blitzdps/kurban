-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2022 at 10:14 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kurban`
--

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

CREATE TABLE `about` (
  `id` int(11) NOT NULL,
  `about` text NOT NULL,
  `visi` text NOT NULL,
  `misi` text NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`id`, `about`, `visi`, `misi`, `img`) VALUES
(1, '<p>Flobamora Technology</p><p>Sebuah tim pengembangan web dan android yang didirikan di Kota Kupang pada bulan Maret 2022. Tim ini didirkan oleh 3 orang yaitu Dimas Sedy , Ammar Jannah dan Ardean Raflian.</p>', 'Membuat sistem yang dapat membantu perkembangan teknologi di masyarakat.', '1. Membantu mengkaselerasi teknologi dalam kehidupan masyarakat.\r\n<p>\r\n2. Mensosialisasikan teknologi kepada masyarakat.', 'RQ4.png');

-- --------------------------------------------------------

--
-- Table structure for table `absen`
--

CREATE TABLE `absen` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `waktu` time NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `status` enum('Belum Absen','Hadir','Tidak Hadir','Izin') NOT NULL,
  `role_absen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `absen_pegawai`
--

CREATE TABLE `absen_pegawai` (
  `id` int(11) NOT NULL,
  `id_peng` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `sum_jam` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `role_absen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `acara`
--

CREATE TABLE `acara` (
  `id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `id_kat` int(11) NOT NULL,
  `img` varchar(250) NOT NULL,
  `tempat` varchar(255) NOT NULL,
  `jam` varchar(250) NOT NULL,
  `tgl` date NOT NULL,
  `id_peng` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `acara`
--

INSERT INTO `acara` (`id`, `judul`, `deskripsi`, `id_kat`, `img`, `tempat`, `jam`, `tgl`, `id_peng`) VALUES
(8, 'Kegiatan Pemotongan Hewan Kurban', '<p>Kegiatan Pemotongan hewan kurban akan dilaksanakan mulai jam 09:00. Diharapkan agar warga bisa ikut membantu kegiatan tersebut di masjid.</p><p>&nbsp;</p><p>Terima Kasih</p>', 1, '1595609564.jpg', 'Masjid Al-Anshar', '09:00', '2022-07-10', 1),
(9, 'Sosialisasi sistem pembagian daging kurban', '<p>Kegiatan sosialisasi akan dilaksanakan di kediamaran rumah Pak Edy</p>', 2, 'Sosialisasi.jpg', 'Rumah Pak Edy', '20:00', '2022-07-09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `balas_konseling`
--

CREATE TABLE `balas_konseling` (
  `id` int(11) NOT NULL,
  `pengirim` enum('Karyawan','siswa') NOT NULL,
  `id_peng` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `balasan` text NOT NULL,
  `tgl` date NOT NULL,
  `waktu` time NOT NULL,
  `role_konseling` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `daftar_absen`
--

CREATE TABLE `daftar_absen` (
  `id` int(11) NOT NULL,
  `id_pend` int(11) NOT NULL,
  `id_kelas` varchar(250) NOT NULL,
  `tgl` date NOT NULL,
  `status` enum('Selesai','Belum Selesai') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `daftar_absen`
--

INSERT INTO `daftar_absen` (`id`, `id_pend`, `id_kelas`, `tgl`, `status`) VALUES
(1, 3, '11', '2022-01-10', 'Belum Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `data_absen_pegawai`
--

CREATE TABLE `data_absen_pegawai` (
  `id` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `data_cicilan`
--

CREATE TABLE `data_cicilan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `id_peng` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `tenor` int(11) NOT NULL,
  `status` enum('Belum Lunas','Lunas') NOT NULL,
  `tgl_lunas` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data_cicilan`
--

INSERT INTO `data_cicilan` (`id`, `nama`, `id_peng`, `nominal`, `tenor`, `status`, `tgl_lunas`) VALUES
(1, 'Cicilan 1', 2, 12000, -1, 'Lunas', '2021-07-26'),
(2, 'Cicilan 2', 2, 15000, 9, 'Belum Lunas', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `data_divisi`
--

CREATE TABLE `data_divisi` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `gaji` int(11) NOT NULL,
  `tunjangan` int(11) NOT NULL,
  `role_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data_divisi`
--

INSERT INTO `data_divisi` (`id`, `nama`, `gaji`, `tunjangan`, `role_id`) VALUES
(1, 'Guru', 150000, 200000, 2),
(17, 'Karyawan', 100000, 100000, 3),
(18, 'Demo', 150000, 150000, 3);

-- --------------------------------------------------------

--
-- Table structure for table `data_kelas`
--

CREATE TABLE `data_kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `id_pend` int(11) NOT NULL,
  `id_peng` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_kelas`
--

INSERT INTO `data_kelas` (`id`, `nama`, `id_pend`, `id_peng`) VALUES
(1, 'Hmb 01', 1, 2),
(2, 'Hmb 02', 1, 1),
(3, 'Hmb 03', 1, 1),
(4, 'Hmb 04', 1, 1),
(5, 'Hmb 05', 1, 1),
(6, 'Hmb 06', 1, 1),
(7, 'Hmb 07', 1, 1),
(8, 'Hmb 08', 1, 1),
(10, 'Hmb 10', 2, 2),
(11, 'IX A', 3, 2),
(13, 'IX A', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `data_kursi`
--

CREATE TABLE `data_kursi` (
  `id` int(11) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `tipe` enum('Kursi A','Kursi B') NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_kursi`
--

INSERT INTO `data_kursi` (`id`, `nama`, `tipe`, `id_kelas`, `id_siswa`) VALUES
(43, 'B1', 'Kursi B', 1, 0),
(44, 'B2', 'Kursi B', 1, 0),
(45, 'A3', 'Kursi A', 1, 0),
(46, 'A2', 'Kursi A', 2, 0),
(47, 'A1', 'Kursi A', 2, 0),
(48, 'A2', 'Kursi A', 3, 0),
(49, 'A3', 'Kursi A', 1, 0),
(50, 'A4', 'Kursi A', 1, 0),
(51, 'A5', 'Kursi A', 1, 0),
(52, 'A6', 'Kursi A', 1, 0),
(53, 'A7', 'Kursi A', 1, 0),
(54, 'A8', 'Kursi A', 1, 0),
(55, 'A9', 'Kursi A', 1, 0),
(56, 'A10', 'Kursi A', 1, 0),
(57, 'A11', 'Kursi A', 1, 0),
(58, 'A12', 'Kursi A', 1, 0),
(59, 'A13', 'Kursi A', 1, 0),
(60, 'A14', 'Kursi A', 1, 0),
(62, 'B16', 'Kursi B', 1, 0),
(63, 'B17', 'Kursi B', 1, 0),
(64, 'B18', 'Kursi B', 1, 0),
(65, 'B19', 'Kursi B', 1, 0),
(66, 'B20', 'Kursi B', 1, 0),
(67, 'B21', 'Kursi B', 1, 0),
(68, 'B22', 'Kursi B', 1, 0),
(69, 'B23', 'Kursi B', 1, 0),
(70, 'B24', 'Kursi B', 1, 0),
(71, 'B25', 'Kursi B', 1, 0),
(74, 'A2', 'Kursi A', 4, 0),
(75, 'A1', 'Kursi A', 4, 0),
(77, 'B1', 'Kursi B', 2, 0),
(79, 'A1', 'Kursi A', 10, 0),
(81, 'B2', 'Kursi B', 2, 0),
(82, 'AA1', 'Kursi A', 1, 0),
(83, 'B3', 'Kursi B', 10, 0),
(84, 'B4', 'Kursi B', 10, 0),
(85, 'Ax2', 'Kursi A', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `data_pelanggaran`
--

CREATE TABLE `data_pelanggaran` (
  `id` int(11) NOT NULL,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `point` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_pelanggaran`
--

INSERT INTO `data_pelanggaran` (`id`, `kode`, `nama`, `point`, `jumlah`) VALUES
(1, 'DN', 'TIDAK DINIYYAH', 1, 1),
(2, 'TK', 'TIDAK TAKROR', 2, 0),
(3, 'SK', 'TIDAK SEKOLAH', 3, 1),
(4, 'PTI', 'PULANG TIDAK IZIN', 4, 1),
(5, 'MRK', 'MEROKOK\r\n', 0, 0),
(6, 'KLR', 'KELUAR PADA JAM KEGIATAN', 0, 0),
(7, 'W', 'WARNET', 0, 0),
(8, 'PS', 'PLAYSTATION', 0, 0),
(9, 'WTN', 'Tidak Wethon 2', 10, 0),
(10, 'SPD', 'MENYEWA SEPEDA', 0, 0),
(11, 'GD', 'GANDOL', 0, 0),
(12, 'P12', 'Nyekher', 0, 0),
(13, 'P13', 'Kembali pondok Terlambat', 0, 0),
(14, 'P14', 'Domisili 2 Tempat', 0, 0),
(15, 'AB', 'Absensi Maljum', 0, 0),
(16, 'P16', 'Tdak Taftisan', 0, 0),
(17, 'P17', 'Mencuri', 0, 0),
(18, 'P18', 'Loncat Pager', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `data_pendidikan`
--

CREATE TABLE `data_pendidikan` (
  `id` int(11) NOT NULL,
  `nama` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_pendidikan`
--

INSERT INTO `data_pendidikan` (`id`, `nama`) VALUES
(1, 'SD'),
(2, 'SMPIT'),
(3, 'MTs');

-- --------------------------------------------------------

--
-- Table structure for table `data_perizinan`
--

CREATE TABLE `data_perizinan` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_perizinan`
--

INSERT INTO `data_perizinan` (`id`, `nama`, `jumlah`) VALUES
(1, 'Pulang', 0),
(2, 'Ke pasar', 1),
(9, 'Sakit', 1),
(10, 'Acara', 1);

-- --------------------------------------------------------

--
-- Table structure for table `email_sender`
--

CREATE TABLE `email_sender` (
  `id` int(11) NOT NULL,
  `protocol` varchar(250) NOT NULL,
  `host` varchar(250) NOT NULL,
  `port` int(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `charset` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_sender`
--

INSERT INTO `email_sender` (`id`, `protocol`, `host`, `port`, `email`, `password`, `charset`) VALUES
(1, 'smtp', 'mail@roudhatulquran-manggungjaya.sch.id', 465, 'admin@roudhatulquran-manggungjaya.sch.id', 'admin123Aa@', 'iso-8859-1');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `id_kat` int(11) NOT NULL,
  `img` varchar(250) NOT NULL,
  `img1` varchar(250) NOT NULL,
  `img2` varchar(250) NOT NULL,
  `img3` varchar(250) NOT NULL,
  `id_peng` int(11) NOT NULL,
  `tgl` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `nama`, `deskripsi`, `id_kat`, `img`, `img1`, `img2`, `img3`, `id_peng`, `tgl`) VALUES
(14, 'Walikota Kupang', '<p>Event dengan Walikota Kupang</p>', 2, 'WhatsApp_Image_2022-07-09_at_15_55_07_(1).jpeg', '', '', '', 1, '2021-07-19'),
(15, 'Kepala Dinas PPO Kota Kupang', '<p>Pertemuan dengan Kepala Dinas PPO Kota Kupang untuk presentasi sistem smaart school</p>', 1, 'WhatsApp_Image_2022-07-09_at_15_55_07.jpeg', '', '', '', 1, '2021-07-19');

-- --------------------------------------------------------

--
-- Table structure for table `home`
--

CREATE TABLE `home` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` varchar(255) NOT NULL,
  `tombol` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `img` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `home`
--

INSERT INTO `home` (`id`, `judul`, `isi`, `tombol`, `link`, `img`) VALUES
(1, 'Pendaftaran Jamaah Idul Adha 1433 H', 'Pengisian data jamaah untuk penerimaan kurban', 'Daftar Sekarang', 'ppdb', 'hero-img.png');

-- --------------------------------------------------------

--
-- Table structure for table `kabupaten`
--

CREATE TABLE `kabupaten` (
  `id_kab` int(11) NOT NULL,
  `id_prov` int(11) NOT NULL,
  `nama_kab` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kabupaten`
--

INSERT INTO `kabupaten` (`id_kab`, `id_prov`, `nama_kab`) VALUES
(1101, 11, 'Aceh Selatan'),
(1102, 11, 'Aceh Tenggara'),
(1103, 11, 'Aceh Timur'),
(1104, 11, 'Aceh Tengah'),
(1105, 11, 'Aceh Barat'),
(1106, 11, 'Aceh Besar'),
(1107, 11, 'Pidie'),
(1108, 11, 'Aceh Utara'),
(1109, 11, 'Simeulue'),
(1110, 11, 'Aceh Singkil'),
(1111, 11, 'Bireuen'),
(1112, 11, 'Aceh Barat Daya'),
(1113, 11, 'Gayo Lues'),
(1114, 11, 'Aceh Jaya'),
(1115, 11, 'Nagan Raya'),
(1116, 11, 'Aceh Tamiang'),
(1117, 11, 'Bener Meriah'),
(1118, 11, 'Pidie Jaya'),
(1171, 11, 'Banda Aceh'),
(1172, 11, 'Sabang'),
(1173, 11, 'Lhokseumawe'),
(1174, 11, 'Langsa'),
(1175, 11, 'Subulussalam'),
(1201, 12, 'Tapanuli Tengah'),
(1202, 12, 'Tapanuli Utara'),
(1203, 12, 'Tapanuli Selatan'),
(1204, 12, 'Nias'),
(1205, 12, 'Langkat'),
(1206, 12, 'Karo'),
(1207, 12, 'Deli Serdang'),
(1208, 12, 'Simalungun'),
(1209, 12, 'Asahan'),
(1210, 12, 'Labuhanbatu'),
(1211, 12, 'Dairi'),
(1212, 12, 'Toba Samosir'),
(1213, 12, 'Mandailing Natal'),
(1214, 12, 'Nias Selatan'),
(1215, 12, 'Pakpak Bharat'),
(1216, 12, 'Humbang Hasundutan'),
(1217, 12, 'Samosir'),
(1218, 12, 'Serdang Bedagai'),
(1219, 12, 'Batu Bara'),
(1220, 12, 'Padang Lawas Utara'),
(1221, 12, 'Padang Lawas'),
(1222, 12, 'Labuhanbatu Selatan'),
(1223, 12, 'Labuhanbatu Utara'),
(1224, 12, 'Nias Utara'),
(1225, 12, 'Nias Barat'),
(1271, 12, 'Medan'),
(1272, 12, 'Pematang Siantar'),
(1273, 12, 'Sibolga'),
(1274, 12, 'Tanjung Balai'),
(1275, 12, 'Binjai'),
(1276, 12, 'Tebing Tinggi'),
(1277, 12, 'Padangsidimpuan'),
(1278, 12, 'Gunungsitoli'),
(1301, 13, 'Pesisir Selatan'),
(1302, 13, 'Solok'),
(1303, 13, 'Sijunjung'),
(1304, 13, 'Tanah Datar'),
(1305, 13, 'Padang Pariaman'),
(1306, 13, 'Agam'),
(1307, 13, 'Lima Puluh Kota'),
(1308, 13, 'Pasaman'),
(1309, 13, 'Kepulauan Mentawai'),
(1310, 13, 'Dharmasraya'),
(1311, 13, 'Solok Selatan'),
(1312, 13, 'Pasaman Barat'),
(1371, 13, 'Padang'),
(1373, 13, 'Sawahlunto'),
(1374, 13, 'Padang Panjang'),
(1375, 13, 'Bukittinggi'),
(1376, 13, 'Payakumbuh'),
(1377, 13, 'Pariaman'),
(1401, 14, 'Kampar'),
(1402, 14, 'Indragiri Hulu'),
(1403, 14, 'Bengkalis'),
(1404, 14, 'Indragiri Hilir'),
(1405, 14, 'Pelalawan'),
(1406, 14, 'Rokan Hulu'),
(1407, 14, 'Rokan Hilir'),
(1408, 14, 'Siak'),
(1409, 14, 'Kuantan Singingi'),
(1410, 14, 'Kepulauan Meranti'),
(1471, 14, 'Pekanbaru'),
(1472, 14, 'Dumai'),
(1501, 15, 'Kerinci'),
(1502, 15, 'Merangin'),
(1503, 15, 'Sarolangun'),
(1504, 15, 'Batanghari'),
(1505, 15, 'Muaro Jambi'),
(1506, 15, 'Tanjung Jabung Barat'),
(1507, 15, 'Tanjung Jabung Timur'),
(1508, 15, 'Bungo'),
(1509, 15, 'Tebo'),
(1571, 15, 'Jambi'),
(1572, 15, 'Sungai Penuh'),
(1601, 16, 'Ogan Komering Ulu'),
(1602, 16, 'Ogan Komering Ilir'),
(1603, 16, 'Muara Enim'),
(1604, 16, 'Lahat'),
(1605, 16, 'Musi Rawas'),
(1606, 16, 'Musi Banyuasin'),
(1607, 16, 'Banyuasin'),
(1608, 16, 'Ogan Komering Ulu Timur'),
(1609, 16, 'Ogan Komering Ulu Selatan'),
(1610, 16, 'Ogan Ilir'),
(1611, 16, 'Empat Lawang'),
(1612, 16, 'Penukal Abab Lematang Ilir'),
(1613, 16, 'Musi Rawas Utara'),
(1671, 16, 'Palembang'),
(1672, 16, 'Pagar Alam'),
(1673, 16, 'Lubuk Linggau'),
(1674, 16, 'Prabumulih'),
(1701, 17, 'Bengkulu Selatan'),
(1702, 17, 'Rejang Lebong'),
(1703, 17, 'Bengkulu Utara'),
(1704, 17, 'Kaur'),
(1705, 17, 'Seluma'),
(1706, 17, 'Muko Muko'),
(1707, 17, 'Lebong'),
(1708, 17, 'Kepahiang'),
(1709, 17, 'Bengkulu Tengah'),
(1771, 17, 'Bengkulu'),
(1801, 18, 'Lampung Selatan'),
(1802, 18, 'Lampung Tengah'),
(1803, 18, 'Lampung Utara'),
(1804, 18, 'Lampung Barat'),
(1805, 18, 'Tulang Bawang'),
(1806, 18, 'Tanggamus'),
(1807, 18, 'Lampung Timur'),
(1808, 18, 'Way Kanan'),
(1809, 18, 'Pesawaran'),
(1810, 18, 'Pringsewu'),
(1811, 18, 'Mesuji'),
(1812, 18, 'Tulang Bawang Barat'),
(1813, 18, 'Pesisir Barat'),
(1871, 18, 'Bandar Lampung'),
(1872, 18, 'Metro'),
(1901, 19, 'Bangka'),
(1902, 19, 'Belitung'),
(1903, 19, 'Bangka Selatan'),
(1904, 19, 'Bangka Tengah'),
(1905, 19, 'Bangka Barat'),
(1906, 19, 'Belitung Timur'),
(1971, 19, 'Pangkal Pinang'),
(2101, 21, 'Bintan'),
(2102, 21, 'Karimun'),
(2103, 21, 'Natuna'),
(2104, 21, 'Lingga'),
(2105, 21, 'Kepulauan Anambas'),
(2171, 21, 'Batam'),
(2172, 21, 'Tanjung Pinang'),
(3101, 31, 'Adm. Kep. Seribu'),
(3171, 31, 'Adm. Jakarta Pusat'),
(3172, 31, 'Adm. Jakarta Utara'),
(3173, 31, 'Adm. Jakarta Barat'),
(3174, 31, 'Adm. Jakarta Selatan'),
(3175, 31, 'Adm. Jakarta Timur'),
(3201, 32, 'Bogor'),
(3202, 32, 'Sukabumi'),
(3203, 32, 'Cianjur'),
(3204, 32, 'Bandung'),
(3205, 32, 'Garut'),
(3206, 32, 'Tasikmalaya'),
(3207, 32, 'Ciamis'),
(3208, 32, 'Kuningan'),
(3209, 32, 'Cirebon'),
(3210, 32, 'Majalengka'),
(3211, 32, 'Sumedang'),
(3212, 32, 'Indramayu'),
(3213, 32, 'Subang'),
(3214, 32, 'Purwakarta'),
(3215, 32, 'Karawang'),
(3216, 32, 'Bekasi'),
(3217, 32, 'Bandung Barat'),
(3218, 32, 'Pangandaran'),
(3276, 32, 'Depok'),
(3277, 32, 'Cimahi'),
(3279, 32, 'Banjar'),
(3301, 33, 'Cilacap'),
(3302, 33, 'Banyumas'),
(3303, 33, 'Purbalingga'),
(3304, 33, 'Banjarnegara'),
(3305, 33, 'Kebumen'),
(3306, 33, 'Purworejo'),
(3307, 33, 'Wonosobo'),
(3308, 33, 'Magelang'),
(3309, 33, 'Boyolali'),
(3310, 33, 'Klaten'),
(3311, 33, 'Sukoharjo'),
(3312, 33, 'Wonogiri'),
(3313, 33, 'Karanganyar'),
(3314, 33, 'Sragen'),
(3315, 33, 'Grobogan'),
(3316, 33, 'Blora'),
(3317, 33, 'Rembang'),
(3318, 33, 'Pati'),
(3319, 33, 'Kudus'),
(3320, 33, 'Jepara'),
(3321, 33, 'Demak'),
(3322, 33, 'Semarang'),
(3323, 33, 'Temanggung'),
(3324, 33, 'Kendal'),
(3325, 33, 'Batang'),
(3326, 33, 'Pekalongan'),
(3327, 33, 'Pemalang'),
(3328, 33, 'Tegal'),
(3329, 33, 'Brebes'),
(3372, 33, 'Surakarta'),
(3373, 33, 'Salatiga'),
(3401, 34, 'Kulon Progo'),
(3402, 34, 'Bantul'),
(3403, 34, 'Gunung Kidul'),
(3404, 34, 'Sleman'),
(3471, 34, 'Yogyakarta'),
(3501, 35, 'Pacitan'),
(3502, 35, 'Ponorogo'),
(3503, 35, 'Trenggalek'),
(3504, 35, 'Tulungagung'),
(3505, 35, 'Blitar'),
(3506, 35, 'Kediri'),
(3507, 35, 'Malang'),
(3508, 35, 'Lumajang'),
(3509, 35, 'Jember'),
(3510, 35, 'Banyuwangi'),
(3511, 35, 'Bondowoso'),
(3512, 35, 'Situbondo'),
(3513, 35, 'Probolinggo'),
(3514, 35, 'Pasuruan'),
(3515, 35, 'Sidoarjo'),
(3516, 35, 'Mojokerto'),
(3517, 35, 'Jombang'),
(3518, 35, 'Nganjuk'),
(3519, 35, 'Madiun'),
(3520, 35, 'Magetan'),
(3521, 35, 'Ngawi'),
(3522, 35, 'Bojonegoro'),
(3523, 35, 'Tuban'),
(3524, 35, 'Lamongan'),
(3525, 35, 'Gresik'),
(3526, 35, 'Bangkalan'),
(3527, 35, 'Sampang'),
(3528, 35, 'Pamekasan'),
(3529, 35, 'Sumenep'),
(3578, 35, 'Surabaya'),
(3579, 35, 'Batu'),
(3601, 36, 'Pandeglang'),
(3602, 36, 'Lebak'),
(3603, 36, 'Tangerang'),
(3604, 36, 'Serang'),
(3672, 36, 'Cilegon'),
(3674, 36, 'Tangerang Selatan'),
(5101, 51, 'Jembrana'),
(5102, 51, 'Tabanan'),
(5103, 51, 'Badung'),
(5104, 51, 'Gianyar'),
(5105, 51, 'Klungkung'),
(5106, 51, 'Bangli'),
(5107, 51, 'Karangasem'),
(5108, 51, 'Buleleng'),
(5171, 51, 'Denpasar'),
(5201, 52, 'Lombok Barat'),
(5202, 52, 'Lombok Tengah'),
(5203, 52, 'Lombok Timur'),
(5204, 52, 'Sumbawa'),
(5205, 52, 'Dompu'),
(5206, 52, 'Bima'),
(5207, 52, 'Sumbawa Barat'),
(5208, 52, 'Lombok Utara'),
(5271, 52, 'Mataram'),
(5301, 53, 'Kupang'),
(5302, 53, 'Timor Tengah Selatan'),
(5303, 53, 'Timor Tengah Utara'),
(5304, 53, 'Belu'),
(5305, 53, 'Alor'),
(5306, 53, 'Flores Timur'),
(5307, 53, 'Sikka'),
(5308, 53, 'Ende'),
(5309, 53, 'Ngada'),
(5310, 53, 'Manggarai'),
(5311, 53, 'Sumba Timur'),
(5312, 53, 'Sumba Barat'),
(5313, 53, 'Lembata'),
(5314, 53, 'Rote Ndao'),
(5315, 53, 'Manggarai Barat'),
(5316, 53, 'Nagekeo'),
(5317, 53, 'Sumba Tengah'),
(5318, 53, 'Sumba Barat Daya'),
(5319, 53, 'Manggarai Timur'),
(5320, 53, 'Sabu Raijua'),
(5321, 53, 'Malaka'),
(6101, 61, 'Sambas'),
(6102, 61, 'Mempawah'),
(6103, 61, 'Sanggau'),
(6104, 61, 'Ketapang'),
(6105, 61, 'Sintang'),
(6106, 61, 'Kapuas Hulu'),
(6107, 61, 'Bengkayang'),
(6108, 61, 'Landak'),
(6109, 61, 'Sekadau'),
(6110, 61, 'Melawi'),
(6111, 61, 'Kayong Utara'),
(6112, 61, 'Kubu Raya'),
(6171, 61, 'Pontianak'),
(6172, 61, 'Singkawang'),
(6201, 62, 'Kotawaringin Barat'),
(6202, 62, 'Kotawaringin Timur'),
(6203, 62, 'Kapuas'),
(6204, 62, 'Barito Selatan'),
(6205, 62, 'Barito Utara'),
(6206, 62, 'Katingan'),
(6207, 62, 'Seruyan'),
(6208, 62, 'Sukelasa'),
(6209, 62, 'Lamandau'),
(6210, 62, 'Gunung Mas'),
(6211, 62, 'Pulang Pisau'),
(6212, 62, 'Murung Raya'),
(6213, 62, 'Barito Timur'),
(6271, 62, 'Palangkaraya'),
(6301, 63, 'Tanah Laut'),
(6302, 63, 'Kotabaru'),
(6303, 63, 'Banjar'),
(6304, 63, 'Barito Kuala'),
(6305, 63, 'Tapin'),
(6306, 63, 'Hulu Sungai Selatan'),
(6307, 63, 'Hulu Sungai Tengah'),
(6308, 63, 'Hulu Sungai Utara'),
(6309, 63, 'Tabalong'),
(6310, 63, 'Tanah Bumbu'),
(6311, 63, 'Balangan'),
(6371, 63, 'Banjarmasin'),
(6372, 63, 'Banjarbaru'),
(6401, 64, 'Paser'),
(6402, 64, 'Kutai Kartanegara'),
(6403, 64, 'Berau'),
(6407, 64, 'Kutai Barat'),
(6408, 64, 'Kutai Timur'),
(6409, 64, 'Penajam Paser Utara'),
(6411, 64, 'Mahakam Ulu'),
(6471, 64, 'Balikpapan'),
(6472, 64, 'Samarinda'),
(6474, 64, 'Bontang'),
(6501, 65, 'Bulungan'),
(6502, 65, 'Malinau'),
(6503, 65, 'Nunukan'),
(6504, 65, 'Tana Tidung'),
(6571, 65, 'Tarakan'),
(7101, 71, 'Bolaang Mongondow'),
(7102, 71, 'Minahasa'),
(7103, 71, 'Kepulauan Sangihe'),
(7104, 71, 'Kepulauan Talaud'),
(7105, 71, 'Minahasa Selatan'),
(7106, 71, 'Minahasa Utara'),
(7107, 71, 'Minahasa Tenggara'),
(7108, 71, 'Bolaang Mongondow Utara'),
(7109, 71, 'Kep. Siau Tagulandang Biaro'),
(7110, 71, 'Bolaang Mongondow Timur'),
(7111, 71, 'Bolaang Mongondow Selatan'),
(7171, 71, 'Manado'),
(7172, 71, 'Bitung'),
(7173, 71, 'Tomohon'),
(7174, 71, 'Kotamobagu'),
(7201, 72, 'Banggai'),
(7202, 72, 'Poso'),
(7203, 72, 'Donggala'),
(7204, 72, 'Toli Toli'),
(7205, 72, 'Buol'),
(7206, 72, 'Morowali'),
(7207, 72, 'Banggai Kepulauan'),
(7208, 72, 'Parigi Moutong'),
(7209, 72, 'Tojo Una Una'),
(7210, 72, 'Sigi'),
(7211, 72, 'Banggai Laut'),
(7212, 72, 'Morowali Utara'),
(7271, 72, 'Palu'),
(7301, 73, 'Kepulauan Selayar'),
(7302, 73, 'Bulukumba'),
(7303, 73, 'Bantaeng'),
(7304, 73, 'Jeneponto'),
(7305, 73, 'Takalar'),
(7306, 73, 'Gowa'),
(7307, 73, 'Sinjai'),
(7308, 73, 'Bone'),
(7309, 73, 'Maros'),
(7310, 73, 'Pangkajene Kepulauan'),
(7311, 73, 'Barru'),
(7312, 73, 'Soppeng'),
(7313, 73, 'Wajo'),
(7314, 73, 'Sidenreng Rappang'),
(7315, 73, 'Pinrang'),
(7316, 73, 'Enrekang'),
(7317, 73, 'Luwu'),
(7318, 73, 'Tana Toraja'),
(7322, 73, 'Luwu Utara'),
(7324, 73, 'Luwu Timur'),
(7326, 73, 'Toraja Utara'),
(7371, 73, 'Makassar'),
(7372, 73, 'Pare Pare'),
(7373, 73, 'Palopo'),
(7401, 74, 'Kolaka'),
(7402, 74, 'Konawe'),
(7403, 74, 'Muna'),
(7404, 74, 'Buton'),
(7405, 74, 'Konawe Selatan'),
(7406, 74, 'Bombana'),
(7407, 74, 'Wakatobi'),
(7408, 74, 'Kolaka Utara'),
(7409, 74, 'Konawe Utara'),
(7410, 74, 'Buton Utara'),
(7411, 74, 'Kolaka Timur'),
(7412, 74, 'Konawe Kepulauan'),
(7413, 74, 'Muna Barat'),
(7414, 74, 'Buton Tengah'),
(7415, 74, 'Buton Selatan'),
(7471, 74, 'Kendari'),
(7472, 74, 'Bau Bau'),
(7501, 75, 'Gorontalo'),
(7502, 75, 'Boalemo'),
(7503, 75, 'Bone Bolango'),
(7504, 75, 'Pahuwato'),
(7505, 75, 'Gorontalo Utara'),
(7601, 76, 'Mamuju Utara'),
(7602, 76, 'Mamuju'),
(7603, 76, 'Mamasa'),
(7604, 76, 'Polewali Mandar'),
(7605, 76, 'Majene'),
(7606, 76, 'Mamuju Tengah'),
(8101, 81, 'Maluku Tengah'),
(8102, 81, 'Maluku Tenggara'),
(8103, 81, 'Maluku Tenggara Barat'),
(8104, 81, 'Buru'),
(8105, 81, 'Seram Bagian Timur'),
(8106, 81, 'Seram Bagian Barat'),
(8107, 81, 'Kepulauan Aru'),
(8108, 81, 'Maluku Barat Daya'),
(8109, 81, 'Buru Selatan'),
(8171, 81, 'Ambon'),
(8172, 81, 'Tual'),
(8201, 82, 'Halmahera Barat'),
(8202, 82, 'Halmahera Tengah'),
(8203, 82, 'Halmahera Utara'),
(8204, 82, 'Halmahera Selatan'),
(8205, 82, 'Kepulauan Sula'),
(8206, 82, 'Halmahera Timur'),
(8207, 82, 'Pulau Morotai'),
(8208, 82, 'Pulau Taliabu'),
(8271, 82, 'Ternate'),
(8272, 82, 'Tidore Kepulauan'),
(9101, 91, 'Merauke'),
(9102, 91, 'Jayawijaya'),
(9103, 91, 'Jayapura'),
(9104, 91, 'Nabire'),
(9105, 91, 'Kepulauan Yapen'),
(9106, 91, 'Biak Numfor'),
(9107, 91, 'Puncak Jaya'),
(9108, 91, 'Paniai'),
(9109, 91, 'Mimika'),
(9110, 91, 'Sarmi'),
(9111, 91, 'Keerom'),
(9112, 91, 'Pegunungan Bintang'),
(9113, 91, 'Yahukimo'),
(9114, 91, 'Tolikara'),
(9115, 91, 'Waropen'),
(9116, 91, 'Boven Digoel'),
(9117, 91, 'Mappi'),
(9118, 91, 'Asmat'),
(9119, 91, 'Supiori'),
(9120, 91, 'Mamberamo Raya'),
(9121, 91, 'Mamberamo Tengah'),
(9122, 91, 'Yalimo'),
(9123, 91, 'Lanny Jaya'),
(9124, 91, 'Nduga'),
(9125, 91, 'Puncak'),
(9126, 91, 'Dogiyai'),
(9127, 91, 'Intan Jaya'),
(9128, 91, 'Deiyai'),
(9201, 92, 'Sorong'),
(9202, 92, 'Manokwari'),
(9203, 92, 'Fak Fak'),
(9204, 92, 'Sorong Selatan'),
(9205, 92, 'Raja Ampat'),
(9206, 92, 'Teluk Bintuni'),
(9207, 92, 'Teluk Wondama'),
(9208, 92, 'Kaimana'),
(9209, 92, 'Tambrauw'),
(9210, 92, 'Maybrat'),
(9211, 92, 'Manokwari Selatan'),
(9212, 92, 'Pegunungan Arfak');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `id_fingerprint` varchar(250) NOT NULL,
  `nik` int(50) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `jk` enum('L','P') NOT NULL,
  `ttl` date NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) NOT NULL,
  `telp` varchar(13) NOT NULL,
  `id_divisi` int(11) NOT NULL,
  `dept` varchar(250) NOT NULL,
  `intensif` int(11) NOT NULL,
  `jam_mengajar` int(11) NOT NULL,
  `nominal_jam` int(11) NOT NULL,
  `bpjs` int(11) NOT NULL,
  `koperasi` int(11) NOT NULL,
  `simpanan` int(11) NOT NULL,
  `tabungan` int(11) NOT NULL,
  `id_pend` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `id_fingerprint`, `nik`, `nama`, `jk`, `ttl`, `email`, `password`, `alamat`, `telp`, `id_divisi`, `dept`, `intensif`, `jam_mengajar`, `nominal_jam`, `bpjs`, `koperasi`, `simpanan`, `tabungan`, `id_pend`, `id_kelas`, `role_id`, `status`, `date_created`) VALUES
(1, '1', 0, 'Superadmin', 'L', '0000-00-00', 'admin@gmail.com', '$2y$10$4jAEow4pzFupfJZS6HNWmuTaI4q53oXS4FLdhbp9OciHQiCfnZy9q', 'Karawang', '081282662710', 0, '', 10000, 8, 0, 0, 0, 0, 0, 1, 1, 1, 1, '0000-00-00'),
(2, '12', 123456, 'Admin lain', 'L', '1988-06-15', 'admin1@gmail.com', '$2y$10$/1jsRcRzP4g2meuFjVNx9Oz5HhsvCatJRtCAL.AWjsZwolvfyXv0a', 'Pekalongan', '08123456789', 1, '', 10000, 2, 17500, 60000, 20000, 20000, 20000, 2, 10, 2, 1, '2020-12-22');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_acara`
--

CREATE TABLE `kategori_acara` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `uniq` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori_acara`
--

INSERT INTO `kategori_acara` (`id`, `nama`, `uniq`) VALUES
(1, 'Kumpulan', 'kumpulan'),
(2, 'Rapat', 'rapat');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_gallery`
--

CREATE TABLE `kategori_gallery` (
  `id` int(11) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `uniq` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori_gallery`
--

INSERT INTO `kategori_gallery` (`id`, `nama`, `uniq`) VALUES
(1, 'Gallery', 'gallery'),
(2, 'Gallery 1', 'gallery1');

-- --------------------------------------------------------

--
-- Table structure for table `konseling`
--

CREATE TABLE `konseling` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_peng` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `topik` varchar(100) NOT NULL,
  `solusi` text NOT NULL,
  `tgl_pengajuan` date NOT NULL,
  `tgl_tutup` date NOT NULL,
  `pembuka` enum('Pengurus','Siswa') NOT NULL,
  `penutup` varchar(250) NOT NULL,
  `status` enum('Pending','Respon','Terbaca','Selesai','Respon Siswa','Terbaca Siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kontak`
--

CREATE TABLE `kontak` (
  `id` int(11) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subjek` varchar(250) NOT NULL,
  `pesan` text NOT NULL,
  `tgl` date NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kontak`
--

INSERT INTO `kontak` (`id`, `nama`, `email`, `subjek`, `pesan`, `tgl`, `status`) VALUES
(1, 'Dahri Anshor', 'dahrianshor@gmail.com', 'Apa saja', 'Test Pesan Message', '2021-07-10', 2),
(2, 'Aden', 'aden.baihaqi@gmail.com', 'Test Email', 'Test Email', '2022-01-13', 2);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggaran`
--

CREATE TABLE `pelanggaran` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_pelang` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `id_pend` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `penggajian`
--

CREATE TABLE `penggajian` (
  `id` int(11) NOT NULL,
  `id_peng` int(11) NOT NULL,
  `id_divisi` int(11) NOT NULL,
  `tgl_awal` date NOT NULL,
  `tgl_akhir` date NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penggajian`
--

INSERT INTO `penggajian` (`id`, `id_peng`, `id_divisi`, `tgl_awal`, `tgl_akhir`, `status`) VALUES
(12, 2, 1, '2022-01-01', '2022-01-31', 0);

-- --------------------------------------------------------

--
-- Table structure for table `perizinan`
--

CREATE TABLE `perizinan` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_izin` int(11) NOT NULL,
  `keterangan` text NOT NULL,
  `tgl` date NOT NULL,
  `expired` date NOT NULL,
  `status` enum('Success','Pending','Proses','Expired') NOT NULL,
  `id_pend` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ppdb`
--

CREATE TABLE `ppdb` (
  `id` int(11) NOT NULL,
  `no_daftar` varchar(30) NOT NULL,
  `nik` varchar(30) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(250) NOT NULL,
  `jk` enum('L','P') NOT NULL,
  `ttl` date NOT NULL,
  `prov` varchar(250) NOT NULL,
  `kab` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `nama_ayah` varchar(255) NOT NULL,
  `nama_ibu` varchar(255) NOT NULL,
  `pek_ayah` varchar(100) NOT NULL,
  `pek_ibu` varchar(100) NOT NULL,
  `nama_wali` varchar(255) NOT NULL,
  `pek_wali` varchar(255) NOT NULL,
  `peng_ortu` varchar(255) NOT NULL,
  `no_telp` varchar(100) NOT NULL,
  `thn_msk` int(11) NOT NULL,
  `sekolah_asal` varchar(255) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `diniyah` varchar(100) NOT NULL,
  `img_siswa` varchar(255) NOT NULL,
  `img_kk` varchar(255) NOT NULL,
  `img_ijazah` varchar(255) NOT NULL,
  `img_ktp` varchar(255) NOT NULL,
  `status` int(2) NOT NULL,
  `alasan` text NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ppdb`
--

INSERT INTO `ppdb` (`id`, `no_daftar`, `nik`, `nis`, `nama`, `email`, `no_hp`, `password`, `jk`, `ttl`, `prov`, `kab`, `alamat`, `nama_ayah`, `nama_ibu`, `pek_ayah`, `pek_ibu`, `nama_wali`, `pek_wali`, `peng_ortu`, `no_telp`, `thn_msk`, `sekolah_asal`, `kelas`, `diniyah`, `img_siswa`, `img_kk`, `img_ijazah`, `img_ktp`, `status`, `alasan`, `date_created`) VALUES
(16, 'SAN00001', '009', '009', 'SIAPA', 'ma.aqien@gmail.com', '0', '$2y$10$cq7OPnWXHE3CBxd2qbVnMe3g.ODoumeQfk2tl5we6lFNgWilpe/Ze', 'L', '2001-07-08', 'Jawa Barat', 'Bekasi', 'bekasi', 'AYAH', 'IBU', 'Buruh', 'Ibu Rumah Tangga', '', 'Tidak ada wali', '< Rp.1.000.000', '0', 2022, 'MI', '7', 'SMP', 'ffc2b51ab517b8bc606d40ee68c5960b-111c0b67f89b532956c866f1e9826072_600x400.jpg', 'Hijrah1.jpg', 'unta.jpg', 'ffc2b51ab517b8bc606d40ee68c5960b-111c0b67f89b532956c866f1e9826072_600x4001.jpg', 1, '', '2022-01-09'),
(17, 'SAN00017', '12000998877', '120929600', 'Aden Baihaqi', 'aden.baihaqi@gmail.com', '08129887777', '$2y$10$JL2ZGUPsO1EyBQu2mc2VpOaqFNq4BTdCe9Re9jGCVbvK8byrArW8C', 'L', '1990-01-02', 'Banten', 'Tangerang Selatan', 'Jl Raya Gading Serpong', 'Ayahku', 'Ibuku', 'Wiraswasta', 'Ibu Rumah Tangga', 'Waliku', 'Tidak ada wali', 'Rp.3.000.000 - Rp.4.000.000', '088766666', 2001, 'MI Teladan Bawamai', 'IV', 'AA', 'tes.jpg', 'RQ3.PNG', 'RQ1.PNG', 'logo.png', 1, '', '2022-01-10');

-- --------------------------------------------------------

--
-- Table structure for table `provinsi`
--

CREATE TABLE `provinsi` (
  `id_prov` char(2) NOT NULL,
  `nama` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provinsi`
--

INSERT INTO `provinsi` (`id_prov`, `nama`) VALUES
('11', 'Aceh'),
('12', 'Sumatera Utara'),
('13', 'Sumatera Barat'),
('14', 'Riau'),
('15', 'Jambi'),
('16', 'Sumatera Selatan'),
('17', 'Bengkulu'),
('18', 'Lampung'),
('19', 'Kepulauan Bangka Belitung'),
('21', 'Kepulauan Riau'),
('31', 'DKI Jakarta'),
('32', 'Jawa Barat'),
('33', 'Jawa Tengah'),
('34', 'DI Yogyakarta'),
('35', 'Jawa Timur'),
('36', 'Banten'),
('51', 'Bali'),
('52', 'Nusa Tenggara Barat'),
('53', 'Nusa Tenggara Timur'),
('61', 'Kalimantan Barat'),
('62', 'Kalimantan Tengah'),
('63', 'Kalimantan Selatan'),
('64', 'Kalimantan Timur'),
('65', 'Kalimantan Utara'),
('71', 'Sulawesi Utara'),
('72', 'Sulawesi Tengah'),
('73', 'Sulawesi Selatan'),
('74', 'Sulawesi Tenggara'),
('75', 'Gorontalo'),
('76', 'Sulawesi Barat'),
('81', 'Maluku'),
('82', 'Maluku Utara'),
('91', 'Papua Barat'),
('92', 'Papua');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `nik` varchar(30) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(250) NOT NULL,
  `jk` enum('L','P') NOT NULL,
  `ttl` date NOT NULL,
  `prov` varchar(250) NOT NULL,
  `kab` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `nama_ayah` varchar(255) NOT NULL,
  `nama_ibu` varchar(255) NOT NULL,
  `pek_ayah` varchar(100) NOT NULL,
  `pek_ibu` varchar(100) NOT NULL,
  `nama_wali` varchar(255) NOT NULL,
  `pek_wali` varchar(255) NOT NULL,
  `peng_ortu` varchar(255) NOT NULL,
  `no_telp` varchar(100) NOT NULL,
  `thn_msk` int(11) NOT NULL,
  `sekolah_asal` varchar(255) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `diniyah` varchar(100) NOT NULL,
  `img_siswa` varchar(255) NOT NULL,
  `img_kk` varchar(255) NOT NULL,
  `img_ijazah` varchar(255) NOT NULL,
  `img_ktp` varchar(255) NOT NULL,
  `id_pend` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `date_created` date NOT NULL,
  `role_id` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `point`, `nik`, `nis`, `nama`, `email`, `no_hp`, `password`, `jk`, `ttl`, `prov`, `kab`, `alamat`, `nama_ayah`, `nama_ibu`, `pek_ayah`, `pek_ibu`, `nama_wali`, `pek_wali`, `peng_ortu`, `no_telp`, `thn_msk`, `sekolah_asal`, `kelas`, `diniyah`, `img_siswa`, `img_kk`, `img_ijazah`, `img_ktp`, `id_pend`, `id_kelas`, `status`, `date_created`, `role_id`) VALUES
(1, 100, '12000998877', '120929600', 'Aden Baihaqi', 'aden.baihaqi@gmail.com', '', '$2y$10$JL2ZGUPsO1EyBQu2mc2VpOaqFNq4BTdCe9Re9jGCVbvK8byrArW8C', 'L', '1990-01-02', 'Banten', 'Tangerang Selatan', 'Jl Raya Gading Serpong', 'Ayahku', 'Ibuku', 'Wiraswasta', 'Ibu Rumah Tangga', 'Waliku', 'Tidak ada wali', 'Rp.3.000.000 - Rp.4.000.000', '088766666', 2001, 'MI Teladan Bawamai', 'IV', 'AA', '', '', '', '', 0, 0, 1, '2022-01-10', 5);

-- --------------------------------------------------------

--
-- Table structure for table `tagline`
--

CREATE TABLE `tagline` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tagline`
--

INSERT INTO `tagline` (`id`, `nama`, `deskripsi`, `img`) VALUES
(1, 'Sistem Smart School A', 'Sekolah kami sudah menggunakan sistem yang terbaru yaitu Smart School. Jadi siswa akan lebih kreatif dan aktif dalam proses pembelajaran.', 'values-11.png'),
(2, 'Sistem Smart School B', 'Sekolah kami sudah menggunakan sistem yang terbaru yaitu Smart School. Jadi siswa akan lebih kreatif dan aktif dalam proses pembelajaran.', 'values-2.png'),
(3, 'Sistem Smart School C', 'Sekolah kami sudah menggunakan sistem yang terbaru yaitu Smart School. Jadi siswa akan lebih kreatif dan aktif dalam proses pembelajaran.', 'values-3.png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jamaah`
--

CREATE TABLE `tbl_jamaah` (
  `id_jamaah` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `alamat` varchar(50) NOT NULL,
  `blok` varchar(10) NOT NULL,
  `no` int(11) NOT NULL,
  `status` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_jamaah`
--

INSERT INTO `tbl_jamaah` (`id_jamaah`, `nama`, `alamat`, `blok`, `no`, `status`) VALUES
(2, 'Iman', 'Perumahan Anugerah Sejahtera 3', 'B', 2, 'Belum'),
(5, 'Dimas', 'Perumahan Anugerah Sejahtera 3', 'E', 5, 'Sudah Terima'),
(7, 'Ammar', 'Perumahan Anugerah Sejahtera 3', 'C', 1, ''),
(8, 'Nanda', 'Perumnas', 'A', 5, NULL),
(10, 'Ucang', 'Perumahan PT Semen', 'E', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_masjid`
--

CREATE TABLE `tbl_masjid` (
  `id_masjid` int(11) NOT NULL,
  `nama_masjid` varchar(50) NOT NULL,
  `sapi` int(11) NOT NULL,
  `kambing` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_masjid`
--

INSERT INTO `tbl_masjid` (`id_masjid`, `nama_masjid`, `sapi`, `kambing`, `jumlah`) VALUES
(2, 'Al-Anshar', 13, 7, 20),
(3, 'Al-Alaq', 5, 5, 10);

-- --------------------------------------------------------

--
-- Table structure for table `website`
--

CREATE TABLE `website` (
  `id` int(11) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `deskripsi` text NOT NULL,
  `alamat` varchar(250) NOT NULL,
  `kab` varchar(250) NOT NULL,
  `logo` varchar(250) NOT NULL,
  `fav` varchar(100) NOT NULL,
  `img_login` varchar(100) NOT NULL,
  `img_login_admin` varchar(250) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telp` varchar(255) NOT NULL,
  `maps` text NOT NULL,
  `link_fb` varchar(255) NOT NULL,
  `link_ig` varchar(255) NOT NULL,
  `link_tw` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `website`
--

INSERT INTO `website` (`id`, `nama`, `deskripsi`, `alamat`, `kab`, `logo`, `fav`, `img_login`, `img_login_admin`, `email`, `telp`, `maps`, `link_fb`, `link_ig`, `link_tw`) VALUES
(1, 'Flobamora Technology', 'Perusahaan pengembang web dan android di NTT', 'Perumahan Anugerah Sejahtera Blok E no 1', 'Semarang', 'RQ-removebg-preview.png', 'RQ-removebg-preview1.png', '—Pngtree—muslim_read_quran_5315501.png', '3627634.jpg', 'flobamora.tech@gmail.com', '082146109855', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63375.286073977055!2d109.63939797254515!3d-6.895940718061649!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70242ca490fe13%3A0xc0c68a126b258cb6!2sPekalongan%2C%20Kota%20Pekalongan%2C%20Jawa%20Tengah%2C%20Indonesia!5e0!3m2!1sid!2shk!4v1625671808388!5m2!1sid!2shk\" width=\"800\" height=\"350\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', 'https://facebook.com/', 'https://instagram.com/', 'https://twitter.com/');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `absen`
--
ALTER TABLE `absen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `absen_pegawai`
--
ALTER TABLE `absen_pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acara`
--
ALTER TABLE `acara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `balas_konseling`
--
ALTER TABLE `balas_konseling`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daftar_absen`
--
ALTER TABLE `daftar_absen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_absen_pegawai`
--
ALTER TABLE `data_absen_pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_cicilan`
--
ALTER TABLE `data_cicilan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_divisi`
--
ALTER TABLE `data_divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_kelas`
--
ALTER TABLE `data_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_kursi`
--
ALTER TABLE `data_kursi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_pelanggaran`
--
ALTER TABLE `data_pelanggaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_pendidikan`
--
ALTER TABLE `data_pendidikan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_perizinan`
--
ALTER TABLE `data_perizinan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_sender`
--
ALTER TABLE `email_sender`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home`
--
ALTER TABLE `home`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_acara`
--
ALTER TABLE `kategori_acara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_gallery`
--
ALTER TABLE `kategori_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `konseling`
--
ALTER TABLE `konseling`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perizinan`
--
ALTER TABLE `perizinan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppdb`
--
ALTER TABLE `ppdb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tagline`
--
ALTER TABLE `tagline`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_jamaah`
--
ALTER TABLE `tbl_jamaah`
  ADD PRIMARY KEY (`id_jamaah`);

--
-- Indexes for table `tbl_masjid`
--
ALTER TABLE `tbl_masjid`
  ADD PRIMARY KEY (`id_masjid`);

--
-- Indexes for table `website`
--
ALTER TABLE `website`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about`
--
ALTER TABLE `about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `absen`
--
ALTER TABLE `absen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `absen_pegawai`
--
ALTER TABLE `absen_pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `acara`
--
ALTER TABLE `acara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `balas_konseling`
--
ALTER TABLE `balas_konseling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daftar_absen`
--
ALTER TABLE `daftar_absen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_absen_pegawai`
--
ALTER TABLE `data_absen_pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_cicilan`
--
ALTER TABLE `data_cicilan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_divisi`
--
ALTER TABLE `data_divisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `data_kelas`
--
ALTER TABLE `data_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `data_kursi`
--
ALTER TABLE `data_kursi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `data_pelanggaran`
--
ALTER TABLE `data_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `data_pendidikan`
--
ALTER TABLE `data_pendidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `data_perizinan`
--
ALTER TABLE `data_perizinan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `email_sender`
--
ALTER TABLE `email_sender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `home`
--
ALTER TABLE `home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kategori_acara`
--
ALTER TABLE `kategori_acara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kategori_gallery`
--
ALTER TABLE `kategori_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `konseling`
--
ALTER TABLE `konseling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `penggajian`
--
ALTER TABLE `penggajian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `perizinan`
--
ALTER TABLE `perizinan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ppdb`
--
ALTER TABLE `ppdb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tagline`
--
ALTER TABLE `tagline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_jamaah`
--
ALTER TABLE `tbl_jamaah`
  MODIFY `id_jamaah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_masjid`
--
ALTER TABLE `tbl_masjid`
  MODIFY `id_masjid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `website`
--
ALTER TABLE `website`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
