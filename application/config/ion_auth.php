<?php

defined('BASEPATH') OR exit('No direct script access allowed');
$config['database_group_name'] = '';
$config['tables']['users']           = 'users';
$config['tables']['groups']          = 'groups';
$config['tables']['users_groups']    = 'users_groups';
$config['tables']['login_attempts']  = 'login_attempts';
$config['join']['users']  = 'user_id';
$config['join']['groups'] = 'group_id';
$config['hash_method']			    = 'bcrypt';	// bcrypt or argon2
$config['bcrypt_default_cost']		= 10;		// Set cost according to your server benchmark - but no lower than 10 (default PHP value)
$config['bcrypt_admin_cost']		= 12;		// Cost for user in admin group
$config['argon2_default_params']	= [
	'memory_cost'	=> 1 << 12,	// 4MB
	'time_cost'		=> 2,
	'threads'		=> 2
];
$config['argon2_admin_params']		= [
	'memory_cost'	=> 1 << 14,	// 16MB
	'time_cost'		=> 4,
	'threads'		=> 2
];
$config['site_title']                 = "Aplikasi Ujian Online";       // Site Title, example.com
$config['admin_email']                = "admin@example.com"; // Admin Email, admin@example.com
$config['default_group']              = 'guru';           // Default group, use name
$config['admin_group']                = 'admin';             // Default administrators group, use name
$config['identity']                   = 'username';             /* You can use any unique column in your table as identity column.															    		   update the UNIQUE constraint in your DB */
$config['min_password_length']        = 6;                   // Minimum Required Length of Password (not enforced by lib - see note above)
$config['email_activation']           = FALSE;               // Email Activation for registration
$config['manual_activation']          = FALSE;               // Manual Activation for registration
$config['remember_users']             = TRUE;                // Allow users to be remembered and enable auto-login
$config['user_expire']                = 86500;               // How long to remember the user (seconds). Set to zero for no expiration - see sess_expiration in CodeIgniter Session Config for session expiration
$config['user_extend_on_login']       = FALSE;               // Extend the users cookies every time they auto-login
$config['track_login_attempts']       = TRUE;                // Track the number of failed login attempts for each user or ip.
$config['track_login_ip_address']     = TRUE;                // Track login attempts by IP Address, if FALSE will track based on identity. (Default: TRUE)
$config['maximum_login_attempts']     = 3;                   // The maximum number of failed login attempts.
$config['lockout_time']               = 600;                 // The number of seconds to lockout an account due to exceeded attempts
$config['forgot_password_expiration'] = 1800;                // The number of seconds after which a forgot password request will expire. If set to 0, forgot password requests will not expire.
$config['recheck_timer']              = 0;                   // The number of seconds after which the session is checked again against database to see if the user still exists and is active.
$config['remember_cookie_name'] = 'remember_code';
$config['use_ci_email'] = FALSE; // Send Email using the builtin CI email class, if false it will return the code and the identity
$config['email_config'] = [
	'mailtype' => 'html',
];
$config['email_templates'] = 'auth/email/';
$config['email_activate'] = 'activate.tpl.php';
$config['email_forgot_password'] = 'forgot_password.tpl.php';
$config['delimiters_source']       = 'config'; 	// "config" = use the settings defined here, "form_validation" = use the settings defined in CI's form validation library
$config['message_start_delimiter'] = ''; 	// Message start delimiter
$config['message_end_delimiter']   = ''; 	// Message end delimiter
$config['error_start_delimiter']   = '';		// Error message start delimiter
$config['error_end_delimiter']     = '';	// Error message end delimiter
