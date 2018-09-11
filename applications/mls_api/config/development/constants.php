<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| User session key
|--------------------------------------------------------------------------
|
| Use this session key in each App local area.
|
*/
define('USER_SESSION_KEY', 'mls_api_user');


//MLS地址
define('MLS_URL', PROTOCOL . MLS_NAME);

//管理平台
define('MLS_ADMIN_URL', PROTOCOL . MLS_ADMIN_NAME);

//签约平台
define('MLS_SIGN_URL', PROTOCOL . MLS_SIGN_NAME);

//对外接口API
define('MLS_API_URL', PROTOCOL . MLS_API_NAME);

//JOBAPI
define('MLS_JOB_URL', PROTOCOL . MLS_JOB_NAME);

//移动端API
define('MLS_MOBILE_URL', PROTOCOL . MLS_MOBILE_NAME);

//静态资源URL
define('MLS_SOURCE_URL', PROTOCOL . MLS_SOURCE_NAME);

//文件上传URL
define('MLS_FILE_SERVER_URL', PROTOCOL . MLS_FILE_SERVER_NAME);

//金融端URL
define('MLS_FINANCE_URL', PROTOCOL . MLS_FINANCE_NAME);

//软件名称
define('SOFTWARE_NAME', '科地地产');
/* End of file constants.php */
/* Location: ./application/config/constants.php */
