<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$active_group = 'default';
$active_record = TRUE;
// c_novel主库
$db['default']['hostname'] = '127.0.0.1'; 
$db['default']['username'] = 'root';
$db['default']['password'] = '123456';
$db['default']['database'] = 'c_novel';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE; 
//jiukuaidao 从库，只支持读取
$db['db1']['hostname'] = '127.0.0.1'; 
$db['db1']['username'] = 'root';
$db['db1']['password'] = '123456';
$db['db1']['database'] = 'c_novel';
$db['db1']['dbdriver'] = 'mysqli';
$db['db1']['dbprefix'] = '';
$db['db1']['pconnect'] = TRUE;
$db['db1']['db_debug'] = TRUE;
$db['db1']['cache_on'] = FALSE;
$db['db1']['cachedir'] = '';
$db['db1']['char_set'] = 'utf8';
$db['db1']['dbcollat'] = 'utf8_general_ci';
$db['db1']['swap_pre'] = '';
$db['db1']['autoinit'] = TRUE;
$db['db1']['stricton'] = FALSE; 
/* End of file database.php */
/* Location: ./application/config/database.php */