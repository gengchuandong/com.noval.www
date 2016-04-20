<?php
class dbs
{
	public $db;
	public function __construct($config=''){
		$ci = &get_instance();
		$dname = 'default';
		if(!empty($config) && is_array($config)){
			if(isset($config['db']) && !empty($config['db'])){
				$dname = $config['db'];
			}
		}
		$this->db = $ci->load->database($dname,true);
	}
	/*获取单行数据*/
	public function getRow($sql){
		return $this->db->query($sql)->row_array();
		$this->db->close();
	}
	/*获取单个结果*/
	public function getOne($sql){
		$res = $this->db->query($sql)->row_array();
		foreach ($res as $k=>$v) {
			$r = $v;
			break;
		}
		return $r;
		$this->db->close();
	}
	/*获取所有行*/
	public function getAll($sql){
		return $this->db->query($sql)->result_array();
		$this->db->close();
	}
	/*获取行数*/
	public function getNums($sql){
		return $this->db->query($sql)->num_rows();
		$this->db->close();
	}
	/*更新*/
	public function update($table,$arr,$where){
		return $this->db->update($table,$arr,$where);
	}
	public function insert($table,$arr){
		$this->db->insert($table,$arr);
		return $this->db->insert_id();
	}
	public function insert_batch($table,$arr){
		return $this->db->insert_batch($table,$arr);
	}
	public function query($sql){
		return $this->db->query($sql);
	}
}