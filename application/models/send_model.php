<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Send_model extends CI_Model {

	public function insert($data) {
		$q = $this->
			db->
			insert('lines', $data);
		
		if ($this->db->insert_id())
		{
			return $this->db->insert_id();
		}
		return false;
	}

}