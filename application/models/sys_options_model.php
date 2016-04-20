<?php
class Sys_options_model extends MY_Model{
	public $type_list = array(
	0=>'酒品','头像','举报类型','短信通道','备注标签','取消订单标签','投诉标签','客服动作标签','处罚规则标签'
	);
	
	public function sys_options_data($id = 0, $type = 0)
	{
		$list = array();
		$where  = " where 1";
		if($type>0){
			$where .= " and opt_type = $type";
		}
		$sql = "select * from m_sys_options $where";
		$res = $this->data_getAll($sql);
		foreach ($res as $v){
			$list[$v['id']] = $v['opt_name'];
		} 
		if($id>0){
			return isset($list[$id])?$list[$id]:'';
		}else{
			return $list;
		}		
	}
	
	public function sys_options8_data()
	{
		return $this->sys_options_data(0, 8);
	}
	
	public function sys_options_list()
	{
		$post = $this->post();
		//echo $post['limit']."!".$post['numPerPage'];
		$where = " where 1";

		$is_delete = 0;
		if(isset($post['is_delete']) && $post['is_delete']>0){
			$is_delete = 1;	
			$where .= " and is_delete = 1";
		}else{
			$where .= " and is_delete = 0";
		}
		
		if (isset($post['opt_type']) && $post['opt_type']>0){
			$where .= " and opt_type=".(intval($post['opt_type'])-1);
		}
		
		$biz_name = "";
		if(isset($post['biz_name']) && !empty($post['biz_name'])){
			$biz_name = trim($post['biz_name']);
			$where .= " and opt_name like '%{$biz_name}%' ";
		}
		
		//查询某类分类，外部调用时
		if(isset($post['t'])){
			$where .= " and opt_type = {$post['t']}";
		}
		
		$this->load->library('data');
		$this->data->db = select_db('');
		
		$sql = "select id,opt_name,create_time,is_delete,
		case opt_type
		when 0 then '酒品'
		when 1 then '用户头像'
		when 2 then '举报原因' 
		when 3 then '短信通道'
		when 4 then '备注标签'
		when 5 then '取消订单标签'
		when 6 then '投诉标签'
		end as opt_type_content
		from m_sys_options $where";
		$post['total'] = $this->data->getNums($sql);
		$sql .= " order by sort desc limit {$post['limit']},{$post['numPerPage']}";
		//echo $sql;
		$list = $this->data->getAll($sql);
//		foreach($list as $k=>$v){
//			$list[$k]['opt_type']=$this->type_list[$v['opt_type']];
//		}
//		/*if(isset($post['t'])){
//			$post['t']=$post['t'];
//		}*/
		$post['list'] = $list;
		$post['is_delete'] = $is_delete;
		$post['biz_name'] = $biz_name;
		return $post;
	}
	
	public function sys_options_info($id = 0)
	{
		if($id>0){
			$sql = "select * from m_sys_options where id = $id";
			//echo $sql;
			$res = $this->data_getRow($sql);
			return $res;
		}
	}
}