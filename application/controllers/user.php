<?php
/* 用户    */
class user extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("user_model");
	}
	
	public function index()
	{
		$user_id = $this->session->userdata('user_id');
		if($user_id>0){
			$res['info'] = $this->user_model->user_info($user_id);
			$this->load->view("user/info",$res);
		}else{
			redirect("/user/login");
		}
	}
	
	public function login()
	{
		$post = $this->input->post();
		$user_name = $this->input->post("username");
		$password = $this->input->post("pwd");
		if(!empty($user_name) && !empty($password)){
			$res = $this->user_model->check_login($user_name,$password);
			if($res){
				redirect("home");
			}
		}
		$this->load->view('user/login',$post);
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect("user/login");
	}
	
	public function register()
	{
		if (!empty($this->input->post())){ 
			$res = $this->user_model->register();
			if ($res){
				redirect("home");
			}
		}
		$this->load->view("user/register");
	}
}