<?php defined('BASEPATH') OR exit('No direct script access allowed');
Class Session
{
	public $userdata = array();
	public function __construct($params = array())
	{
		if(session_id() == ''){
			session_start();
			ini_set('session.cookie_lifetime',7200);//session_id 有效期
		}
		$this->userdata = $_SESSION;
	}

	function userdata($item)
	{
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	array
	 */
	function all_userdata()
	{
		return $this->userdata;
	}

	// --------------------------------------------------------------------

	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$_SESSION[$key] = $val;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($_SESSION[$key]);
			}
		}

	}
	function sess_write()
	{
		// set the custom userdata, the session data we will set in a second
		$custom_userdata = $this->userdata;
		foreach($this->userdata as $k=>$v)
		{
			$_SESSION[$k] = $v;
		}		
		
	}
	function sess_destroy()
	{
		session_destroy();
	}
}
