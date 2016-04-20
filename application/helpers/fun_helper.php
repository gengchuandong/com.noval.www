<?php 
   
function utf8_strlen($string = null) {  
		// 将字符串分解为单元  
		preg_match_all("/./us", $string, $match);  
		// 返回单元个数  
		return count($match[0]);  
	} 
if (!function_exists('arrayToString'))
{
	function arrayToString($arr){
	    ksort($arr);
		$str = '';
		foreach($arr as $k=>$v){
		  $str .= $str == '' ? $k.'='.$v : '&'.$k.'='.$v;
		}
		return $str;
	}
}
if ( ! function_exists('tojson'))
{
	function tojson($success=1,$err_code=0,$result='',$navTabId='',$rel='',$forwardUrl='') { 
		$ci=&get_instance();
		$template_id = $ci->session->userdata("template_id");
	    if($template_id>0){
			$jdata = array(
		    	'statusCode'=>$success>0?200:300,
		    	'message'=>!empty($result)?$result:'操作成功',
		    	'navTabId'=>isset($navTabId)?$navTabId:'',
		    	'rel'=>isset($rel)?$rel:'',
		    	'callbackType'=>!empty($forwardUrl)?'forward':'closeCurrent',
		    	'forwardUrl'=>!empty($forwardUrl)?$forwardUrl:'',
	    	);
	    	echo json_encode($jdata,JSON_UNESCAPED_UNICODE);
    		exit;
	    } 
	  die(json_encode(array('success'=>$success,'err_code'=>$err_code,'result'=>$result)));    // 
	} 
}
if ( ! function_exists('tojson_model'))
{
	function tojson_model($model,$result=''){  
		if(isset($model->err_code) && $model->err_code>0){
			$success = 0;
			$err_code = $model->err_code;
			$result = $model->err_msg;
		}else{
			$success = 1;
			$err_code = 0;
			$result = $model->result;
		}
		tojson($success,$err_code,$result);
	 // die(json_encode(array('success'=>$success,'err_code'=>$err_code,'result'=>$result),JSON_UNESCAPED_UNICODE));    // 
	} 
}
if ( ! function_exists('sql_check'))
{
	function sql_check($sql_str) { 
	  return preg_match('/select|insert|update|delete|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/', $sql_str);    // 进行过滤  
	} 
}
 
    if (!function_exists('is_mobile'))
	{
		function is_mobile($str){
			return preg_match("/^1[0-9][0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$str);  
		}
	}
	if (!function_exists('is_mail'))
	{
		function is_mail($str){
			return preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $str);  
		}
	} 
	if (!function_exists('img_url'))
	{
		function img_url($str,$is_thumb=0){
			if(empty($str)){
				return "/images/nopic.png";
			}
			if(preg_match("/^http?/",$str)){
				return $str;
			}
			if($is_thumb){
				return IMAGE_SERVER_URL.$str;  
			}else{
				return IMAGE_SERVER_URL.$str;
			}
		}
	}
	function status($status,$pre='v'){
		$ci = &get_instance();
		$ci->lang->load('status', 'zh_cn');
		return $ci->lang->line($pre.'_'.$status);
	}
	function type($pre='',$num=0){
		$ci = &get_instance();
		$ci->lang->load('type', 'zh_cn');
		return $ci->lang->line($pre.'_'.$num);
	}
	function view($path,$data=''){
		$ci = &get_instance();
		$is_moile = $ci->session->userdata('is_mobile');
		if($is_moile){
			$ci->load->set_path('mobile');
		}
		$ci->load->view($path,$data);
	}
	if (!function_exists('vcurl'))
	{
		function vcurl($url, $post = '', $cookie = '', $cookiejar = '', $referer = ''){
		    $tmpInfo = ''; 		   
		    $cookiepath = 'cookie.txt';  
		    $header[] = "Content-type: application/x-www-form-urlencoded; charset=utf-8"; 
		    $curl = curl_init(); 
		    curl_setopt($curl, CURLOPT_URL, $url);   
		    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   
		    if($referer) {   
		    curl_setopt($curl, CURLOPT_REFERER, $referer);   
		    } else {   
		    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);    
		    }   
		    if($post) {   
		    curl_setopt($curl, CURLOPT_POST, 1);    
		    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);   
		    }   
		    if($cookie) {   
		    curl_setopt($curl, CURLOPT_COOKIE, $cookie);   
		    }   
		    if($cookiejar) {   
		    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiepath);   
		    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiepath);   
		    }   
		    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);   
		    curl_setopt($curl, CURLOPT_TIMEOUT, 100);   
		    curl_setopt($curl, CURLOPT_HEADER, 0);  
		    curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);    
		    $tmpInfo = curl_exec($curl);   
		    if (curl_errno($curl) || curl_error($curl)) {   
		    //  echo '<pre><b>错误:</b><br />'.curl_error($curl);exit;
				$tmpInfo='';
		    }   
		    curl_close($curl);
		    return $tmpInfo;   
		}
	}
	function pwd_hash($str=''){
		$salt = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
		$salt = base64_encode($salt);
		$salt = str_replace('+', '.', $salt);
		$hash = crypt($str, '$2a$06$'.$salt.'$');
		return  $hash;
	}
	function pwd_verify($pwd1='',$pwd2=''){
		return password_verify($pwd1,$pwd2);
	}
	function model_data($model,$fun,$id=''){ 
		$ci=&get_instance();
		$m = $model.'_model';
		$ci->load->model($m);
		return $ci->$m->$fun($id);
	}
	function config_data($key){
		$ci=&get_instance();
		$ci->load->model("config_model");
		return $ci->config_model->get_info($key);
	}

	/** 
	 * 功能：发送短信
	 * 创建人：gengchuandong
	 * 创建时间：2015-10-22 上午09:17:17
	 */
	function sms_send_msg($mobile,$content){
		$ci=&get_instance();
		$ci->load->library('sms');
		return $ci->sms->send_msg($mobile,$content); 
	}
	
	function select_db($db=''){
		$ci=&get_instance();
		$db = !empty($db)?$db:'default';
		return $ci->load->database($db,true);
	}
	function data($db='default'){
		$ci=&get_instance();
		$ci->load->library('dbs',array('db'=>$db));
		return $ci->dbs;
	} 
	function mec($api='memcached'){
		$ci=&get_instance();
		$ci->config->load('memcached');
		$args = $ci->config->item($api);
		$memcache_obj = memcache_connect($args['server'],$args['port']);
		return $memcache_obj; 
	}
	function redis($api='redis'){
		$ci=&get_instance();
		$ci->config->load('redis');
		$args = $ci->config->item($api);
		$redis = new redis();
		$redis->connect($args['server'], $args['port']);
		return $redis; 
	}

	/** 
	 * 功能：获取指定日期所在月的第一天和最后一天
	 * 创建人：gengchuandong
	 * 创建时间：2015-8-21 上午07:29:03
	 */
	function get_month($date=''){
		if(empty($date)){
			$date = date("Y-m-d H:i:s");
		}
		$firstday = date("Y-m-01",strtotime($date));
		$lastday = date("Y-m-d",strtotime("$firstday +1 month -1 day"));
		return array($firstday,$lastday);
	 }

	 /**
	 * post
	 * */
	function post($kname=''){
		$ci=&get_instance();
		$post = $ci->input->post();
		$get = $ci->input->get();
		if(!empty($get)){
			foreach ($get as $k=>$v){
				$post[$k]=$v;
			}
		}
		if(!empty($kname)){
			return $post[$kname];
		}
		return $post;
	}
	/**导出
	 * @param	$header	表格标题
	 * @param	$data	数据
	 * @param	$file	导出文件名
	 * */
	function export($header,$data,$file,$arr=array(-1)){
		$ci=&get_instance();
		$ci->load->library("export");
		$ci->export->tocsv($header,$data,$file.'.csv',$arr);
	}	

function list_page_header($action=''){
	$ci=&get_instance();
	if(!empty($action)){
		$str = substr($action, 0,1);
		if($str!='/'){
			$action = '/'.$action;
		}
	}else{
		$action = '/'.$ci->uri->segment('1');
	}
	$data = array(
	'action'=>$action,
	);
	$ci->load->view("/public/page_header",$data);
}
function list_page_footer(){
	$ci=&get_instance();
	$ci->load->view("/public/list_page");
}

/** 
 * 功能：将json_decode解析后的数组转化成标准数组
 * 创建人：gengchuandong
 * 创建时间：2015-8-18 上午08:30:55
 */
function object_array($arr){
  if(is_object($arr)){
    $arr = (array)$arr;
  }
  if(is_array($arr)){
    foreach($arr as $k=>$v){
      $arr[$k] = object_array($v);
    }
  }
  return $arr;
} 