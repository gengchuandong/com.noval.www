<?php
class MY_Controller extends CI_Controller {
	public $err_code=0;
	public $err_msg='';
	public $list_fields=array();
	public $data;
	public $json_arr = array();
    function __construct()
    {
        parent::__construct();
//        $user_id = $this->session->userdata("user_id");
//        if(empty($user_id) || $user_id<1){
//        	echo "<script>window.top.location='/login';</script>";
//        	exit;
//        }
//    	if(!$this->admin_priv()){
//	    	echo "<div style='font-size:15px;color:red;margin:20px;'>对不起，您的权限不足，不允许进行此操作！</div>";
//	    	exit;
//	    }
		$this->load->set_path('v1');//判断当前版本文件夹
    } 
	/**
	 * 功能：判断当前操作权限
	 * 创建人：lee
	 * 创建时间：2015-7-20 上午08:37:58
	 * @param $function 如为null则为点击时判断
	 */
    public function admin_priv($function = null){
    	$role_id = $this->session->userdata('role_id');
    	$c = $this->uri->segment(1);
    	
    	if(empty($function)){
    		$f = $this->uri->segment(2);
    	}
    	else {
    		$f = $function;
    	}
    	if(empty($c)&&empty($f)){
    		return true;
    	}
    	if($role_id==4 || ($c=='user' && $f=='info') || ($c=='user' && $f=='update')){
    		return true;
    	}
    	if($f=='submit'){
    		$f = 'update';
    	}
    	
    	if(empty($f)){
    		$f = "index";
    	}

    	$this->load->model('menu_model');
    	$m_data = $this->menu_model->menu_data(true,1); 
 
    	$menu_ids = array_keys($m_data,$c);
    	if(count($menu_ids)<=0){
    		return true;
    	}
    	
    	//查询当前操作是否需控制
    	$h_all_data = $this->menu_model->all_handle_data();
        //如需控制，则查询是否有此权限
    	$h_data = $this->menu_model->handle_data($role_id);
    	//将当前菜单id及权限标识拼装
    	$khadle_ids = array();
    	$ahadle_ids = array();
   		foreach ($menu_ids as $k=>$v){
   			$menu_id = $v;
   			$hadle_idea = $menu_id.','.$f;
   			$khadle_id = array_search($hadle_idea, $h_all_data);
   			if (!empty($khadle_id)){
   				$khadle_ids[] = $khadle_id;
   			} 
   			$ahadle_id = array_search($hadle_idea, $h_data);
      		if (!empty($ahadle_id)){
   				$ahadle_ids[] = $ahadle_id;
   			} 
   		} 
//    	$hadle_idea = $menu_id.','.$f;  

//    	$hadle_id = array_search($hadle_idea, $h_all_data);
//    	if(empty($hadle_id)){
//    		return true;
//    	} 
        if(count($khadle_ids)<=0){
    		return true;
    	}
    	//如果存在相同控制器名的菜单权限方法则通过
        if(count($ahadle_ids)>0){
    		return true;
    	}
    	
//		$hadle_ids = array_diff($khadle_ids, $ahadle_ids);   
//		$flag = empty($hadle_ids)?1 : 0;  
//		  
//		if ($flag) {  
//			return true;
//		} 
    	
//    	$hadle_id = array_search($hadle_idea, $h_data);
 
//    	if(!empty($hadle_id)){
//    		return true;
//    	}  
	    return false;
    	
    }
	/**格式化json数据
	 * @param $statusCode
	 * @param $message
	 * @param $navTabId
	 * @param $rel
	 * @param $callbackType  closeCurrent  forward
	 * @param $forwardUrl
	 * */
    public function tojson(){
    	$arr = $this->json_arr;
    	if(empty($arr)){
	    	$jdata = array(
		    	'statusCode'=>200,
		    	'message'=>'操作成功',
		    	'navTabId'=>'',
		    	'rel'=>'',
		    	'callbackType'=>'closeCurrent',
		    	'forwardUrl'=>''
	    	);
    	}else{
    		$jdata = array(
		    	'statusCode'=>isset($arr['statusCode'])?$arr['statusCode']:200,
		    	'message'=>isset($arr['message'])?$arr['message']:'操作成功',
		    	'navTabId'=>isset($arr['navTabId'])?$arr['navTabId']:'',
		    	'rel'=>isset($arr['rel'])?$arr['rel']:'',
		    	'callbackType'=>isset($arr['forwardUrl'])&&!empty($arr['forwardUrl'])?'forward':'closeCurrent',
		    	'forwardUrl'=>isset($arr['forwardUrl'])?$arr['forwardUrl']:'',
	    	);
    	}
    	echo json_encode($jdata,JSON_UNESCAPED_UNICODE);
    	exit;
    }
	public function export($res,$file_name='',$arr=array(-1)){
		if(empty($res)) die('无数据');
		$this->load->library('export');
		$header = array_keys($res[0]);
		foreach($header as $k=>$v){
			$h[$v] = $v;
		}
		if(empty($file_name)){
			$file_name = __FUNCTION__;
		}
		export($h,$res,$file_name,$arr);
	}
/**
	 * 功能：操作权限（是否显示当前）
	 * 创建人：lee
	 * 创建时间：2015-7-20 上午08:24:53
	 * @param $function 名称
	 */
	public function handle_power($function = "index")
	{
		$is_show = $this->admin_priv($function);
		echo $is_show;
	}
}