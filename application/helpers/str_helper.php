<?php
/**
 * 截取字符串
 * @param	$str	字符
 * @param	$start	截取开始位数
 * @param	$lenth	截取长度
 * */
function subString_UTF8($str, $start, $lenth)
    {
        $len = strlen($str);
        $r = array();
        $n = 0;
        $m = 0;
        for($i = 0; $i < $len; $i++) {
            $x = substr($str, $i, 1);
            $a  = base_convert(ord($x), 10, 2);
            $a = substr('00000000'.$a, -8);
            if ($n < $start){
                if (substr($a, 0, 1) == 0) {
                }elseif (substr($a, 0, 3) == 110) {
                    $i += 1;
                }elseif (substr($a, 0, 4) == 1110) {
                    $i += 2;
                }
                $n++;
            }else{
                if (substr($a, 0, 1) == 0) {
                    $r[ ] = substr($str, $i, 1);
                }elseif (substr($a, 0, 3) == 110) {
                    $r[ ] = substr($str, $i, 2);
                    $i += 1;
                }elseif (substr($a, 0, 4) == 1110) {
                    $r[ ] = substr($str, $i, 3);
                    $i += 2;
                }else{
                    $r[ ] = '';
                }
                if (++$m >= $lenth){
                    break;
                }
            }
        }
        
        return $r;
    } 
    function sub_str($str, $start, $lenth){
    	$len = strlen($str);
    	$str = join('',subString_UTF8($str,$start,$lenth));
    	if($len>$lenth){
    		$str .='***';
    	}
    	return $str;
    }
    /**
     * 判断时间类型是整数形式还是日期格式
     * 日期格式直接输出，整数格式继续判断
     * 是多少位的，毫秒还是微秒
     * 微秒除以1000
     * 转成date格式输出
     * update:duan 2014年7月21日14:50:34
     * */
    function my_date($time=0){
    	$pattern = '/:/';
    	$res=preg_match($pattern,$time);
    	if($res){
    		return $time;    		
    	}else{
    		if(strlen($time)>10){
    			$time = $time/1000;
    		}
    		return date("Y-m-d H:i:s",$time);
    	}
    }
    
    /**
     * 获取随机字符串
     *@param $n	取得随机位数
     * */
    function get_word($n=4){
		$pool = '23456789abcdefghgkmnpkrstuvwxyz';
		$str = '';
		for ($i = 0; $i < $n; $i++)
		{
			$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		return $str;
	}
	/**
* 根据数组中的某个键值大小进行排序，仅支持二维数组
*
* @param array $array 排序数组
* @param string $key 键值
* @param bool $asc 默认正序
* @return array 排序后数组
*/
function arraySortByKey(array $array, $key, $asc = true)
{
    $result = array();
    // 整理出准备排序的数组
    foreach ( $array as $k => &$v ) {
        $values[$k] = isset($v[$key]) ? $v[$key] : '';
    }
    unset($v);
    // 对需要排序键值进行排序
    $asc ? asort($values) : arsort($values);
    // 重新排列原有数组
    foreach ( $values as $k => $v ) {
        $result[$k] = $array[$k];
    }

    return $result;
}
/*获取字符串长度*/
function mstrlen($str,$charset='utf-8'){  
	if($charset=='utf-8') $str = iconv('utf-8','gb2312',$str);  
	$num = strlen($str);  
	$cnNum = 0;  
	for($i=0;$i<$num;$i++){  
		if(ord(substr($str,$i+1,1))>127){  
			$cnNum++;  
			$i++;  
		}  
	}  
	$enNum = $num-($cnNum*2);  
	$number = $enNum+$cnNum;  
	return ceil($number);  
}  
/*截取字符串*/
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)  
{  
	if(function_exists("mb_substr"))  
	    return mb_substr($str, $start, $length, $charset);  
	elseif(function_exists('iconv_substr')) {  
	    return iconv_substr($str,$start,$length,$charset);  
	}  
	$re['utf-8']   = "/[/x01-/x7f]|[/xc2-/xdf][/x80-/xbf]|[/xe0-/xef][/x80-/xbf]{2}|[/xf0-/xff][/x80-/xbf]{3}/";  
	$re['gb2312'] = "/[/x01-/x7f]|[/xb0-/xf7][/xa0-/xfe]/";  
	$re['gbk']    = "/[/x01-/x7f]|[/x81-/xfe][/x40-/xfe]/";  
	$re['big5']   = "/[/x01-/x7f]|[/x81-/xfe]([/x40-/x7e]|/xa1-/xfe])/";  
	preg_match_all($re[$charset], $str, $match);  
	$slice = join("",array_slice($match[0], $start, $length));  
	if($suffix) return $slice."…";  
	return $slice;  
}
 function hide_str($account=''){
		$n = mstrlen($account);
		if(is_mobile($account)){
			$account = msubstr($account,0,3).'****'.msubstr($account,$n-4,4);
		}elseif(is_mail($account)){
			$account = explode('@', $account);
			$account = msubstr($account[0], 0,4)."****@".$account[1];
		}
		return $account;
	}