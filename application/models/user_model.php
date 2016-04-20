<?php
class User_model extends MY_Model{

	public function __construct()
	{
		parent::__construct(); 
	}
	
	/** 
	 * 功能：判断是否登陆
	 * 创建人：gengchuandong
	 * 创建时间：2015-10-19 上午10:03:01
	 */
	public function is_login(){
		$user_id = $this->session->userdata('user_id');
		if($user_id>0){
		}else{
			redirect("/user/login");
		}
	}

	/**
	 * 功能：判断是否为管理员
	 * 创建人：gengchuandong
	 * 创建时间：2015-10-19 上午10:03:48
	 */
	public function is_admin(){
		$role_id = $this->session->userdata('role_id');
		$user_id = $this->session->userdata('user_id');
		if($user_id>0 && $role_id==1){
		}else{
			die('Without permission!');
		}
	}

	/** 
	 * 功能：检查登陆状态
	 * 创建人：gengchuandong
	 * 创建时间：2015-10-19 上午10:04:16
	 */
	public function check_login($user_name,$password){
		$db = select_db();
		$sql = "select a.id,a.username,a.mobile,a.nickname,a.level,a.password
			from dp_user a where a.username='$user_name' and a.is_delete=0"; 
		$info = $db->query($sql)->row_array();
	 	$pwd = isset($info['password'])?$info['password']:'';
	 	if (!pwd_verify($password,$pwd)){
	 		return false;
	 	}
    	$login_arr = array(
			'user_id'=>isset($info['id'])?$info['id']:0,
			'user_name'=>isset($info['username'])?$info['username']:'',
    		'mobile'=>isset($info['mobile'])?$info['mobile']:'',
    		'level'=>isset($info['level'])?$info['level']:0,
    		'nickname'=>isset($info['nickname'])?$info['nickname']:'',
		);
		$this->session->set_userdata($login_arr);
		return true;
	}

	/** 
	 * 功能：更新用户信息
	 * 创建人：gengchuandong
	 * 创建时间：2015-10-20 上午02:13:15
	 */
	public function user_update($post){
		$db = select_db('default');
		
		$old_password = $post['old_password'];
		if(empty($old_password)){
			$this->err_code=1;
			$this->err_msg='原密码不能为空';
			return FALSE;
		}
		$user_id = $this->session->userdata('user_id'); 
		$new_password = $post['new_password'];
		$new_password1 = $post['new_password1'];
		if(!empty($old_password)){
			if(empty($new_password) || empty($new_password1)){
				$this->err_code=2;
				$this->err_msg='新密码不能为空';
				return FALSE;
			}
			if($new_password != $new_password1){
				$this->err_code=2;
				$this->err_msg='新密码输入不一致';
				return FALSE;
			} 
			$old_password = pwd_hash($old_password);
			$sql = "select count(id) total from dp_user where id=$user_id and password='$old_password'";
			$res = $db->query($sql)->row_array();
			$q = $res['total'];
			if($q == 0){
				$this->err_code=2;
				$this->err_msg='旧密码不正确';
				return FALSE;
			}
			$num = strlen($new_password);
			if($num<4 || $num>16){
				$this->err_code=2;
				$this->err_msg='密码长度必须4-16位之间';
				return FALSE;
			}
			$udate['password'] = pwd_hash($new_password);
		}  
		return $db->query("update dp_user set password='".pwd_hash($new_password)."' where id=$user_id");
	}

 
	
	/** 
	 * 功能：用户注册
	 * 创建人：gengchuandong
	 * 创建时间：2015-10-19 上午01:28:01
	 */
    public function register()
    {
    	$post = $this->post();
    	$username = isset($post['username'])?$post['username']:'';
    	$pwd = isset($post['pwd'])?$post['pwd']:'';
    	$repwd = isset($post['repwd'])?$post['repwd']:'';
    	
    	if (empty($username) && empty($pwd) && empty($repwd)){
    		return false;
    	}
    	if ($this->check_username($username) > 0){
    		return false;
    	}
    	if ($pwd === $repwd){
    		$in_arr = array(
    			'username' => $username,
    			'password' => pwd_hash($pwd),
    		);
    		$db = select_db('default');
    		if ($db->insert('dp_user',$in_arr)){
    			$id = $db->insert_id();
    			$info = $this->user_info($id);
    			
    			$login_arr = array(
					'user_id'=>isset($info['id'])?$info['id']:0,
					'user_name'=>isset($info['username'])?$info['username']:'',
    				'mobile'=>isset($info['mobile'])?$info['mobile']:'',
    				'level'=>isset($info['level'])?$info['level']:0,
    				'nickname'=>isset($info['nickname'])?$info['nickname']:'',
				);
				$this->session->set_userdata($login_arr);
    			return true;
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    	
    }
    
    /** 
     * 功能：根据用户id获取信息
     * 创建人：gengchuandong
     * 创建时间：2015-10-19 上午02:24:08
     */
    public function user_info($id=0)
    {
    	if ($id<=0) return null;
    	
    	$db = select_db();
    	$sql = "select id,username,level,mobile,nickname,account from dp_user where id=$id and is_delete=0";
    	return $db->query($sql)->row_array();
    }
	
    /** 
     * 功能：检验是否有存在用户名/手机号，有，返回用户id；无，返回0.
     * 创建人：gengchuandong
     * 创建时间：2015-10-19 上午01:51:36
     */
	public function check_user($object='')
	{
		$db = select_db('default');
		if(empty($object)){
			return 0;
		}
		$sql = "select a.id from dp_user a where ( a.username='$object' or a.mobile='$object' ) and a.is_delete=0";
		$res = $db->query($sql)->row_array();
		if(empty($res)){
			return 0;
		}else{
			return $res;
		}
	}
	
	/** 
	 * 功能：用户充值vip
	 * 创建人：gengchuandong
	 * 创建时间：2015-10-23 上午08:33:58
	 */
	public function recharge($user_id=0,$account=0)
	{
		if ($user_id <= 0 || $account <= 0) return false;
		
		$db = select_db();
		//查询累计充值金额，并更新用户vip等级
		$sql = "select sum(account) total from dp_account_log where user_id=$user_id and type=1 and is_delete=0"; 
		$res = $db->query($sql)->row_array();
		$s_account = isset($res['total'])?intval($res['total']):0 + $account;
		$lv = 0;
		if ($s_account >= 10) $lv = 1;
		if ($s_account >= 50) $lv = 2;
		if ($s_account >= 100) $lv = 3;
		if ($s_account >= 500) $lv = 4;
		if ($s_account >= 1000) $lv = 5;
		if ($s_account >= 10000) $lv = 6;
		
		$db->trans_start();//开启事务
		
		$sql = "update dp_user set account=account+$account and level=$lv where id=$user_id and is_delete=0";
		$db->query($sql);
		
		$in_arr = array(
			'user_id' => $user_id,
			'type' => 1,
			'account' => $account,
		);
		$db->insert('dp_account_log',$in_arr);
		$db->trans_complete();//事务结束
		
		return true;
	}
	
	/**
	 * 功能：检查账户余额是否能进行扣款操作
	 * 创建人：gengchuandong
	 * 创建时间：2015-11-9 上午09:04:58
	 */
	public function check_account($price = 0)
	{
		$db = select_db();
		$user_id = $this->session->userdata('user_id');
		$sql = "select account from dp_user where state=0 and is_delete=0 and id=$user_id";
		$res = $db->query($sql)->row_array();
		if ((double)$res['account'] >= (double)$price){
			return true;
		}else{
			return false;
		}
	}
	
}