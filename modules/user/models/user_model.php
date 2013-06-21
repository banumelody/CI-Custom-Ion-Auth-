<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class User_model extends CI_Model {


	function __construct (){
		parent::__construct();
	}

	function get_groups(){
		$this->load->database('users');
		$query = $this->db->get('groups');

		if (!$query->num_rows()>0) {
			return False;
		} else {
			return $query->result();
		}
	}

	function get_group_privileges($id) {
		$this->load->database('users');

		$this->db->from('functions f');
		$this->db->order_by("name", "asc");
		$query	= $this->db->get();


		if ($query->num_rows()>0) {
			foreach ($query->result() as $key => $value) {
				$this->db->from('privileges p');
				$this->db->where('p.group_id', $id);
				$this->db->where('p.function_id', $value->id);
				$access	= $this->db->get();
				if ($access->num_rows()>0) {
					$query->row($key)->access = true;
				} else {
					$query->row($key)->access = false;
				}
			}
			return $query->result();
		} else return False;
	}
}