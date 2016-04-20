<?php
class csv{
	/**
	 * 读取CSV文件，返回二维数组格式的文件内容
	 * **/
	public function reade($file) {
		$handle = fopen($file, 'r'); 
		$out = array ();
	    $n = 0;
	    while ($data = fgetcsv($handle, 10000)) {
	        $num = count($data);
	        for ($i = 0; $i < $num; $i++) {
	            $out[$n][$i] = $data[$i];
	        }
	        $n++;
	    }
	    fclose($handle);
	    
	    $len_result = count($out);
	    if($len_result==0){
	        exit;
	    }
	    return $out;
	}
}