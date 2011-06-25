Remember Me
===========

This CodeIgniter Spark provides a library for creating and verifying non-forgeable cookies suitable for "Remember Me?" type login checkboxes.

How Does It Work?
=================

Using this library's setCookie() function, the following occurs:

- A cookie is generated containing a unique hash
- This hash is recorded into a database table, called *ci_cookies* along with the netid/username passed on to setCookie()
- When the verifyCookie() function is called the browser's cookie hash must match the hash recorded to the database. If it does, verifyCookie() returns true
- Optionally, developers can provide an authorization check using their own function that will verify the status of the user when the user returns to your site, no session exists, and they have a valid cookie with a matching database entry. This way you can, for instance, include an LDAP check to make sure that this user is still employed by your company so that his/her cookie is destroyed when this check fails

Usage
=====

1. Create your *ci_cookies* table in the database used by your site using the included schema *ci_cookies.sql*
2. If you wish to provide your own authorization check to verify users that have valid cookies but no session information, you will need to inform RememberMe of the model or library where this function resides, as well as this functions's name. See the included sample *config/rememberme.php* for this configuration. If you wish to disable this feature, comment out the *authfunc* config option in this file.
3. Create your login form with a remember me checkbox, and in your application if this checkbox is checked execute the following (where *$this->input->post('rememberme')* is your form's remember me checkbox input):
	
		$this->rememberme->setCookie($this->input->post('rememberme'));

4. To verify the cookie, include code such as the following:

		$cookie_user = $this->rememberme->verifyCookie();
		if ($cookie_user) {
			// find user id of cookie_user stored in application database			
			$user = User::findUser($cookie_user);			
			// set session if necessary								
			if (!$this->session->userdata('user_id')) {							
				$this->session->set_userdata('user_id', $user);
			}
			$this->user = $user;
		}
		else if ($this->session->userdata('user_id')) {
			$this->user = $this->session->userdata('user_id');
		}
		

Note that *$this->rememberme->verifyCookie();* will return true if the cookie is valid (and, optionally, the user name associated with the cookie passes your custom verification function). The CodeIgniter Session class and Cookie Helper is required by Remember Me and autoloaded for storing the user ID for that session, it is recommended that you use the following configuration options found in your applications main *config/config.php* file:

	$config['sess_expire_on_close']	= TRUE;
	$config['sess_encrypt_cookie']	= TRUE;
	$config['sess_use_database']	= TRUE;
	$config['sess_table_name']		= 'ci_sessions';
	
The *sess_expire_on_close* setting will allow your local sessions to be regenerated at the beginning of each new session upon cookie verification, and the other settings provide some additional security (see the CodeIgniter user guide on creating your *ci_sessions* table). Remember Me will set its own session variable called *rememberme_session* tracking the netid/username of the current user using the CodeIgniter Session class for additional verification. You can use this as a means of tracking the current user within your application, or else supplement this with your own session data. In the above code fragment *$this->session->userdata('user_id')* is tracking the current user numerical ID stored in our database's *users* table (in this case the numerical ID differs from the netid/username), but you name this variable whatever you want, you don't have to stick to the naming schemes used within the above code fragment.


