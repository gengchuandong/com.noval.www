<?php
class operation_model extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("user_model");
	}
	
	/**
	 * 
	 * 功能：提交点评
	 * 创建人：gengchuandong
	 * 创建时间：2015-11-3 上午02:09:30
	 */
	public function comment()
	{
		$post = post();
		$novel_n = isset($post['novel_n']) ? trim($post['novel_n']):'';
		if (empty($novel_n)) return false;
		
		$user_id = $this->session->userdata('user_id');
		$db = select_db();
		$db->trans_start();//事务开始
		$novel_arr = array(
			'title' => $novel_n,
		);
		if (isset($post['url']) && trim($post['url']) != ''){//如果填写url，则主表插入链接
			$novel_arr['url'] = trim($post['url']);
		}
		
		$db->insert('dp_novel',$novel_arr);
		$novel_id = $db->insert_id();
		
		if (isset($post['content']) && trim($post['content']) != ''){//如果填写内容，则从表插入内容
			$db->query("insert into dp_novel_content (novel_id,content) values($novel_id,'".trim($post['content'])."')");
		}
		$com_arr = array(
			'novel_id' => $novel_id,
			'create_user' => $user_id,
		);
		$price = isset($post['price']) ? intval($post['price']) : 0;
		if ($price > 0){
			$is_have_account = $this->user_model->check_account($price);
			if (!$is_have_account) return false;//余额不足，不予许提交付费点评
			$com_arr['type'] = 1;
			$com_arr['price'] = $price;
			//扣除用户账户相应的金额
			$db->query("update dp_user set account=account-$price where id=$user_id and is_delete=0");
			//账户余额变更记录日志
			$db->query("insert into dp_account_log(user_id,type,account,content) values($user_id,2,$price,'申请点评花费')");
		}
		$db->insert('dp_comment',$com_arr);
		$db->trans_complete();//事务结束
		
		return true;
	}
 
	/** 
	 * 功能：封面中心的封面制作申请
	 * 创建人：gengchuandong
	 * 创建时间：2015-11-9 上午03:53:19
	 */
	public function cover()
	{
		$post = post();
		$novel_n = isset($post['novel_n']) ? trim($post['novel_n']):'';
		$author = isset($post['author']) ? trim($post['author']):'';
		$cat_id = isset($post['cat_id']) ? intval($post['cat_id']):0;
		$url = isset($post['url']) ? trim($post['url']):'';
		$price = isset($post['price']) ? intval($post['price']):0;
		
		if (empty($novel_n) || empty($author) || $cat_id<=0 || empty($url) || $price<=0) return false;
		
		$is_have_account = $this->user_model->check_account($price);
		if (!$is_have_account) return false;//余额不足，不予许提交付费点评
		
		$user_id = $this->session->userdata('user_id');
		$db = select_db();
		$db->trans_start();//事务开始
		
		$novel_arr = array(
			'title' => $novel_n,
			'author' => $author,
			'cat_id' => $cat_id,
			'url' => $url, 
		);
		
		$db->insert('dp_novel',$novel_arr);
		$novel_id = $db->insert_id();
		
		$cov_arr = array(
			'novel_id' => $novel_id,
			'price' => $price,
			'create_user' => $user_id,
		);
		if (isset($post['bg_img']) && trim($post['bg_img'])!=''){
			$cov_arr['bg_img'] = trim($post['bg_img']);
		}
		if (isset($post['desc']) && trim($post['desc'])!=''){
			$cov_arr['desc'] = trim($post['desc']);
		}
 		$db->insert('dp_cover',$cov_arr);
		//扣除用户账户相应的金额
		$db->query("update dp_user set account=account-$price where id=$user_id and is_delete=0");
		//账户余额变更记录日志
		$db->query("insert into dp_account_log(user_id,type,account,content) values($user_id,3,$price,'申请封面制作花费')");

		$db->trans_complete();//事务结束 
		return true;
	}
	
	/** 
	 * 功能：试签约
	 * 创建人：gengchuandong
	 * 创建时间：2015-11-16 上午03:02:35
	 */
	public function try_contract()
	{
		$post = post();
		$name = isset($post['name']) ? trim($post['name']):'';
		$tel = isset($post['tel']) ? trim($post['tel']):'';
		$qq = isset($post['qq']) ? trim($post['qq']):'';
		$website = isset($post['website']) ? trim($post['website']):'';
		$price = isset($post['price']) ? intval($post['price']):0;
		
		if (empty($name) || empty($tel) || empty($qq) || empty($website) || $price<=0) return false;
		
		$is_have_account = $this->user_model->check_account($price);
		if (!$is_have_account) return false;//余额不足，不予许提交付费签约
		
		$user_id = $this->session->userdata('user_id');
		$db = select_db();
		$db->trans_start();//事务开始
		
		$contract_arr = array(
			'name' => $name,
			'tel' => $tel,
			'qq' => $qq,
			'website' => $website,
			'price' => $price,
			'create_user' => $user_id,
		);
		
		$db->insert('dp_try_contract',$contract_arr);
 
		//扣除用户账户相应的金额
		$db->query("update dp_user set account=account-$price where id=$user_id and is_delete=0");
		//账户余额变更记录日志
		$db->query("insert into dp_account_log(user_id,type,account,content) values($user_id,4,$price,'试签约花费')");

		$db->trans_complete();//事务结束 
		return true;
	}
}