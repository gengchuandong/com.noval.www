<?php
class queue extends MY_Controller{
	public $act_coupon_id = 0;
	public $pre_num = 50;
	public $mec_key = '';
	public $key = 'COUPON_BIND_';
	public $mec;
	public function __construct($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
		$this->mec = mec();
	}
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}
	public function add($arr = array(),$id = 0)
	{
		$mec_key = $this->key.$id;
		$uid = $this->mec->get($mec_key);
		if(empty($uid) && count($arr)>0){
			$this->mec->delete($mec_key);
			$this->mec->add($mec_key,$arr);
			$uid = $arr;
		}
		return count($uid);
	}
	
	public function get_list($id = 0)
	{
		$uid = $this->mec->get($this->key.$id);
		if(empty($uid)) return 0;
		$i = $this->pre_num;
		$uid1 = array_slice($uid, 0, $i);
		return $uid1;
	}
	public function get_all_list($id = 0)
	{
		$uid = $this->mec->get($this->key.$id);
		if(empty($uid)) return 0;
	//	krsort($uid);
	//	asort($uid);
		return $uid;
	}
	public function update_list($c = 0, $id = 0)
	{
		if($c == 0){
			$c = $this->pre_num;
		}
		$uid = $this->mec->get($this->key.$id);
		if(empty($uid)) return false;
		$uid = array_splice($uid, $c,count($uid));
		$this->mec->replace($this->key.$id,$uid);
	}
	public function total($id = 0)
	{
		$uid = $this->mec->get($this->key.$id);
		return !empty($uid)?count($uid):0;
	}
}