<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Experience extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
	}

	public function index($focus=null, $reply=null) {


		$this->load->library('form_validation');
		$this->form_validation->set_rules('line', 'Line', 'required');

		if ($this->form_validation->run() !== FALSE)
		{
			$this->load->model('send_model');

			$data = array('line' => $this->input->post('line'), 'reply_to' => $this->input->post('reply_to'));

			$insert_id = $this->send_model->insert($data);

			if($insert_id!==false)
			{
				
				if(isset($focus)&&$focus!==0) 
				{
					redirect('experience/'.$focus.'/'.$insert_id);
				}
				else
				{
					redirect('experience/'.$insert_id);
				}
			}
			else
			{
				echo "Fail";
			}

			$focus = $this->input->post('id');
		}

		$data['focus'] = $focus;
		$data['reply'] = $reply;

		if (isset($reply)) {
			$data['reply_to'] = $reply;
		}
		else if (isset($focus)) {
			$data['reply_to'] = $focus;
		}
		else
		{
			//new poem
			$data['reply_to'] = 0;
		}		

		$this->load->model('exp_model');

		if ($focus !== null && $focus !== 0 && $this->exp_model->get_line_by_id($focus))
		{//line specified and exists

			//get focused line and all parent lines for view
			//$data['lines'] = $this->exp_model->get_focus($focus);

			//specify submit cta for reply
			$data['submit_placeholder'] = 'Add a line...';
			$data['submit_cta'] = 'PASS IT ON';


			if ($reply !== null && $reply!=='0' && $this->exp_model->get_line_by_id($reply))
			{//A specific reply has been selected AND exists

				//add replies to view data
				$data['replies'] = $this->exp_model->get_focus_replies($reply, $focus);

			}
			else
			{
				$data['replies'] = $this->exp_model->get_latest_replies($focus);
			}

		}
		else
		{
			//no focus is specified, retrieve and accomodate first lines
			$data['lines'] = $this->exp_model->get_latest();
			$data['first_lines'] = true;
			$data['submit_placeholder'] = '';
			$data['submit_cta'] = 'START A POEM';
		}

		$data['css'] = array('experience');

		$data['js'] = array('jquery-2.1.1.min', 'js.cookie', 'controller');

		if (!isset($focus)) {$focus = '\'null\'';}

		//$data['js_scripts'][1] = '';

		$data['js_vars'] = array(
			'focus' => $focus,
			'base_url' => '\''.base_url().'\''
			);

		$this->load->view('header', $data);

		$this->load->view('experience', $data);

		$this->load->view('footer', $data);


	}

}