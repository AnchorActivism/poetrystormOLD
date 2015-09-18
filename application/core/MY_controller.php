<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();

        error_reporting(E_ALL);
        ini_set('display_errors', 1);


        /*
        if (current_url() == base_url().'index.php/home/guest' ||
            current_url() == base_url().'index.php/login' ||
            current_url() == base_url().'index.php/login/service/Facebook') 

        {
            if ( isset($_SESSION['email'])) {
                redirect('home');
            }
        }
        else
        {

            log_message('debug', 'current url: '.current_url());

            if ( !isset($_SESSION['email'])) {
                redirect('home/guest');
            }

            //echo current_url().'<br />';
            //echo base_url().'index.php/home/guest';

        }
        */


        if (isset($_SESSION['email'])) {
        	log_message('debug', 'logged user: '.$_SESSION['email']);
	        $this->load->model('user_model');
	        $this->usr = $this->user_model->find_by_email($_SESSION['email']);
    	}
    }

    public function index() {


    }
}