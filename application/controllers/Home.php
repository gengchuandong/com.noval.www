<?php
class Home extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();  
	}
	
	/** 
	 * 功能：初始化首页
	 * 创建人：gengchuandong
	 * 创建时间：2015-8-18 上午02:48:47
	 */
	public function index()
	{
		$this->load->view("home");
	} 
}