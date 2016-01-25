<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Database Auth driver
 *
 * @package		Kohana/Auth
 * @author			kohei
 */
class Auth_Database extends Auth {

	/**
	 * Constructor loads the user list into the class.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/*
	 * Generate unique token
	 */
	public static function generate_unique_token()
	{
		// Set default token valid
		$token_valid = FALSE;

		while (!$token_valid)
		{
			// Create token
			$token = sha1(uniqid(Text::random('alnum', 32), TRUE));

			// Check if token is unique
			$token_valid = DB::select()
					->from('user_tokens')
					->where('token', '=', $token)
					->execute()
					->count() == 0;
		}

		return $token;
	}

	/**
	 * Checks if a session is active.
	 *
	 * @param   mixed    $role Role name string, or array with role names
	 * @return  boolean
	 */
	public function logged_in($role = NULL)
	{
		// Get the user from the session
		$user = $this->get_user();

		if ($user)
		{
			// If we don't have a roll no further checking is needed
			if (!$role) return TRUE;

			if (is_array($role))
			{
				// Get all the roles
				$roles = DB::select('roles.*')
					->from('roles_users')
					->join('roles')->on('roles_users.role_id', '=', 'roles.id')
					->where('roles_users.user_id', '=', $user->id)
					->where('name', 'IN', $role)
					->execute()
					->as_array(NULL, 'id');

				// Make sure all the roles are valid ones
				if (count($roles) !== count($role)) return FALSE;
			}
			elseif (is_string($role))
			{
				// Get all the roles
				$roles = DB::select('roles.*')
					->from('roles_users')
					->join('roles')->on('roles_users.role_id', '=', 'roles.id')
					->where('roles_users.user_id', '=', $user->id)
					->where('name', '=', $role)
					->execute()
					->as_array(NULL, 'id');

				if (count($roles) == 0) return FALSE;
			}
			else
			{
				return FALSE;
			}

			return DB::select('users.*', 'roles_users.role_id')
					->from('users')
					->join('roles_users')->on('users.id', '=', 'roles_users.user_id')
					->where('roles_users.role_id', 'IN', $roles)
					->execute()
					->count() > 0;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Logs a user in.
	 *
	 * @param 	string	 	username
	 * @param 	string   	password
	 * @param 	boolean	enable autologin (not supported)
	 * @return	boolean
	 */
	protected function _login($username, $password, $remember)
	{
		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password);
		}

		// Get user from username and password
		$result = DB::select('users.*', 'roles_users.role_id')
			->from('users')
			->join('roles_users')->on('users.id', '=', 'roles_users.user_id')
			->where('username', '=', $username)
			->where('password', '=', $password)
			->where('role_id', '=', 1) //login role
			->as_object()
			->execute();

		// If count is 1
		if ($result->count() === 1)
		{
			$user = $result->current();

			// Check password
			if ($user->password === $password)
			{
				if ((bool) $remember === TRUE)
				{
					$token = self::generate_unique_token();

					// Token data
					$data = array(
						'user_id' => $user->id,
						'expires' => time() + $this->_config['lifetime'],
						'user_agent' => sha1(Request::$user_agent),
						'token' => $token,
					);

					// Create a new user token
					DB::insert()
						->table('user_tokens')
						->columns(array_keys($data))
						->values(array_values($data))
						->execute();

					// Set the autologin cookie
					Cookie::set('authautologin', $token, $this->_config['lifetime']);
				}

				return $this->complete_login($user);
			}
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Attempt to log in a user by using an Database object and plain-text password.
	 *
	 * @param   string   $username  Username to log in
	 * @param   string   $password  Password to check against
	 * @param   boolean  $remember  Enable autologin
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE)
	{
		if (empty($password))
			return FALSE;

		// Todo:: kohx::1
		// もしブロックが１ならreturn FALSE
		$block_check = Tbl::factory('users')
			->where('username', '=', $username)
			->read('is_block');

		if($block_check)
		{
			return FALSE;
		}

		return $this->_login($username, $password, $remember);
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param 	mixed		username
	 * @return	boolean
	 */
	public function force_login($username)
	{
		$result = DB::select()
			->from('users')
			->where('username', '=', $username)
			->as_object()
			->execute();

		if ($result->count() === 1)
		{
			return $this->complete_login($result->current());
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param 	string		username
	 * @return	string
	 */
	public function password($username)
	{
		$result = DB::select()
			->from('users')
			->where('username', '=', $username)
			->execute();

		if ($result->count() === 1)
		{
			$user = $result->current();
			return (!$user->password) ? $user->password : FALSE;
		}

		return FALSE;
	}

	/**
	 * Complete the login for a user by incrementing the logins and setting
	 * session data: user_id, username, roles.
	 *
	 * @param	string		username
	 * @return 	void
	 */
	protected function complete_login($user)
	{
		DB::update('users')
			->set(array('logins' => $user->logins + 1))
			->set(array('last_login' => time()))
			->where('username', '=', $user->username)
			->execute();

		return parent::complete_login($user);
	}

	/**
	 * Compare password with original (plain text). Works for current (logged in) user
	 *
	 * @param	string		$password
	 * @return 	boolean
	 */
	public function check_password($password)
	{
		$user = $this->get_user();

		if (!$user) return FALSE;

		return ($this->hash($password) === $user->password);
	}

	/**
	 * Gets the currently logged in user from the session (with auto_login check).
	 * Returns FALSE if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user($default = NULL)
	{
		$user = parent::get_user($default);

		if (!$user)
		{
			// check for "remembered" login
			$user = $this->auto_login();
		}

		return $user;
	}

	/**
	 * Logs a user in, based on the authautologin cookie.
	 *
	 * @return  mixed
	 */
	public function auto_login()
	{
		// Get token from cookie
		$token = Cookie::get('authautologin');

		// If there is token
		if ($token)
		{
			// Get user_token
			$user_token = DB::select()
				->from('user_tokens')
				->where('token', '=', $token)
				->as_object()
				->execute()
				->current();

			// If there is user_token
			if ($user_token)
			{
				// Check user agent
				if ($user_token->user_agent === sha1(Request::$user_agent))
				{
					// Generate new token
					$token = self::generate_unique_token();

					// Update user tokens table
					DB::update()
						->table('user_tokens')
						->set(array('token' => $token))
						->where('id', '=', $user_token->id)
						->execute();

					// Set the new token
					Cookie::set('authautologin', $token, $user_token->expires - time());

					$user = DB::select()
						->from('users')
						->where('id', '=', $user_token->user_id)
						->as_object()
						->execute()
						->current();

					// Complete the login with the found data
					$this->complete_login($user);

					// Automatic login was successful
					return $user;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Log a user out and remove any autologin cookies.
	 *
	 * @param   boolean  completely destroy the session
	 * @param	boolean  remove all tokens for user
	 * @return  boolean
	 */
	public function logout($destroy = FALSE, $logout_all = FALSE)
	{
		if ($token = Cookie::get('authautologin'))
		{
			// Delete the autologin cookie to prevent re-login
			Cookie::delete('authautologin');

			// Clear the autologin token from the database
			$user_token = DB::select()
				->from('user_tokens')
				->where('token', '=', $token)
				->execute()
				->current();

			if ($user_token AND $logout_all)
			{
				DB::delete()
					->table('user_tokens')
					->where('user_id', '=', $user_token['user_id'])
					->execute();
			}
			elseif ($user_token)
			{
				DB::delete()
					->table('user_tokens')
					->where('id', '=', $user_token['id'])
					->execute();
			}
		}

		return parent::logout($destroy);
	}

}

// End Auth Database
