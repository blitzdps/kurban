<?php

/**
 * 
 */
class Masjid_model extends CI_model
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function getAllMasjid()
	{
		$q = $this->db->query("SELECT * FROM tbl_masjid ORDER BY nama_masjid ASC ");
		return $q;
	}

	public function getMasjidById($id)
	{
		return $this->db->get_where('tbl_masjid' , ['id_masjid' => $id])->row_array();
	}

	public function tambahdata($data)
	{
		$data = [
			"nama_masjid" => $this->input->post('nama_masjid',true),
			"sapi" => $this->input->post('sapi',true),
			"kambing" => $this->input->post('kambing',true),
			"jumlah" => $this->input->post('jumlah',true)
		];
		$this->db->insert('tbl_masjid', $data);
	}

	public function hapusdata($id)
	{
		$this->db->delete('tbl_masjid', ['id_masjid' => $id]);
	}

	public function ubahdata($data)
	{		
		$data = [
			"nama_masjid" => $this->input->post('nama_masjid',true),
			"sapi" => $this->input->post('sapi',true),
			"kambing" => $this->input->post('kambing',true),
			"jumlah" => $this->input->post('jumlah',true)
		];
		$this->db->where('id_masjid', $this->input->post('id_masjid'));
		$this->db->update('tbl_masjid', $data);
	}

}