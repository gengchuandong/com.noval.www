<?php
class flog{
	var $ci;
	var $log_path;
	var $name;
	public function __construct(){
		$this->ci = &get_instance();
		$this->log_path = APPPATH.'/logs/';
	}
	public function write($name,$content=''){
		if(is_array($content)){
			$content = var_export($content,true);
		}
		$ip = $this->ci->input->ip_address();
		
		$content = date('Y-m-d H:i:s').'|'.$ip.'|'.$content."\r\n";
		
		$data[] = $content;
		$name = $this->log_path.date('Y-m-d').'_'.$name.'.csv';
		return $this->write_file($name, $data,'a');
	}
	public function write_file($path, $data, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE)
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		foreach ($data as $fields) {
		 //   fputcsv($fp, $fields);
		}
		flock($fp, LOCK_UN);
		fclose($fp);
		return TRUE;
	}
	
	/**
	 * 记录日志 csv格式
	 * $param	$content	一维数组
	 * @name	$name	日志文件名
	 * */
	public function log($name,$content){
		$ip = $this->ci->input->ip_address();
		if(!is_array($content)){
			$content = array('time'=>date('Y-m-d H:i:s'),'ip'=>$ip,$content);
		}else{
			$arr1 = array('time'=>date('Y-m-d H:i:s'),'ip'=>$ip);
			$content = array_merge($arr1,$content);
		}
		$path = $this->log_path.date('Y-m-d').'_'.$name.'.log';
		if ( ! $fp = @fopen($path, 'a'))
		{
			return FALSE;
		}
		flock($fp, LOCK_EX);
		fwrite($fp, print_r($content,true));
		flock($fp, LOCK_UN);
		fclose($fp);
		return TRUE;
	}

}