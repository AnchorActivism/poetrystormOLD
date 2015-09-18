<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_port extends MY_Controller {

	function __construct()
	{
		log_message('debug', 'So-far-so-good: 0');
		parent::__construct();
	}

	public function get_top_entries()
	{
		log_message('debug', 'So-far-so-good: 1');
		$this->load->model('exp_model');
		log_message('debug', 'So-far-so-good: 2');
		echo json_encode($this->exp_model->get_latest());
	}

	public function get_focus_parents($focus)
	{
		$this->load->model('exp_model');
		echo json_encode($this->exp_model->get_focus_parents($focus));
	}

	public function get_focus_replies($focus)
	{
		$this->load->model('exp_model');
		echo json_encode($this->exp_model->get_focus_replies($focus));
	}

	public function get_poem_tree($top) {
		echo json_encode($this->exp_model->get_poem_tree($top));
	}

}