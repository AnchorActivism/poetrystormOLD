<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Exp_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	public function get_poem_tree($top) {


		$r = $this->get_line_by_id($top);
	
		if ($r !== false)  {

			$poem[0][0] = $r;

			do {

				$i = count($poem) - 1;

				foreach ($poem[$i] as $key => $value) {
					
					$replies = get_line_by_reply_to($value['id']);

					if ($replies !== false) {

						foreach ($replies as $key2 => $value2) {
							array_unshift($poem[$i], $value2);
						}
					}
			
				}
			} while ($replies!==false);

			return $poem;

		}
	}
/*
	
	public function get_poem_tree($op) {
		//Constructs a list of all FIRST replies and their alternatives.
		//Does not get replies OF alternatives.. as of now.

		$lines[0] = get_line_by_id($op);

	}/*

	/*public function get_poem_tree($top) {
		//Constructs a hierarchical object of the entire poem
		//underneath the top line including replies and alternatives.

		//get top line obj
		$r = $this->get_line_by_id($top);

		if ($r !== false) {
			
			$replies

			$new[0] = $r;

			if ($r->reply_to !== 0) {
				//if this isn't the top line, find alternatives

				do {

					get_line_by_reply_to($top)

				}
				while(get_line_by_reply_to($last) !== false);
			}

		}
	}*/

	public function get_focus_parents($id) {
		
		$lines = array();

		do {

			//returns obj of table row
			$r = $this->get_line_by_id($id);

			if ($r !== false) {

				//wrap line in array
				$new[0] = $r;

				//prepends to $lines
				array_unshift($lines, $new);

				//set id for next "do"
				$id = $r->reply_to;

			}

		} while($id>0);

		return $lines;
	}

public function get_focus_replies($focus) {

		$lines = array();
		$id = $focus;
		$c = 0;

		do {

			//get lines where $reply_to = $id
			$q = $this->
					db->
					where('reply_to', $id)->
					limit(150)->
					get('lines');

			if ($q->num_rows()>0) {

				$r = $q->result();

				//append to $lines
				array_push($lines, $r);

				//set id for next "do"
				$id = $r[0]->id;
				$c++;
			}
			else
			{
				$c=100000000;
			}

		} while ( $c < 150);

		return $lines;

	}
/*
	public function get_focus_parents($id) {
		//Organizes lines as nested objects,
		//starting with the last line and moving backwards.
		
		$lines = array();

		do {

			//returns obj of table row
			$r = $this->get_line_by_id($id);

			if ($r !== false) {

				if (isset($last)) {
					//put last line obj inside parent obj replies array
				
					$r->replies = array();
					array_unshift($r->replies, $last);
				
				}

				//set $id and $last for next "do"
				$id = $r->reply_to;
				$last = $r;

			}

		} while($id>0);

		return $last;
	}*/
/*
	public function get_focus($id) {
		
		$lines = array();

		do {

			//returns obj of table row
			$r = $this->get_line_by_id($id);

			if ($r !== false) {

				//prepends to $lines
				array_unshift($lines, $r);

				//set id for next "do"
				$id = $r->reply_to;

			}

		} while($id>0);

		return $lines;
	}*/

	public function get_latest() {

		$q = $this->db
				->select('*')
				->from('lines')
				->where('reply_to', '0')
				->order_by('id', "desc")
				->limit(200)
				->get();

		

		/*$q = $this->
				db->
				get_where('lines', array('reply_to' => $zero), 100)->
				order_by('id', "asc");

*/
		if ($q->num_rows > 0)
		{	

			$lines = array();

			foreach ($q->result() as $key => $value) {
			
				$new[0] = $value;
				array_push($lines, $new);
			
			}

			return $lines;
		}
		
		return false;
	}

/*
	public function get_focus_replies($reply, $focus) {
		
		//current
		$q = $this->
				db->
				where('id', $reply)->
				limit(1)->
				get('lines');

		$lines[0] = $q->row_array();


		//next	
		$q = $this->
				db->
				where('reply_to', $focus)->
				where('id >', $reply)->
				limit(1)->
				get('lines');

		if ($q->num_rows()<1) {

			$q = $this->
					db->
					where('reply_to', $focus)->
					where('id !=', $reply)->
					limit(1)->
					order_by('id', 'asc')->
					get('lines');
		}
		
		$lines[1] = $q->row_array();
		
		
		//prev
		if (isset($lines[1])) {

			$q = $this->
					db->
					where('reply_to', $focus)->
					where('id <', $reply)->
					limit(1)->
					get('lines');

			if ($q->num_rows()<1) {

				$q = $this->
						db->
						where('reply_to', $focus)->
						where('id !=', $lines[1]['id'])->
						limit(1)->
						order_by('id', 'desc')->
						get('lines');
			}
			
			$lines[2] = $q->row_array();
		
		}

		return $lines;

	}
	*/

	public function get_latest_replies($focus) {
		$q = $this->
				db->
				where('reply_to', $focus)->
				limit(3)->
				get('lines');

		if ($q->num_rows() > 0 ) 
		{
			return $q->result_array();

		}
		return false;
	}

	public function get_line_by_id($id) {

		if($q = $this->
				db->
				get_where('lines', array('id' => $id), 1));

		if ( $q->num_rows() > 0 ) {
			return $q->row();
		}
		return false; 
	}
	
	public function get_line_by_reply_to($reply_to) {

		$q = $this->
				db->
				get_where('lines', array('reply_to' => $reply_to), 100);

		if ( $q->num_rows() > 0 ) {
			return $q->result_array();
		}
		return false; 
	}
}