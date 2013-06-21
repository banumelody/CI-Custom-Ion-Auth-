<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class User extends CI_Controller {

	public $data;

	function __construct (){
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('form_validation');

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('language');

		$this->lang->load('auth');
		$this->load->database('users');
		$this->load->model('user_model');
		
	}

	function index() {
		if (!$this->ion_auth->logged_in()) {
			redirect('/user/login', 'refresh');
		}
		echo anchor('/user/logout', 'Log Out', 'title="Log Out"');
		//$this->ion_auth->is_allow();
	}

	function login() {
		if ($this->ion_auth->logged_in()) {
			redirect('/user','refresh');
		}

		$this->data['title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('identity', 'Identity', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true)
		{
			//check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('/user', 'refresh');
			}
			else
			{
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('/user/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		}
		else
		{
			//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);

			$this->load->view('login', $this->data);
		}
	}

	function logout() {
		$this->ion_auth->logout();
		redirect('/user', 'refresh');	
	}

	function generator() {
		$this->ion_auth->is_allow('user_generator');

		echo "string";
	}

	function groups() {
		$this->ion_auth->is_allow('user_'.__FUNCTION__);
		//echo get_class($this).__FUNCTION__;
		//echo get_class_methods($this);

		$groups = $this->user_model->get_groups();
		if ($groups) {
			foreach ($groups as $key => $value) {
				$link 	= '/user/group/'.$value->id;
				$title	= $value->name;
				echo anchor($link, $title, $title).'<br>	';
			}
		}
	}

	function group($id) {
		if(!$id || empty($id))
		{
			redirect('user/groups', 'refresh');
		}
		
		$group = $this->user_model->get_group_privileges($id);
		if ($group) {
			//echo json_encode($group);
			$checkbox	= array();
			foreach ($group as $key => $value) {
				$param	= array(
	    			'name'        => $value->id,
	    			'id'          => $value->id,
	    			//'value'       => 'accept',
	    			'checked'     => $value->access,
    			);
    			$checkbox[]	= $param;
			

				$this->data['group_id']		= $id;
				$this->data['privileges']	= $group;
				$this->data['checkbox']		= $checkbox;
				if ($this->form_validation->run() == true){

				}else{
					
					$this->load->view('set_privilege', $this->data);
				}
			}
		}
	}

	function profile() {
		$this->ion_auth->is_allow('user_'.__FUNCTION__);

		$user 	= $this->ion_auth->user()->row();
		echo json_encode($user);
	}

	function test() {
		if ($this->ion_auth->add_group_to_access(3,1)) {
			echo "bisa nambah";	
		}else{
			echo "ga bisa nambah";
		}
	}
}
