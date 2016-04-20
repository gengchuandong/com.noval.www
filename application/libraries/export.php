<?php
class export
{
	/**
	 * 将数组动态输出至 csv 文件【服务器端输出到浏览器】
	 * @param array $data 二维数组
	 * @param string $filename 文件名
	 */
	function outputCsv($data, $filename = 'file.csv',$arr=array(-1)) {
		header("Content-type: text/html; charset=GB2312"); 
		header('Content-Type:application/force-download');
		header("content-Disposition:filename={$filename}");
	   
		$numberindex=0;
		foreach ($data as $fields) {
			$numberindex=0;
			foreach ($fields as $key => $value) {
				if(in_array($numberindex,$arr)){
					echo mb_convert_encoding(str_replace(",","，", trim($value)),"gbk", "UTF-8").",";
				}
				else {
					echo mb_convert_encoding(str_replace(",","，", trim($value)),"gbk", "UTF-8")."\t,";
				}
				$numberindex++;
				//echo  iconv("utf-8","gb2312",$value) . "\t,";				
			}
			echo "\r\n";
		}
	}
	/**
	 * 将数组动态输出至 csv 文件【服务器端生成文件】
	 * @param array $data 二维数组
	 * @param string $filename 文件名
	 */
	function tocsv($header,$data,$file,$arr=array(-1)){
		$keys = array_keys($header);
		foreach ($header as $k=>$v){
			$list[0][$k]=$v;
		}
		foreach ($data as $key => $value){
			foreach ($keys as $k){
				$list[$key+1][$k] = $value[$k];
			}
		} 
		$this->outputCsv($list,$file,$arr);
		exit;
	
	}	
	/**
	 * 将数组动态输出至 csv 文件【服务器端生成文件】
	 * @param array $data 二维数组
	 * @param string $filename 文件名
	 */
	public function writeCsv($data,$filename, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE)
	{
		$pf='./images/uploads/'.date("Ym");
		if (!file_exists($pf))
		{
			mkdir ($pf);
		}
		$path=$pf.'/'.$filename.'.csv';
		if ( ! $fp = @fopen($path, 'w'))
		{
			return FALSE;
		}
		$header = array_keys($data[0]);
		fputcsv($fp,$header);
		foreach ($data as $fields) {
		   fputcsv($fp,$fields);
		}
		fclose($fp);
		return TRUE;
	}
}