<?php
class MY_Model extends CI_Model{
	public $err_code = 0; //错误码
	public $err_msg = ''; //错误描述
	public $mydb;
	public function __construct(){
		parent::__construct();
	}
	public function return_result($err_code=0,$err_msg=''){
		$this->err_code = $err_code;
		$this->err_msg = $err_msg;
	}
	/**
	 * 格式化输出json
	 * @param	$success	成功=1，失败=0
	 * @param	$err_code	错误码
	 * @param	$result	结果
	 * */
	public function tojson($success=1,$err_code=0,$result='') {  
	  die(json_encode(array('success'=>$success,'err_code'=>$err_code,'result'=>$result)));
	}  
	 
	/**
	 * 添加缓存
	 * @param	$key	缓存键值
	 * @param	$val	缓存值
	 * @param	$time	缓存时间(秒)
	 * */
	public function mec_add($key,$val,$time=0){
		$this->load->library('mec');
		return $this->mec->add($key,$val,$time);
	}
	/**
	 * 更新缓存
	 * @param	$key	缓存键值
	 * @param	$val	缓存值
	 * @param	$time	缓存时间(秒)
	 * */
	public function mec_replace($key,$val,$time=0){
		$this->load->library('mec');
		return $this->mec->replace($key,$val,$time);
	}
	/**
	 * 获取缓存
	 * @param	$key	缓存键值
	 * */
	public function mec_get($key){
		$this->load->library('mec');
		return $this->mec->get($key);
	}
	/**
	 * 删除缓存
	 * @param	$key	缓存键值
	 * @param	$time	缓存时间(秒)
	 * */
	public function mec_delete($key,$time=0){
		$this->load->library('mec');
		return $this->mec->delete($key,$time);
	}
	/**
	 * 设置session
	 * @param	$arr	数组
	 * */
	public function session_set($arr){
		$this->load->library("session");
		return $this->session->set_userdata($arr);
	}
	/**
	 * 获取session
	 * @param	$key	session键值
	 * */
	public function session_get($key){
		$this->load->library("session");
		return $this->session->userdata($key);
	}
	/**
	 * 删除session
	 * @param	$array	数组
	 * */
	public function session_delete($array){
		$this->load->library("session");
		$this->session->unset_userdata($array);
	}
	/**
	 * 销毁session
	 * */
	public function session_destroy(){
		$this->load->library("session");
		$this->session->sess_destroy();
	}
	/**
	 * 获取接口数据
	 * */
	public function get_api_result($model,$fun,$param){
		$this->load->library("api");
		return $this->api->get_api_result($model,$fun,$param);
	}
	/**
	 * 翻页
	 * @param	$base_url 链接
	 * @param	$uri 页码变量位置
	 * @param	$total 总条数
	 * @param	$page_size 每页条数
	 * */
	public function page($base_url='',$uri=1,$total=0,$page_size=10){
		$this->load->library('pagination');
		$config['base_url'] = $base_url;
		$config['total_rows'] = $total;
		$config['per_page'] = $page_size; 
		$config['uri_segment'] = $uri;
		$this->pagination->initialize($config); 
		$link = $this->pagination->create_links();
		return array('page'=>$link,'total'=>$total);
	}
	/**
	 * 格式化post
	 * */
	public function post(){
		$post = $this->input->post();
		$post['numPerPage']= isset($post['numPerPage'])?$post['numPerPage']:20;
		$post['page_size'] = $post['numPerPage'];
		$post['pageNum'] = isset($post['pageNum'])?intval($post['pageNum']):1;
		$post['limit'] = ($post['pageNum']-1)*$post['numPerPage'];
		$get = $this->input->get();
		if(!empty($get)){
			foreach ($get as $k=>$v){
				$post[$k]=$v;
			}
		}
		return $post;
	}
	
	/** 
	 * 功能：导出数据到csv文件
	 * 创建人：gengchuandong
	 * 创建时间：2015-8-17 上午05:53:55
	 * 参数：  $menu_arr ： 导出的菜单项数组：field 菜单对应的key   menu_name 菜单名称
	 *       $sql : 查询sql
	 * 		 $table : 对应的主表
	 *       $file_name ： 导出文件名
	 */
	public function	excel($menu_arr,$sql,$table='default',$file_name='',$arr=array(-1)){
		if(empty($menu_arr)) die('无数据');
		
		$this->load->library('export');
		$header = array();
		foreach ($menu_arr as $k => $v){
			$header[$v['field']] = $v['menu_name'];
		}
		$keys = array_keys($header);  
		if(empty($file_name)){
			$file_name = __FUNCTION__;
		} 
		//写头部
		header("Content-type: text/html; charset=GB2312"); 
		header('Content-Type:application/force-download');
		header("content-Disposition:filename={$file_name}.csv");
		 
		foreach ($header as $k=>$v){
			$list[0][$k]=$v;
		}
		$numberindex=0;
		foreach ($list as $fields) {
			$numberindex=0;
			foreach ($fields as $key => $value) {
				if(in_array($numberindex,$arr)){
					echo mb_convert_encoding(str_replace(",","，", trim($value)),"gbk", "UTF-8").",";
				}
				else {
					echo mb_convert_encoding(str_replace(",","，", trim($value)),"gbk", "UTF-8")."\t,";
				}
				$numberindex++;
				//echo  iconv("utf-8","gb2312",$value) . "\t,";				
			}
			echo "\r\n";
		} 
		//查询分批次写入，每次查询10000条，写入10000条
		$db = select_db($table);
		$limit = 10000;
		$c = 0;
		$res = $header;
		while (!empty($res)){ 
			$rsql = $sql." limit $c,$limit";
			$c = $c + $limit; 
			$res = $db->query($rsql)->result_array();
			$rs = array(); 
			foreach ($res as $key => $value){
				foreach ($keys as $k){
					$rs[$key][$k] = $value[$k];
				}
			} 
			$numberindex=0;
			foreach ($rs as $fields) {
				$numberindex=0;
				foreach ($fields as $key => $value) {
					if(in_array($numberindex,$arr)){
						echo mb_convert_encoding(str_replace(",","，", trim($value)),"gbk", "UTF-8").",";
					}
					else {
						echo mb_convert_encoding(str_replace(",","，", trim($value)),"gbk", "UTF-8")."\t,";
					}
					$numberindex++;
					//echo  iconv("utf-8","gb2312",$value) . "\t,";				
				}
				echo "\r\n";
			}
		} 
		return true; 
	}
}