<?php
/**
* 单点登录控制器类
*/
class Sso extends CI_Controller
{
        
	/**
     * session存储位置
     * @var string
     */
    public $links_path;
        
    /**
     * 标识sessioin是否开启
     * @var boolean
     */
    protected $started=false;        
        
    /**
     * 当前应用
     * @var string
     */
    protected $broker = null;
   	protected $user = null;

    public function __construct()
    {
    	parent::__construct();
        $this->load->model('sso_model');
        //如果创建连接函数没有开启，$link_path系统默认存储session目录
        //if (!function_exists('symlink')) $this->links_path = sys_get_temp_dir();
        $this->links_path = sys_get_temp_dir();
    }
        
        public function index()
        {
                exit('1');
        }

        
    /**
     * 功能：登录
     * 创建人：Lethe
     * E-mail : gengchuandong@jiuxian.com
     * 创建时间：2016-1-5
     */
    public function login()
    {
        $this->_session_start();
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if (empty($username)) $this->failLogin("no_user_name");
        if (empty($password)) $this->failLogin("no_password");
//die('111');
        //数据库验证
        $info = $this->sso_model->sso_login($username,$password);
        if (isset($info['user'])&&$info['user']!='')
        {
        	$_SESSION['user'] = $info['user'];
            $this->info();
        }else{
            $this->failLogin($info['error']);
        }       
    }               
        
    /**
     * 功能：退出
     * 创建人：Lethe
     * E-mail : gengchuandong@jiuxian.com
     * 创建时间：2016-1-5
     */
    public function logout()
    {
    	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $brokers = $this->sso_model->get_all_broker();
                
        $res='';
        foreach ($brokers as $k=>$v){
        	if (trim($v['api_url'])!=''){
            	$tmp_s = strstr($v['api_url'], '?') ? '&' : '?';
            	$res .= '<script type="text/javascript" src="'.$v['api_url'].$tmp_s.'&time='.time().' reload="1"></script>';                                
        	}
    	}
        $this->_session_start();
       	unset($_SESSION['user']);
        echo $res;
    }
        
        
    /**
     * 功能：获取用户信息
     * 创建人：Lethe
     * E-mail : gengchuandong@jiuxian.com
     * 创建时间：2016-1-5
     */
    public function info()
    {
        $this->_session_start();
        //如果不存在登陆用户 返回提示
        if (!isset($_SESSION['user'])) $this->failLogin("Not logged in");
        echo json_encode($_SESSION['user']);exit();
    }        
        
        
    /**
     * 连接session
     */
    public function attach()
    {
        //开启回话
        $this->_session_start();
//die('dsd');
        //检验broker
        $broker = $this->input->get_post('broker');
        if (empty($broker)) $this->fail("No broker specified");
        //检验token
        $token = $this->input->get_post('token');
        if (empty($token)) $this->fail("No token specified");
        //检验校验码
        $checkcode = $this->input->get_post('checkcode');
        if (empty($checkcode) || $this->generateAttachChecksum($broker, $token) != $checkcode) $this->fail("Invalid checkcode");
        //如果没有设置session存储位置
        if (!isset($this->links_path)) {
            //拼接session存储文件  
            $link = (session_save_path() ? session_save_path() : sys_get_temp_dir()) . "/sess_" . $this->generateSessionId($broker, $token);
//die('dd1'.$link);
            //如果sessioin文件不存在 把本文件链接到系统的session_id上
            if (!file_exists($link)) $attached = symlink('sess_' . session_id(), $link);
            //如果没有链接成功，报错
            if (!$attached) trigger_error("Failed to attach; Symlink wasn't created.", E_USER_ERROR);
        } else {
//die($this->links_path);
            //指定session路径存放session
            $link = "logs/" . $this->generateSessionId($broker, $token);
//die(config_item('sess_save_path').'--dd2'.$link);

            if (!file_exists($link)) $attached = file_put_contents($link, session_id());
            if (!$attached) trigger_error("Failed to attach; Link file wasn't created.", E_USER_ERROR);
        }
        //跳转至broker
        $redirect = $this->input->get_post('redirect');
        if (isset($redirect)) {
            header("Location: " . $redirect, true, 307);
            exit;        
        }
        
        // 输出图片用于ajax登录
        header("Content-Type: image/png");
        readfile("empty.png");
    }        
                
    /*
     * 开启session并且防止session劫持
     */
    protected function _session_start()
    {
       //如果session已经开水器  false
            
       if ($this->started) return;
        $this->started = true;
        // 应用session
        $matches = null;
                
                $cookie = $this->input->cookie(session_name());
                
                //如果通过request方式获取到PHPSSID 并且匹配本规则
        if (isset($cookie) && preg_match('/^SSO-(\w*+)-(\w*+)-([a-z0-9]*+)$/', $cookie, $matches)) {
                
                $sid = $cookie;
                        
                    if (isset($this->links_path) && file_exists("{$this->links_path}/$sid")) {
                            session_id(file_get_contents("{$this->links_path}/$sid"));
                            session_start();
                            setcookie(session_name(), "", 1);
                    } else {
                                session_start();
                    }

            if (!isset($_SESSION['client_addr'])) {
                session_destroy();
                $this->fail("Not attached");
            }

            if ($this->generateSessionId($matches[1], $matches[2], $_SESSION['client_addr']) != $sid) {
                session_destroy();
                $this->fail("Invalid session id");
            }

            $this->broker = $matches[1];
            return;
        }

        // 开启用户会话
        session_start();
                //如果存在客户端IP并且客户端IP和服务端不一致，更新SESSIONID
        if (isset($_SESSION['client_addr']) && $_SESSION['client_addr'] != $_SERVER['REMOTE_ADDR']) session_regenerate_id(true);
                //如果存在客户端IP并且一致，客户端IP设置为服务端IP
        if (!isset($_SESSION['client_addr'])) $_SESSION['client_addr'] = $_SERVER['REMOTE_ADDR'];
    }        

    /**
         * 通过session token生成session id
     * 
     * @return string
     */
    protected function generateSessionId($broker, $token, $client_addr=null)
    {
    	//验证broker
        $info = $this->sso_model->get_broker_info($broker);        
        if ($info) {
            $secret = $info['secret'];
        }else{
            return null;
        }
        //如果客户端地址没有设置，获取客户端IP
        if (!isset($client_addr)) $client_addr = $_SERVER['REMOTE_ADDR'];
        //根据 参数生出客户端session文件名称
        return "SSO-{$broker}-{$token}-" . md5('session' . $token . $client_addr . $secret);
    }
        
    /**
         * 通过session token生成session id
     * 
     * @return string
     */
    protected function generateAttachChecksum($broker, $token)
    {
        $info = $this->sso_model->get_broker_info($broker);
        if ($info) {
            $secret = $info['secret'];
        }else{
            return null;
        }
        return md5('attach' . $token . $_SERVER['REMOTE_ADDR'] . $secret);
    }
        
    /**
     * 错误
     *
     * @param string $message
     */
    protected function fail($message)
    {
        header("HTTP/1.1 406 Not Acceptable");
        echo $message;
        exit;
    }
        
    /**
     * 登录失败
     *
     * @param string $message
     */
    protected function failLogin($message)
    {
        header("HTTP/1.1 401 Unauthorized");
        echo $message;
        exit;
    }
        
}

/* End of file sso.php */
/* Location: ./application/controllers/sso.php */