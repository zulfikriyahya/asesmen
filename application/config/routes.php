<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['default_controller'] = 'auth'; //'install';
$route['login'] = 'Auth/index';
$route['logout'] = 'Auth/logout';
$route['404_override'] = 'auth';
$route['translate_uri_dashes'] = FALSE;
