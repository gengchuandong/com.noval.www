<?php
/* 操作  */
class operation extends MY_Controller
{ 
	public function __construct()
	{
		parent::__construct();
		$this->load->model("operation_model");
	}
	
	/**
	 * 功能：点评
	 * 创建人：gengchuandong
	 * 创建时间：2015-11-16 上午02:57:45
	 */
	public function comment()
	{
		$res = $this->operation_model->comment();
	}
	
	/**
	 * 功能：封面制作
	 * 创建人：gengchuandong
	 * 创建时间：2015-11-16 上午02:59:20
	 */
	public function cover()
	{
		$res = $this->operation_model->cover();
	}
	
	/** 
	 * 功能：试签约
	 * 创建人：gengchuandong
	 * 创建时间：2015-11-16 上午02:59:40
	 */
	public function try_contract()
	{
		$res = $this->operation_model->try_contract();
	}
}