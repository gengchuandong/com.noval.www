<?php
class sms
{
	public $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";//第三方接口互易无限短信平台：http://www.ihuyi.com/
	public $account = "cf_iy_wenxing";//短信平台账号
	public $password = "@wenxing523";//账号密码
	public function __construct($args=array()){
		$ci = &get_instance(); 
	}
	
//		返回值SubmitResult结构说明：

//		命名	类型	描述
//		code	int	           返回值为2时，表示提交成功
//		smsid	string	仅当提交成功后，此字段值才有意义（消息ID）
//		msg	    string	提交结果描述

//		返回值枚举
//		code	msg

//		0	   	提交失败
//		2		提交成功
//		400		非法ip访问
//		401		帐号不能为空
//		402		密码不能为空
//		403		手机号码不能为空
//		4030	手机号码已被列入黑名单
//		404		短信内容不能为空
//		405		用户名或密码不正确
//		4050	账号被冻结
//		4051	剩余条数不足
//		4052	访问ip与备案ip不符
//		406		手机格式不正确
//		407		短信内容含有敏感字符
//		4070	签名格式不正确
//		4071	没有提交备案模板
//		4072	短信内容与模板不匹配
//		4073	短信内容超出长度限制
//		408		您的帐户疑被恶意利用，已被自动冻结，如有疑问请与客服联系。
	
	public function send_msg($mobile='',$content=''){
		if(empty($mobile) || empty($content)){
			return false;
		} 
		//通过POST方式访问接口
		$post_data = "account=".$this->account."&password=".$this->password."&mobile=".$mobile."&content=".rawurlencode($content);
		$gets =  $this->xml_to_array($this->Post($post_data, $this->target));
		print_r($gets);die;
		return $gets;
	}
	public function Post($curlPost,$url){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_NOBODY, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
			$return_str = curl_exec($curl);
			curl_close($curl);
			return $return_str;
	}
	public function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $thisxml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}
}