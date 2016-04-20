<?php 
class mec {
	public $mecobj;
	public $args;
	public function __construct($args=array()){
		$ci = &get_instance();
		$ci->config->load('memcached');
		if(empty($args)){
		   	$this->args = $ci->config->item('memcached');
			$memcache_obj = memcache_connect($this->args['server'], $this->args['port']);
			$this->mecobj = $memcache_obj;
		}else{
			$this->args = $ci->config->item('redis');
			$redis = new redis();  
			$redis->connect($this->args['server'], $this->args['port']); 
			$this->mecobj = $redis;
		}
	}
	public function add($key,$val,$time=0){
		if(isset($this->args['type']) && $this->args['type']=='redis'){
			if($time>0){
				return $this->mecobj->setex($key,$time,$val);
			}else{
				return $this->mecobj->set($key,$val);
			}
		}else{
			return $this->mecobj->add($key,$val,$this->args['uses_zlib'],$time);
		}
	}
	public function get($key){
		return $this->mecobj->get($key);
	}
	public function replace($key,$val,$time){
		return $this->mecobj->replace($key,$val,$this->args['uses_zlib'],$time);
	}
	//全部删除
	public function flush(){
		return $this->mecobj->flush();
	}
	//删除指定键值
	public function delete($key,$time=0){
		return $this->mecobj->delete($key,$time);
	}
	public function getStatus(){
		return $this->mecobj->getStats();
	}
	
}

?>