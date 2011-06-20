<?php
/*
 * comments
 *
*/

class rememberme {

	function __construct() {
		$this->CI =& get_instance();
	}
	
	function setCookie($netid = "") {
		if (!$netid) {
			show_error("setCookie request missing netid");
			return;
		}
		
		$this->CI->load->library('user_agent');	
		
		$query = $this->CI->db->get_where('ci_cookies', array(
			'netid' => $netid
		));
			
		$cookie_id = uniqid('', true);			
		if (!$query->num_rows()) {
			
			$insertdata = array(
				'cookie_id' => $cookie_id,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $this->CI->agent->browser() . " " . $this->CI->agent->version() . " " . $this->CI->agent->platform(),
				'netid' => $netid,
				'created_at' => date('Y-m-d H:i:s')
			);

			$this->CI->db->insert('ci_cookies', $insertdata);					
		}	
		
		// set cookie for 1 year
		$cookie = array(
			'name' => 'rememberme_token',
			'value' => $cookie_id,
			'expire' => 31557600,
			'domain' => '.' . $_SERVER['SERVER_NAME'],
			'secure' => $_SERVER['HTTPS']
		);
		set_cookie($cookie);
		
		// establish session
		$this->CI->session->set_userdata('rememberme_session', $netid);
	}
	
	function verifyCookie() {											
		$query = $this->CI->db->get_where('ci_cookies', array(
			'cookie_id' => get_cookie('rememberme_token')			
		));
		if ($query->num_rows()) {
			$row = $query->row();
			
			// authorize user, if this option is set
			if ($this->CI->config->item('authfunc')) {
				if ($this->CI->config->item('requiremodel')) {
					$this->CI->load->model($this->CI->config->item('requiremodel'));
				}
				else if ($this->CI->config->item('requirelibrary')) {
					$this->CI->load->library($this->CI->config->item('requirelibrary'));
				}	

				$authorize = call_user_func($this->CI->config->item('authfunc'), $row->netid);
				
				if (!$authorize) {
					$this->CI->session->sess_destroy();
					delete_cookie('rememberme_token');
					return false;
				}
			}			
			
			// valid cookie
			if ($this->CI->session->userdata('rememberme_session')) {
				// session active, make sure cookie and session netids match
				if ($this->CI->session->userdata('rememberme_session') !== $row->netid) {
					return false;
				}
			}
			else {											
				// create new session
				$this->CI->session->set_userdata('rememberme_session', $row->netid);				
				
				// return netid		
				return $row->netid;				
			}					
		}
		else {
			return false;
		}
	}

}