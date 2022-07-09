<?php

/**
 * 
 */
class Jamaah_model extends CI_model
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function getAllJamaah()
	{
		$q = $this->db->query("SELECT * FROM tbl_jamaah ORDER BY nama ASC ");
		return $q;
	}

	public function getJamaahById($id)
	{
		return $this->db->get_where('tbl_jamaah' , ['id_jamaah' => $id])->row_array();
	}

	public function tambahdata($data)
	{
		$data = [
			"nama" => $this->input->post('nama',true),
			"alamat" => $this->input->post('alamat',true),
			"blok" => $this->input->post('blok',true),
			"no" => $this->input->post('no',true),
			"status" => $this->input->post('status',true)
		];
		$this->db->insert('tbl_jamaah', $data);
	}

	public function hapusdata($id)
	{
		$this->db->delete('tbl_jamaah', ['id_jamaah' => $id]);
	}

	public function ubahdata($data)
	{		
		$data = [
			"nama" => $this->input->post('nama',true),
			"alamat" => $this->input->post('alamat',true),
			"blok" => $this->input->post('blok',true),
			"no" => $this->input->post('no',true),
			"status" => $this->input->post('status',true)
		];
		$this->db->where('id_jamaah', $this->input->post('id_jamaah'));
		$this->db->update('tbl_jamaah', $data);
	}

}