<?php
class Sso_model extends MY_Model
{ 
	/**
	 * 功能：获取单点登录的所有代理
	 * 创建人：Lethe
	 * E-mail : gengchuandong@jiuxian.com
	 * 创建时间：2016-1-5
	 */
	public function get_all_broker()
	{
		$this->load->config('sso',true);
		$rs = $this->config->item('sso');
		$list = array();
		if (count($rs['broker'])>0){
			foreach ($rs['broker'] as $k=>$v){
				$list[]['broker'] = $k;
				$list[]['api_url'] = $v['url'];
				$list[]['secret'] = $v['secret'];
			}
		}
		return $list;
	}
	
	/**
	 * 功能：获取代理信息
	 * 创建人：Lethe
	 * E-mail : gengchuandong@jiuxian.com
	 * 创建时间：2016-1-5
	 */
	public function get_broker_info($key='')
	{
		$this->load->config('sso',true);
		$rs = $this->config->item('sso');
		return isset($rs['broker'][$key]) ? $rs['broker'][$key]:null;
	}
	
	public function sso_login($username='',$pwd='')
	{
		if (!empty($username) && !empty($pwd)){
			$data['user'] = $username;
			return $data;
		}else{
			$data['error'] = '用户名为空';
			return $data;
		}
	}
}